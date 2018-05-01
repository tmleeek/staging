<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Ebay_Connector_Item_List_Requester
    extends Ess_M2ePro_Model_Ebay_Connector_Item_Requester
{
    //########################################

    protected function getCommand()
    {
        return array('item','add','single');
    }

    protected function getActionType()
    {
        return Ess_M2ePro_Model_Listing_Product::ACTION_LIST;
    }

    protected function getLogsAction()
    {
        return Ess_M2ePro_Model_Listing_Log::ACTION_LIST_PRODUCT_ON_COMPONENT;
    }

    //########################################
}