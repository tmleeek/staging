<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Amazon_Synchronization_ListingsProducts_Update_Blocked
    extends Ess_M2ePro_Model_Amazon_Synchronization_ListingsProducts_Abstract
{
    //########################################

    protected function getNick()
    {
        return '/update/blocked/';
    }

    protected function getTitle()
    {
        return 'Update Blocked Listings Products';
    }

    // ---------------------------------------

    protected function getPercentsStart()
    {
        return 25;
    }

    protected function getPercentsEnd()
    {
        return 50;
    }

    // ---------------------------------------

    protected function intervalIsEnabled()
    {
        return true;
    }

    protected function intervalIsLocked()
    {
        if ($this->getInitiator() == Ess_M2ePro_Helper_Data::INITIATOR_USER ||
            $this->getInitiator() == Ess_M2ePro_Helper_Data::INITIATOR_DEVELOPER) {
            return false;
        }

        return parent::intervalIsLocked();
    }

    //########################################

    protected function performActions()
    {
        $accounts = Mage::helper('M2ePro/Component_Amazon')->getCollection('Account')->getItems();

        if (count($accounts) <= 0) {
            return;
        }

        $iteration = 0;
        $percentsForOneStep = $this->getPercentsInterval() / count($accounts);

        foreach ($accounts as $account) {

            /** @var $account Ess_M2ePro_Model_Account **/

            $this->getActualOperationHistory()->addText('Starting Account "'.$account->getTitle().'"');
            // M2ePro_TRANSLATIONS
            // The "Update Blocked Listings Products" Action for Amazon Account: "%account_title%" is started. Please wait...
            $status = 'The "Update Blocked Listings Products" Action for Amazon Account: ';
            $status .= '"%account_title%" is started. ';
            $status .= 'Please wait...';
            $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));

            if (!$this->isLockedAccount($account) && !$this->isLockedAccountInterval($account)) {

                $this->getActualOperationHistory()->addTimePoint(
                    __METHOD__.'process'.$account->getId(),
                    'Process Account '.$account->getTitle()
                );

                try {

                    $this->processAccount($account);

                } catch (Exception $exception) {

                    // M2ePro_TRANSLATIONS
                    // The "Update Blocked Listings Products" Action for Amazon Account: "%account%" was completed with error.
                    $message = 'The "Update Blocked Listings Products" Action for Amazon Account "%account%"';
                    $message .= ' was completed with error.';
                    $message = Mage::helper('M2ePro')->__($message, $account->getTitle());

                    $this->processTaskAccountException($message, __FILE__, __LINE__);
                    $this->processTaskException($exception);
                }

                $this->getActualOperationHistory()->saveTimePoint(__METHOD__.'process'.$account->getId());
            }

            // M2ePro_TRANSLATIONS
            // The "Update Listings Products" Action for Amazon Account: "%account_title%" is finished. Please wait...
            $status = 'The "Update Blocked Listings Products" Action for Amazon Account: ';
            $status .= '"%account_title%" is finished. ';
            $status .= 'Please wait...';
            $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));
            $this->getActualLockItem()->setPercents($this->getPercentsStart() + $iteration * $percentsForOneStep);
            $this->getActualLockItem()->activate();

            $iteration++;
        }
    }

    //########################################

    private function processAccount(Ess_M2ePro_Model_Account $account)
    {
        /** @var $collection Mage_Core_Model_Mysql4_Collection_Abstract */
        $collection = Mage::getModel('M2ePro/Listing')->getCollection();
        $collection->addFieldToFilter('component_mode',Ess_M2ePro_Helper_Component_Amazon::NICK);
        $collection->addFieldToFilter('account_id',(int)$account->getId());

        if ($collection->getSize()) {

            $dispatcherObject = Mage::getModel('M2ePro/Amazon_Connector_Dispatcher');
            $connectorObj = $dispatcherObject->getCustomConnector(
                'Amazon_Synchronization_ListingsProducts_Update_Blocked_Requester', array(), $account
            );
            $dispatcherObject->process($connectorObj);
        }
    }

    private function isLockedAccount(Ess_M2ePro_Model_Account $account)
    {
        /** @var $lockItem Ess_M2ePro_Model_Lock_Item_Manager */
        $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
        $lockItem->setNick(
            Ess_M2ePro_Model_Amazon_Synchronization_ListingsProducts_Update_Blocked_ProcessingRunner::LOCK_ITEM_PREFIX
            .'_'.$account->getId()
        );
        $lockItem->setMaxInactiveTime(Ess_M2ePro_Model_Processing_Runner::MAX_LIFETIME);

        return $lockItem->isExist();
    }

    private function isLockedAccountInterval(Ess_M2ePro_Model_Account $account)
    {
        if ($this->getInitiator() == Ess_M2ePro_Helper_Data::INITIATOR_USER ||
            $this->getInitiator() == Ess_M2ePro_Helper_Data::INITIATOR_DEVELOPER) {
            return false;
        }

        $additionalData = Mage::helper('M2ePro')->jsonDecode($account->getAdditionalData());
        if (!empty($additionalData['last_listing_products_synchronization'])) {
            return (strtotime($additionalData['last_listing_products_synchronization'])
                   + 86400) > Mage::helper('M2ePro')->getCurrentGmtDate(true);
        }

        return false;
    }

    //########################################
}