<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Ebay_Synchronization_General_AccountPickupStore_Update
    extends Ess_M2ePro_Model_Ebay_Synchronization_General_Abstract
{
    const MAX_ITEMS_COUNT = 10000;

    //########################################

    protected function getNick()
    {
        return '/account_pickup_store/update/';
    }

    protected function getTitle()
    {
        return 'Pickup Store Update';
    }

    // ---------------------------------------

    protected function getPercentsStart()
    {
        return 60;
    }

    protected function getPercentsEnd()
    {
        return 100;
    }

    //########################################

    public function performActions()
    {
        $account = Mage::helper('M2ePro/Component_Ebay_PickupStore')->getEnabledAccount();
        if (!$account) {
            return;
        }

        $this->getActualOperationHistory()->addText('Starting Account "'.$account->getTitle().'"');
        // M2ePro_TRANSLATIONS
        // The "Synchronize Data" Action for eBay Account: "%account_title%" is started. Please wait...
        $status = 'The "Synchronize Data" Action for eBay Account: "%account_title%" is started. ';
        $status .= 'Please wait...';
        $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));

        $this->getActualOperationHistory()->addTimePoint(
            __METHOD__.'process'.$account->getId(),
            'Process Account '.$account->getTitle()
        );

        $this->processAccount($account);

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__.'process'.$account->getId());

        // M2ePro_TRANSLATIONS
        // The "Synchronize Data" Action for eBay Account: "%account_title%" is finished. Please wait...
        $status = 'The "Synchronize Data" Action for eBay Account: "%account_title%" is finished.'.
            ' Please wait...';
        $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));
        $this->getActualLockItem()->activate();
    }

    //########################################

    private function processAccount(Ess_M2ePro_Model_Account $account)
    {
        $collection = Mage::getResourceModel('M2ePro/Ebay_Account_PickupStore_State_Collection');
        $collection->getSelect()->where('(is_deleted = 1) OR (target_qty != online_qty)');
        $collection->addFieldToFilter('is_in_processing', 0);

        $collection->getSelect()->joinLeft(
            array('eaps' => Mage::getResourceModel('M2ePro/Ebay_Account_PickupStore')->getMainTable()),
            'eaps.id = main_table.account_pickup_store_id',
            array('account_id')
        );

        $collection->addFieldToFilter('eaps.account_id', $account->getId());

        $collection->getSelect()->limit(self::MAX_ITEMS_COUNT);

        $pickupStoreStateItems = $collection->getItems();
        if (empty($pickupStoreStateItems)) {
            return;
        }

        $dispatcher = Mage::getModel('M2ePro/Ebay_Connector_Dispatcher');

        /** @var Ess_M2ePro_Model_Ebay_Connector_AccountPickupStore_Synchronize_ProductsRequester $connector */
        $connector = $dispatcher->getConnector(
            'accountPickupStore', 'synchronize', 'productsRequester', array(), NULL, $account
        );
        $connector->setPickupStoreStateItems($pickupStoreStateItems);
        $dispatcher->process($connector);
    }

    //########################################
}