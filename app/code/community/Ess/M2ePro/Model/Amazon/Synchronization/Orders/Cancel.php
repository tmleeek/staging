<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Amazon_Synchronization_Orders_Cancel
    extends Ess_M2ePro_Model_Amazon_Synchronization_Orders_Abstract
{
    //########################################

    protected function getNick()
    {
        return '/cancel/';
    }

    protected function getTitle()
    {
        return 'Cancel';
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
            $this->getActualOperationHistory()->addText('Starting account "'.$account->getTitle().'"');
            // M2ePro_TRANSLATIONS
            // The "Cancel" action for Amazon account: "%account_title%" is started. Please wait...
            $status = 'The "Cancel" action for Amazon account: "%account_title%" is started. Please wait...';
            $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));
            // ---------------------------------------

            // ---------------------------------------
            $this->getActualOperationHistory()->addTimePoint(
                __METHOD__.'process'.$account->getId(),
                'Process account '.$account->getTitle()
            );
            // ---------------------------------------

            try {

                $this->processAccount($account);

            } catch (Exception $exception) {

                $message = Mage::helper('M2ePro')->__(
                    'The "Cancel" Action for Amazon Account "%account%" was completed with error.',
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
            // The "Cancel" action for Amazon account: "%account_title%" is finished. Please wait...
            $status = 'The "Cancel" action for Amazon account: "%account_title%" is finished. Please wait...';
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

        $failedChangesIds = array();

        foreach ($relatedChanges as $change) {
            $changeParams = $change->getParams();

            /** @var Ess_M2ePro_Model_Order $order */
            $order = Mage::helper('M2ePro/Component_Amazon')->getObject('Order', $change->getOrderId());

            /** @var Ess_M2ePro_Model_Amazon_Order $amazonOrder */
            $amazonOrder = $order->getChildObject();

            if (!$amazonOrder->canRefund()) {
                $failedChangesIds[] = $change->getId();
                continue;
            }

            $connectorData = array(
                'order_id'  => $change->getOrderId(),
                'change_id' => $change->getId(),
                'amazon_order_id' => $changeParams['order_id'],
            );

            $connectorObj = $dispatcherObject->getCustomConnector(
                'Amazon_Synchronization_Orders_Cancel_Requester',
                array('order' => $connectorData), $account
            );
            $dispatcherObject->process($connectorObj);
        }

        if (!empty($failedChangesIds)) {
            Mage::getResourceModel('M2ePro/Order_Change')->deleteByIds($failedChangesIds);
        }
    }

    //########################################

    /**
     * @param Ess_M2ePro_Model_Account $account
     * @return Ess_M2ePro_Model_Order_Change[]
     */
    private function getRelatedChanges(Ess_M2ePro_Model_Account $account)
    {
        /** @var Ess_M2ePro_Model_Mysql4_Order_Change_Collection $changesCollection */
        $changesCollection = Mage::getModel('M2ePro/Order_Change')->getCollection();
        $changesCollection->addAccountFilter($account->getId());
        $changesCollection->addProcessingAttemptDateFilter(10);
        $changesCollection->addFieldToFilter('component', Ess_M2ePro_Helper_Component_Amazon::NICK);
        $changesCollection->addFieldToFilter('action', Ess_M2ePro_Model_Order_Change::ACTION_CANCEL);
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
