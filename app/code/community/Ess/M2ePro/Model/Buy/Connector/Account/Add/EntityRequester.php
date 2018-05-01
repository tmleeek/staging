<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Buy_Connector_Account_Add_EntityRequester
    extends Ess_M2ePro_Model_Buy_Connector_Command_Pending_Requester
{
    //########################################

    protected function getCommand()
    {
        return array('account','add','entity');
    }

    //########################################

    protected function getResponserParams()
    {
        return array(
            'account_id' => $this->account->getId()
        );
    }

    //########################################

    protected function getRequestData()
    {
        return array(
            'title' => $this->account->getTitle(),
            'web_login' => $this->params['web_login'],
            'web_password' => $this->params['web_password'],
            'ftp_login' => $this->params['ftp_login'],
            'ftp_password' => $this->params['ftp_password']
        );
    }

    //########################################

    protected function getProcessingRunnerModelName()
    {
        return 'Buy_Connector_Account_Add_ProcessingRunner';
    }

    //########################################
}