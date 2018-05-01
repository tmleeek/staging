<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Amazon_Synchronization_Orders_Update
    extends Ess_M2ePro_Model_Amazon_Synchronization_Orders_Abstract
{
    //########################################

    protected function getNick()
    {
        return '/update/';
    }

    protected function getTitle()
    {
        return 'Update';
    }

    // ---------------------------------------

    protected function getPercentsStart()
    {
        return 0;
    }

    protected function getPercentsEnd()
    {
        return 100;
    }

    //########################################

    protected function performActions()
    {
        $this->deleteNotActualChanges();

        $permittedAccounts = $this->getPermittedAccounts();
        if (empty($permittedAccounts)) {
            return;
        }

        $iteration = 0;
        $percentsForOneStep = $this->getPercentsInterval() / count($permittedAccounts);

        foreach ($permittedAccounts as $account) {

            /** @var Ess_M2ePro_Model_Account $account */

            // ---------------------------------------
            $this->getActualOperationHistory()->addText('Starting Account "'.$account->getTitle().'"');
            // M2ePro_TRANSLATIONS
            // The "Update" Action for Amazon Account: "%account_title%" is started. Please wait...
            $status = 'The "Update" Action for Amazon Account: "%account_title%" is started. Please wait...';
            $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));
            // ---------------------------------------

            // ---------------------------------------
            $this->getActualOperationHistory()->addTimePoint(
                __METHOD__.'process'.$account->getId(),
                'Process Account '.$account->getTitle()
            );
            // ---------------------------------------

            try {

                $this->processAccount($account);

            } catch (Exception $exception) {

                $message = Mage::helper('M2ePro')->__(
                    'The "Update" Action for Amazon Account "%account%" was completed with error.',
                    $account->getTitle()
                );

                $this->processTaskAccountException($message, __FILE__, __LINE__);
                $this->processTaskException($exception);
            }

            // ---------------------------------------
            $this->getActualOperationHistory()->saveTimePoint(__METHOD__.'process'.$account->getId());
            // ---------------------------------------

            // ---------------------------------------
            // M2ePro_TRANSLATIONS
            // The "Update" Action for Amazon Account: "%account_title%" is finished. Please wait...
            $status = 'The "Update" Action for Amazon Account: "%account_title%" is finished. Please wait...';
            $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));
            $this->getActualLockItem()->setPercents($this->getPercentsStart() + $iteration * $percentsForOneStep);
            $this->getActualLockItem()->activate();
            // ---------------------------------------

            $iteration++;
        }
    }

    //########################################

    private function getPermittedAccounts()
    {
        /** @var $accountsCollection Mage_Core_Model_Mysql4_Collection_Abstract */
        $accountsCollection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Account');
        return $accountsCollection->getItems();
    }

    // ---------------------------------------

    private function processAccount(Ess_M2ePro_Model_Account $account)
    {
        $relatedChanges = $this->getRelatedChanges($account);
        if (empty($relatedChanges)) {
            return;
        }

        Mage::getResourceModel('M2ePro/Order_Change')->incrementAttemptCount(array_keys($relatedChanges));

        /** @var $dispatcherObject Ess_M2ePro_Model_Amazon_Connector_Dispatcher */
        $dispatcherObject = Mage::getModel('M2ePro/Amazon_Connector_Dispatcher');

        foreach ($relatedChanges as $change) {
            $changeParams = $change->getParams();

            $connectorData = array(
                'order_id'         => $change->getOrderId(),
                'change_id'        => $change->getId(),
                'amazon_order_id'  => $changeParams['amazon_order_id'],
                'tracking_number'  => $changeParams['tracking_number'],
                'carrier_name'     => $changeParams['carrier_name'],
                'fulfillment_date' => $changeParams['fulfillment_date'],
                'shipping_method'  => isset($changeParams['shipping_method']) ? $changeParams['shipping_method'] : null,
                'items'            => $changeParams['items']
            );

            $connectorObj = $dispatcherObject->getCustomConnector(
                'Amazon_Synchronization_Orders_Update_Requester',
                array('order' => $connectorData), $account
            );
            $dispatcherObject->process($connectorObj);
        }
    }

    //########################################

    private function getRelatedChanges(Ess_M2ePro_Model_Account $account)
    {
        $changesCollection = Mage::getModel('M2ePro/Order_Change')->getCollection();
        $changesCollection->addAccountFilter($account->getId());
        $changesCollection->addProcessingAttemptDateFilter();
        $changesCollection->addFieldToFilter('component', Ess_M2ePro_Helper_Component_Amazon::NICK);
        $changesCollection->addFieldToFilter('action', Ess_M2ePro_Model_Order_Change::ACTION_UPDATE_SHIPPING);
        $changesCollection->getSelect()->group(array('order_id'));

        return $changesCollection->getItems();
    }

    // ---------------------------------------

    private function deleteNotActualChanges()
    {
        Mage::getResourceModel('M2ePro/Order_Change')
            ->deleteByProcessingAttemptCount(
                Ess_M2ePro_Model_Order_Change::MAX_ALLOWED_PROCESSING_ATTEMPTS,
                Ess_M2ePro_Helper_Component_Amazon::NICK
            );
    }

    //########################################
}
