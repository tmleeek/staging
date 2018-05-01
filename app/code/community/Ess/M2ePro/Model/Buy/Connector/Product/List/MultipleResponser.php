<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Buy_Connector_Product_List_MultipleResponser
    extends Ess_M2ePro_Model_Buy_Connector_Product_Responser
{
    // ########################################

    protected function getSuccessfulMessage(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        // M2ePro_TRANSLATIONS
        // Item was successfully Listed
        return 'Item was successfully Listed';
    }

    // ########################################
}