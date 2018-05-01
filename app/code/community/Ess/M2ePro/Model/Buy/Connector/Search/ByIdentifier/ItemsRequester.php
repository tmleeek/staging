<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Buy_Connector_Search_ByIdentifier_ItemsRequester
    extends Ess_M2ePro_Model_Buy_Connector_Command_Pending_Requester
{
    const SEARCH_TYPE_UPC        = 'UPC';
    const SEARCH_TYPE_GENERAL_ID = 'SKU';

    // ########################################

    public function getCommand()
    {
        return array('product','search','byIdentifier');
    }

    // ########################################

    abstract protected function getQuery();

    abstract protected function getSearchType();

    // ########################################

    protected function getRequestData()
    {
        return array(
            'query' => $this->getQuery(),
            'search_type' => $this->getSearchType(),
        );
    }

    // ########################################
}