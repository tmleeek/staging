<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Ebay_Listing_Product_Action_Type_Relist_Validator
    extends Ess_M2ePro_Model_Ebay_Listing_Product_Action_Type_Validator
{
    //########################################

    public function validate()
    {
        if (!$this->getListingProduct()->isRelistable()) {

            // M2ePro_TRANSLATIONS
            // The Item either is Listed, or not Listed yet or not available
            $this->addMessage('The Item either is Listed, or not Listed yet or not available');

            return false;
        }

        if (!$this->validateCategory()) {
            return false;
        }

        if (!$this->validateQty()) {
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