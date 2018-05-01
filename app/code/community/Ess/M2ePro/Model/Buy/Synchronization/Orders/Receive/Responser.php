<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Buy_Synchronization_Orders_Receive_Responser
    extends Ess_M2ePro_Model_Buy_Connector_Orders_Get_ItemsResponser
{
    /** @var Ess_M2ePro_Model_Synchronization_Log $synchronizationLog */
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

        if ($this->getResponse()->getMessages()->hasErrorEntities()) {
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

    protected function processResponseData()
    {
        try {

            $buyOrders = $this->processBuyOrders();
            if (empty($buyOrders)) {
                return;
            }

            $this->createMagentoOrders($buyOrders);

        } catch (Exception $exception) {

            $this->getSynchronizationLog()->addMessage(
                Mage::helper('M2ePro')->__($exception->getMessage()),
                Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH
            );

            Mage::helper('M2ePro/Module_Exception')->process($exception);
        }
    }

    // ---------------------------------------

    private function processBuyOrders()
    {
        $orders = array();
        $account = $this->getAccount()->getChildObject();

        $responseData = $this->getPreparedResponseData();

        $ordersLastSynchronization = $account->getData('orders_last_synchronization');
        if (strtotime($responseData['latest_creation_time']) > strtotime($ordersLastSynchronization)) {
            $ordersLastSynchronization = $responseData['latest_creation_time'];
        }

        foreach ($responseData['orders'] as $orderData) {
            /** @var $orderBuilder Ess_M2ePro_Model_Buy_Order_Builder */
            $orderBuilder = Mage::getModel('M2ePro/Buy_Order_Builder');
            $orderBuilder->initialize($this->getAccount(), $orderData);

            $order = $orderBuilder->process();

            $orders[] = $order;
        }

        if ($account->getData('orders_last_synchronization') != $ordersLastSynchronization) {
            $account->setData('orders_last_synchronization', $ordersLastSynchronization)->save();
        }

        return $orders;
    }

    private function createMagentoOrders($buyOrders)
    {
        foreach ($buyOrders as $order) {
            /** @var $order Ess_M2ePro_Model_Order */

            if ($this->isOrderChangedInParallelProcess($order)) {
                continue;
            }

            if ($order->canCreateMagentoOrder()) {
                try {
                    $order->createMagentoOrder();
                } catch (Exception $exception) {
                    continue;
                }
            }

            if ($order->getChildObject()->canCreateInvoice()) {
                $order->createInvoice();
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

    /**
     * @return Ess_M2ePro_Model_Account
     */
    protected function getAccount()
    {
        return $this->getObjectByParam('Account','account_id');
    }

    // ---------------------------------------

    protected function getSynchronizationLog()
    {
        if (!is_null($this->synchronizationLog)) {
            return $this->synchronizationLog;
        }

        $this->synchronizationLog = Mage::getModel('M2ePro/Synchronization_Log');
        $this->synchronizationLog->setComponentMode(Ess_M2ePro_Helper_Component_Buy::NICK);
        $this->synchronizationLog->setSynchronizationTask(Ess_M2ePro_Model_Synchronization_Log::TASK_ORDERS);

        return $this->synchronizationLog;
    }

    //########################################
}