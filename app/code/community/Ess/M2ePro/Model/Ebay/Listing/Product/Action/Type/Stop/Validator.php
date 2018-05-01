<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Ebay_Listing_Product_Action_Type_Stop_Validator
    extends Ess_M2ePro_Model_Ebay_Listing_Product_Action_Type_Validator
{
    //########################################

    public function validate()
    {
        if (!$this->getListingProduct()->isStoppable()) {

            $params = $this->getParams();

            if (empty($params['remove'])) {

                // M2ePro_TRANSLATIONS
                // Item is not Listed or not available
                $this->addMessage('Item is not Listed or not available');

            } else {
                $this->getListingProduct()->setData('status', Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED);
                $this->getListingProduct()->save();

                $this->getListingProduct()->deleteInstance();
            }

            return false;
        }

        return true;
    }

    //########################################
}