<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Ebay_Synchronization_General_AccountPickupStore_Process
    extends Ess_M2ePro_Model_Ebay_Synchronization_General_Abstract
{
    const MAX_AFFECTED_ITEMS_COUNT = 10000;

    //########################################

    protected function getNick()
    {
        return '/account_pickup_store/process/';
    }

    protected function getTitle()
    {
        return 'Pickup Store Process';
    }

    // ---------------------------------------

    protected function getPercentsStart()
    {
        return 0;
    }

    protected function getPercentsEnd()
    {
        return 60;
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
        // The "Prepare Data" Action for eBay Account: "%account_title%" is started. Please wait...
        $status = 'The "Prepare Data" Action for eBay Account: "%account_title%" is started. ';
        $status .= 'Please wait...';
        $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));

        $this->getActualOperationHistory()->addTimePoint(
            __METHOD__.'process'.$account->getId(),
            'Process Account '.$account->getTitle()
        );

        $this->processAccount($account);

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__.'process'.$account->getId());

        // M2ePro_TRANSLATIONS
        // The "Prepare Data" Action for eBay Account: "%account_title%" is finished. Please wait...
        $status = 'The "Prepare Data" Action for eBay Account: "%account_title%" is finished.'.
            ' Please wait...';
        $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));
        $this->getActualLockItem()->activate();
    }

    //########################################

    private function processAccount(Ess_M2ePro_Model_Account $account)
    {
        $collection = Mage::getResourceModel('M2ePro/Ebay_Listing_Product_PickupStore_Collection');
        $collection->addFieldToFilter('is_process_required', 1);
        $collection->getSelect()->limit(self::MAX_AFFECTED_ITEMS_COUNT);

        $collection->getSelect()->joinLeft(
            array('eaps' => Mage::getResourceModel('M2ePro/Ebay_Account_PickupStore')->getMainTable()),
            'eaps.id = main_table.account_pickup_store_id',
            array('account_id')
        );

        $collection->addFieldToFilter('eaps.account_id', $account->getId());

        $listingProductIds = $collection->getColumnValues('listing_product_id');
        if (empty($listingProductIds)) {
            return;
        }

        $listingProductIds = array_unique($listingProductIds);

        $affectedItemsCount = 0;

        foreach ($listingProductIds as $listingProductId) {
            /** @var Ess_M2ePro_Model_Listing_Product $listingProduct */
            $listingProduct = Mage::helper('M2ePro/Component_Ebay')->getObject('Listing_Product', $listingProductId);

            $pickupStoreStateUpdater = Mage::getModel('M2ePro/Ebay_Listing_Product_PickupStore_State_Updater');
            $pickupStoreStateUpdater->setListingProduct($listingProduct);

            $affectedItemsCount += $pickupStoreStateUpdater->process();

            if ($affectedItemsCount >= self::MAX_AFFECTED_ITEMS_COUNT) {
                break;
            }
        }
    }

    //########################################
}