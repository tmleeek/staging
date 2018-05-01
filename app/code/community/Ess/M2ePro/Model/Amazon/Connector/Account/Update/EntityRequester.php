<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Amazon_Connector_Account_Update_EntityRequester
    extends Ess_M2ePro_Model_Amazon_Connector_Command_Pending_Requester
{
    // ########################################

    protected function getRequestData()
    {
        return $this->params;
    }

    protected function getCommand()
    {
        return array('account','update','entity');
    }

    // ########################################

    protected function getProcessingRunnerModelName()
    {
        return 'Amazon_Connector_Account_Update_ProcessingRunner';
    }

    // ########################################
}