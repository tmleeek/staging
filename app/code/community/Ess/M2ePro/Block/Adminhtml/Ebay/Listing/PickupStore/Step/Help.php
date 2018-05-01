<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Block_Adminhtml_Ebay_Listing_PickupStore_Step_Help extends Mage_Adminhtml_Block_Widget
{
    //########################################

    public function __construct()
    {
        parent::__construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('ebayListingPickupStoreStepStoresHelp');
        // ---------------------------------------

        $this->setTemplate('M2ePro/ebay/listing/pickupStore/step/help.phtml');
    }

    //########################################
}