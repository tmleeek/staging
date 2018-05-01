<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Buy_Connector_Product_ProcessingRunner
    extends Ess_M2ePro_Model_Connector_Command_Pending_Processing_Runner_Single
{
    /** @var Ess_M2ePro_Model_Listing_Product[] $listingsProducts */
    private $listingsProducts = array();

    // ########################################

    public function processSuccess()
    {
        // all listings products can be removed during processing action
        if (count($this->getListingsProducts()) <= 0) {
            return true;
        }

        return parent::processSuccess();
    }

    public function processExpired()
    {
        // all listings products can be removed during processing action
        if (count($this->getListingsProducts()) <= 0) {
            return;
        }

        $this->getResponser()->failDetected($this->getExpiredErrorMessage());
    }

    public function complete()
    {
        // all listings products can be removed during processing action
        if (count($this->getListingsProducts()) <= 0) {
            $this->getProcessingObject()->deleteInstance();
            return;
        }

        parent::complete();
    }

    // ########################################

    protected function setLocks()
    {
        parent::setLocks();

        $params = $this->getParams();

        $alreadyLockedListings = array();
        foreach ($this->getListingsProducts() as $listingProduct) {

            /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */

            $listingProduct->addProcessingLock(NULL, $this->getProcessingObject()->getId());
            $listingProduct->addProcessingLock('in_action', $this->getProcessingObject()->getId());
            $listingProduct->addProcessingLock(
                $params['lock_identifier'].'_action', $this->getProcessingObject()->getId()
            );

            if (isset($alreadyLockedListings[$listingProduct->getListingId()])) {
                continue;
            }

            $listingProduct->getListing()->addProcessingLock(NULL, $this->getProcessingObject()->getId());

            $alreadyLockedListings[$listingProduct->getListingId()] = true;
        }
    }

    protected function unsetLocks()
    {
        parent::unsetLocks();

        $params = $this->getParams();

        $alreadyUnlockedListings = array();
        foreach ($this->getListingsProducts() as $listingProduct) {

            $listingProduct->deleteProcessingLocks(NULL, $this->getProcessingObject()->getId());
            $listingProduct->deleteProcessingLocks('in_action', $this->getProcessingObject()->getId());
            $listingProduct->deleteProcessingLocks(
                $params['lock_identifier'].'_action', $this->getProcessingObject()->getId()
            );

            if (isset($alreadyUnlockedListings[$listingProduct->getListingId()])) {
                continue;
            }

            $listingProduct->getListing()->deleteProcessingLocks(NULL, $this->getProcessingObject()->getId());

            $alreadyUnlockedListings[$listingProduct->getListingId()] = true;
        }
    }

    // ########################################

    protected function getListingsProducts()
    {
        if (!empty($this->listingsProducts)) {
            return $this->listingsProducts;
        }

        $params = $this->getParams();

        /** @var Ess_M2ePro_Model_Mysql4_Listing_Product_Collection $collection */
        $collection = Mage::helper('M2ePro/Component_Buy')->getCollection('Listing_Product');
        $collection->addFieldToFilter('id', array('in' => $params['listing_product_ids']));

        return $this->listingsProducts = $collection->getItems();
    }

    // ########################################
}