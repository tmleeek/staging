<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Ebay_Listing_Product_Action_Type_Stop_Request
    extends Ess_M2ePro_Model_Ebay_Listing_Product_Action_Type_Request
{
    //########################################

    protected function afterBuildDataEvent()
    {
        $this->getConfigurator()->setPriority(
            Ess_M2ePro_Model_Ebay_Listing_Product_Action_Configurator::PRIORITY_STOP
        );
    }

    //########################################

    /**
     * @return array
     */
    public function getActionData()
    {
        return array(
            'item_id' => $this->getEbayListingProduct()->getEbayItemIdReal()
        );
    }

    //########################################

    protected function initializeVariations() {}

    // ---------------------------------------

    protected function prepareFinalData(array $data)
    {
        return $data;
    }

    //########################################
}