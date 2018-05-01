<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

/**
 * @method Ess_M2ePro_Model_Buy_Listing getComponentListing()
 * @method Ess_M2ePro_Model_Buy_Template_SellingFormat getComponentSellingFormatTemplate()
 * @method Ess_M2ePro_Model_Buy_Listing_Product getComponentProduct()
 */
class Ess_M2ePro_Model_Buy_Listing_Product_PriceCalculator
    extends Ess_M2ePro_Model_Listing_Product_PriceCalculator
{
    //########################################

    protected function isPriceVariationModeParent()
    {
        return $this->getPriceVariationMode()
                            == Ess_M2ePro_Model_Buy_Template_SellingFormat::PRICE_VARIATION_MODE_PARENT;
    }

    protected function isPriceVariationModeChildren()
    {
        return $this->getPriceVariationMode()
                            == Ess_M2ePro_Model_Buy_Template_SellingFormat::PRICE_VARIATION_MODE_CHILDREN;
    }

    //########################################

    protected function getCurrencyForPriceConvert()
    {
        return Ess_M2ePro_Helper_Component_Buy::DEFAULT_CURRENCY;
    }

    //########################################
}