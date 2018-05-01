<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Ebay_Listing_Product_Action_Type_Revise_Validator
    extends Ess_M2ePro_Model_Ebay_Listing_Product_Action_Type_Validator
{
    //########################################

    public function validate()
    {
        $params = $this->getParams();
        if (!isset($params['out_of_stock_control_current_state']) ||
            !isset($params['out_of_stock_control_result'])) {

            throw new Ess_M2ePro_Model_Exception_Logic('Miss required parameters.');
        }

        if (!$this->getListingProduct()->isRevisable()) {

            // M2ePro_TRANSLATIONS
            // Item is not Listed or not available
            $this->addMessage('Item is not Listed or not available');

            return false;
        }

        if (!$this->validateCategory()) {
            return false;
        }

        if (!$params['out_of_stock_control_result'] &&
            !$this->validateQty()
        ) {
            return false;
        }

        if ($this->getEbayListingProduct()->isVariationsReady()) {

            if (!$this->validateVariationsOptions()) {
                return false;
            }

            if (!$this->validateVariationsFixedPrice()) {
                return false;
            }

            return true;
        }

        if ($this->getEbayListingProduct()->isListingTypeAuction()) {
            if (!$this->validateStartPrice()) {
                return false;
            }

            if (!$this->validateReservePrice()) {
                return false;
            }

            if (!$this->validateBuyItNowPrice()) {
                return false;
            }
        } else {
            if (!$this->validateFixedPrice()) {
                return false;
            }
        }

        return true;
    }

    //########################################
}