<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Observer_Listing_Product_Delete_Before extends Ess_M2ePro_Model_Observer_Abstract
{
    //########################################

    public function process()
    {
        /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */
        $listingProduct = $this->getEvent()->getData('object');

        /** @var Ess_M2ePro_Model_Indexer_Listing_Product_Parent_Manager $manager */
        $manager = Mage::getModel('M2ePro/Indexer_Listing_Product_Parent_Manager',
                                  array($listingProduct->getListing()));
        $manager->markInvalidated();
    }

    //########################################
}