<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Amazon_Connector_Orders_Get_Details_ItemsRequester
    extends Ess_M2ePro_Model_Amazon_Connector_Command_Pending_Requester
{
    // ########################################

    public function getCommand()
    {
        return array('orders','get','entitiesDetails');
    }

    // ########################################

    protected function getProcessingRunnerModelName()
    {
        return 'Connector_Command_Pending_Processing_Runner_Partial';
    }

    // ########################################

    protected function getResponserParams()
    {
        return array(
            'account_id'     => $this->account->getId(),
            'marketplace_id' => $this->account->getChildObject()->getMarketplaceId()
        );
    }

    // ########################################

    protected function getRequestData()
    {
        return array(
            'items' => $this->params['items'],
        );
    }

    // ########################################
}