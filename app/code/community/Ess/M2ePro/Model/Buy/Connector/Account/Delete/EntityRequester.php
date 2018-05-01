<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Buy_Connector_Account_Delete_EntityRequester
    extends Ess_M2ePro_Model_Buy_Connector_Command_Pending_Requester
{
    //########################################

    protected function getRequestData()
    {
        return array();
    }

    protected function getCommand()
    {
        return array('account','delete','entity');
    }

    //########################################

    protected function getProcessingRunnerModelName()
    {
        return 'Buy_Connector_Account_Delete_ProcessingRunner';
    }

    //########################################
}