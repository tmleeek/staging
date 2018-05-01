<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Amazon_Synchronization_Orders_Receive_Responser
    extends Ess_M2ePro_Model_Amazon_Connector_Orders_Get_ItemsResponser
{
    protected $synchronizationLog = NULL;

    //########################################

    protected function processResponseMessages(array $messages = array())
    {
        parent::processResponseMessages();

        foreach ($this->getResponse()->getMessages()->getEntities() as $message) {

            if (!$message->isError() && !$message->isWarning()) {
                continue;
            }

            $logType = $message->isError() ? Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR
                : Ess_M2ePro_Model_Log_Abstract::TYPE_WARNING;

            $this->getSynchronizationLog()->addMessage(
                Mage::helper('M2ePro')->__($message->getText()),
                $logType,
                Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH
            );
        }
    }

    protected function isNeedProcessResponse()
    {
        if (!parent::isNeedProcessResponse()) {
            return false;
        }

        $responseData = $this->getResponse()->getData();
        if ($this->getResponse()->getMessages()->hasErrorEntities() && !isset($responseData['items'])) {
            return false;
        }

        return true;
    }

    //########################################

    public function failDetected($messageText)
    {
        parent::failDetected($messageText);

        $this->getSynchronizationLog()->addMessage(
            Mage::helper('M2ePro')->__($messageText),
            Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
            Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH
        );
    }

    //########################################

    protected function processResponseData()
    {
        $accounts = $this->getAccountsByAccessTokens();
        $preparedResponseData = $this->getPreparedResponseData();

        $processedAmazonOrders = array();

        foreach ($preparedResponseData['orders'] as $accountAccessToken => $ordersData) {

            $amazonOrders = $this->processAmazonOrders($ordersData, $accounts[$accountAccessToken]);

            if (empty($amazonOrders)) {
                continue;
            }

            $processedAmazonOrders[] = $amazonOrders;
        }

        $merchantId = current($accounts)->getChildObject()->getMerchantId();

        if (!empty($preparedResponseData['job_token'])) {
            Mage::getSingleton('M2ePro/Config_Synchronization')->setGroupValue(
                "/amazon/orders/receive/{$merchantId}/", "job_token", $preparedResponseData['job_token']
            );
        } else {
            Mage::getSingleton('M2ePro/Config_Synchronization')->deleteGroupValue(
                "/amazon/orders/receive/{$merchantId}/", "job_token"
            );
        }

        Mage::getSingleton('M2ePro/Config_Synchronization')->setGroupValue(
            "/amazon/orders/receive/{$merchantId}/", "from_update_date", $preparedResponseData['to_update_date']
        );

        foreach ($processedAmazonOrders as $amazonOrders) {
            try {

                $this->createMagentoOrders($amazonOrders);

            } catch (Exception $exception) {

                $this->getSynchronizationLog()->addMessage(
                    Mage::helper('M2ePro')->__($exception->getMessage()),
                    Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                    Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH
                );

                Mage::helper('M2ePro/Module_Exception')->process($exception);
            }
        }
    }

    // ---------------------------------------

    private function processAmazonOrders(array $ordersData, Ess_M2ePro_Model_Account $account)
    {
        $accountCreateDate = new DateTime($account->getData('create_date'), new DateTimeZone('UTC'));

        $orders = array();

        foreach ($ordersData as $orderData) {

            $orderCreateDate = new DateTime($orderData['purchase_create_date'], new DateTimeZone('UTC'));
            if ($orderCreateDate < $accountCreateDate) {
                continue;
            }

            /** @var $orderBuilder Ess_M2ePro_Model_Amazon_Order_Builder */
            $orderBuilder = Mage::getModel('M2ePro/Amazon_Order_Builder');
            $orderBuilder->initialize($account, $orderData);

            $order = $orderBuilder->process();

            if (!$order) {
                continue;
            }

            $orders[] = $order;
        }

        return $orders;
    }

    private function createMagentoOrders($amazonOrders)
    {
        foreach ($amazonOrders as $order) {
            /** @var $order Ess_M2ePro_Model_Order */

            if ($this->isOrderChangedInParallelProcess($order)) {
                continue;
            }

            $order->getLog()->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_EXTENSION);

            if ($order->canCreateMagentoOrder()) {
                try {
                    $order->createMagentoOrder();
                } catch (Exception $exception) {
                    continue;
                }
            }

            if ($order->getReserve()->isNotProcessed() && $order->isReservable()) {
                $order->getReserve()->place();
            }

            if ($order->getChildObject()->canCreateInvoice()) {
                $order->createInvoice();
            }
            if ($order->getChildObject()->canCreateShipment()) {
                $order->createShipment();
            }
            if ($order->getStatusUpdateRequired()) {
                $order->updateMagentoOrderStatus();
            }
        }
    }

    /**
     * This is going to protect from Magento Orders duplicates.
     * (Is assuming that there may be a parallel process that has already created Magento Order)
     *
     * But this protection is not covering a cases when two parallel cron processes are isolated by mysql transactions
     */
    private function isOrderChangedInParallelProcess(Ess_M2ePro_Model_Order $order)
    {
        /** @var Ess_M2ePro_Model_Order $dbOrder */
        $dbOrder = Mage::getModel('M2ePro/Order')->load($order->getId());

        if ($dbOrder->getMagentoOrderId() != $order->getMagentoOrderId()) {
            return true;
        }

        return false;
    }

    //########################################

    private function getSynchronizationLog()
    {
        if (!is_null($this->synchronizationLog)) {
            return $this->synchronizationLog;
        }

        $this->synchronizationLog = Mage::getModel('M2ePro/Synchronization_Log');
        $this->synchronizationLog->setComponentMode(Ess_M2ePro_Helper_Component_Amazon::NICK);
        $this->synchronizationLog->setSynchronizationTask(Ess_M2ePro_Model_Synchronization_Log::TASK_ORDERS);

        return $this->synchronizationLog;
    }

    //########################################
}