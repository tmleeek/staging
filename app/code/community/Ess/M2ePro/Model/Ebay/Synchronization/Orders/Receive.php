<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Ebay_Synchronization_Orders_Receive
    extends Ess_M2ePro_Model_Ebay_Synchronization_Orders_Abstract
{
    //########################################

    /**
     * @return string
     */
    protected function getNick()
    {
        return '/receive/';
    }

    /**
     * @return string
     */
    protected function getTitle()
    {
        return 'Orders Receive';
    }

    // ---------------------------------------

    /**
     * @return int
     */
    protected function getPercentsStart()
    {
        return 0;
    }

    /**
     * @return int
     */
    protected function getPercentsEnd()
    {
        return 100;
    }

    //########################################

    protected function performActions()
    {
        $permittedAccounts = $this->getPermittedAccounts();
        if (empty($permittedAccounts)) {
            return;
        }

        $iteration = 1;
        $percentsForOneAcc = $this->getPercentsInterval() / count($permittedAccounts);

        foreach ($permittedAccounts as $account) {
            /** @var $account Ess_M2ePro_Model_Account **/

            $this->getActualOperationHistory()->addText('Starting Account "'.$account->getTitle().'"');
            $this->getActualOperationHistory()->addTimePoint(__METHOD__.'get'.$account->getId(),'Get Orders from eBay');

            // M2ePro_TRANSLATIONS
            // The "Receive" Action for eBay Account "%account_title%" is in data receiving state...
            $status = 'The "Receive" Action for eBay Account "%account_title%" is in data receiving state...';
            $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));
            // ---------------------------------------

            try {

                $ebayOrders = $this->processEbayOrders($account);

                $this->getActualLockItem()->setPercents(
                    $this->getPercentsStart() + $iteration * $percentsForOneAcc * 0.3
                );

                $this->getActualOperationHistory()->saveTimePoint(__METHOD__.'get'.$account->getId());
                $this->getActualOperationHistory()->addTimePoint(
                    __METHOD__.'create_magento_orders'.$account->getId(),
                    'Create Magento Orders'
                );

                // M2ePro_TRANSLATIONS
                // The "Receive" Action for eBay Account "%account_title%" is in Order Creation state...
                $status = 'The "Receive" Action for eBay Account "%account_title%" is in Order Creation state...';
                $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));
                // ---------------------------------------

                if (count($ebayOrders) > 0) {
                    $percentsForOneOrder = (int)(($this->getPercentsStart() + $iteration * $percentsForOneAcc * 0.7)
                        / count($ebayOrders));

                    $this->createMagentoOrders($ebayOrders, $percentsForOneOrder);
                }

            } catch (Exception $exception) {

                $message = Mage::helper('M2ePro')->__(
                    'The "Receive" Action for eBay Account "%account%" was completed with error.',
                    $account->getTitle()
                );

                $this->processTaskAccountException($message, __FILE__, __LINE__);
                $this->processTaskException($exception);
            }

            // ---------------------------------------
            $this->getActualOperationHistory()->saveTimePoint(__METHOD__.'create_magento_orders'.$account->getId());

            $this->getActualLockItem()->setPercents($this->getPercentsStart() + $iteration * $percentsForOneAcc);
            $this->getActualLockItem()->activate();
            // ---------------------------------------

            $iteration++;
        }
    }

    //########################################

    private function getPermittedAccounts()
    {
        /** @var $accountsCollection Mage_Core_Model_Mysql4_Collection_Abstract */
        $accountsCollection = Mage::helper('M2ePro/Component_Ebay')->getCollection('Account');
        return $accountsCollection->getItems();
    }

    // ---------------------------------------

    private function processEbayOrders($account)
    {
        $fromTime = $this->prepareFromTime($account);
        $toTime   = $this->prepareToTime();

        if (strtotime($fromTime) >= strtotime($toTime)) {
            $fromTime = new DateTime($toTime);
            $fromTime->modify('- 5 minutes');

            $fromTime = Ess_M2ePro_Model_Ebay_Connector_Command_RealTime::ebayTimeToString($fromTime);
        }

        $params = array(
            'from_update_date' => $fromTime,
            'to_update_date'=> $toTime
        );

        $jobToken = $account->getData('job_token');
        if (!empty($jobToken)) {
            $params['job_token'] = $jobToken;
        }

        /** @var Ess_M2ePro_Model_Connector_Command_RealTime $connectorObj */
        $dispatcherObj = Mage::getModel('M2ePro/Ebay_Connector_Dispatcher');
        $connectorObj = $dispatcherObj->getCustomConnector(
            'Ebay_Connector_Order_Receive_Items', $params, NULL, $account
        );

        $dispatcherObj->process($connectorObj);
        $response = $connectorObj->getResponseData();

        $this->processResponseMessages($connectorObj->getResponseMessages());

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__.'get'.$account->getId());

        if (!isset($response['items']) || !isset($response['to_update_date'])) {
            return array();
        }

        $accountCreateDate = new DateTime($account->getData('create_date'), new DateTimeZone('UTC'));

        $orders = array();

        foreach ($response['items'] as $ebayOrderData) {

            $orderCreateDate = new DateTime($ebayOrderData['purchase_create_date'], new DateTimeZone('UTC'));
            if ($orderCreateDate < $accountCreateDate) {
                continue;
            }

            /** @var $ebayOrder Ess_M2ePro_Model_Ebay_Order_Builder */
            $ebayOrder = Mage::getModel('M2ePro/Ebay_Order_Builder');
            $ebayOrder->initialize($account, $ebayOrderData);

            $orders[] = $ebayOrder->process();
        }

        /** @var Ess_M2ePro_Model_Ebay_Account $ebayAccount */
        $ebayAccount = $account->getChildObject();

        if (!empty($response['job_token'])) {
            $ebayAccount->setData('job_token', $response['job_token']);
        } else {
            $ebayAccount->setData('job_token', NULL);
        }

        $ebayAccount->setData('orders_last_synchronization', $response['to_update_date']);
        $ebayAccount->save();

        return array_filter($orders);
    }

    private function processResponseMessages(array $messages)
    {
        /** @var Ess_M2ePro_Model_Connector_Connection_Response_Message_Set $messagesSet */
        $messagesSet = Mage::getModel('M2ePro/Connector_Connection_Response_Message_Set');
        $messagesSet->init($messages);

        foreach ($messagesSet->getEntities() as $message) {

            if (!$message->isError() && !$message->isWarning()) {
                continue;
            }

            $logType = $message->isError() ? Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR
                : Ess_M2ePro_Model_Log_Abstract::TYPE_WARNING;

            $this->getLog()->addMessage(
                Mage::helper('M2ePro')->__($message->getText()),
                $logType,
                Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH
            );
        }
    }

    private function createMagentoOrders($ebayOrders, $percentsForOneOrder)
    {
        $iteration = 1;
        $currentPercents = $this->getActualLockItem()->getPercents();

        foreach ($ebayOrders as $order) {
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

            if ($order->getReserve()->isNotProcessed() && $order->isReservable()) {
                $order->getReserve()->place();
            }

            if ($order->getChildObject()->canCreatePaymentTransaction()) {
                $order->getChildObject()->createPaymentTransactions();
            }
            if ($order->getChildObject()->canCreateInvoice()) {
                $order->createInvoice();
            }
            if ($order->getChildObject()->canCreateShipment()) {
                $order->createShipment();
            }
            if ($order->getChildObject()->canCreateTracks()) {
                $order->getChildObject()->createTracks();
            }
            if ($order->getStatusUpdateRequired()) {
                $order->updateMagentoOrderStatus();
            }

            $currentPercents = $currentPercents + $percentsForOneOrder * $iteration;
            $this->getActualLockItem()->setPercents($currentPercents);

            if ($iteration % 5 == 0) {
                $this->getActualLockItem()->activate();
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

    private function prepareFromTime(Ess_M2ePro_Model_Account $account)
    {
        $lastSynchronizationDate = $account->getData('orders_last_synchronization');

        if (is_null($lastSynchronizationDate)) {
            $sinceTime = new DateTime('now', new DateTimeZone('UTC'));
            $sinceTime = Ess_M2ePro_Model_Ebay_Connector_Command_RealTime::ebayTimeToString($sinceTime);

            /** @var Ess_M2ePro_Model_Ebay_Account $ebayAccount */
            $ebayAccount = $account->getChildObject();
            $ebayAccount->setData('orders_last_synchronization', $sinceTime)->save();

            return $sinceTime;
        }

        $sinceTime = new DateTime($lastSynchronizationDate, new DateTimeZone('UTC'));

        // Get min date for synch
        // ---------------------------------------
        $minDate = new DateTime('now',new DateTimeZone('UTC'));
        $minDate->modify('-90 days');
        // ---------------------------------------

        // Prepare last date
        // ---------------------------------------
        if ((int)$sinceTime->format('U') < (int)$minDate->format('U')) {
            $sinceTime = $minDate;
        }
        // ---------------------------------------

        return Ess_M2ePro_Model_Ebay_Connector_Command_RealTime::ebayTimeToString($sinceTime);
    }

    private function prepareToTime()
    {
        $operationHistory = $this->getActualOperationHistory()->getParentObject('synchronization');
        if (!is_null($operationHistory)) {
            $toTime = $operationHistory->getData('start_date');
        } else {
            $toTime = new DateTime('now', new DateTimeZone('UTC'));
        }

        return Ess_M2ePro_Model_Ebay_Connector_Command_RealTime::ebayTimeToString($toTime);
    }

    //########################################
}