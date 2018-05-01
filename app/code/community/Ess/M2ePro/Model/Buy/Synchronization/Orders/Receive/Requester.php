<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Buy_Synchronization_Orders_Receive_Requester
    extends Ess_M2ePro_Model_Buy_Connector_Orders_Get_ItemsRequester
{
    //########################################

    protected function getProcessingRunnerModelName()
    {
        return 'Buy_Synchronization_Orders_Receive_ProcessingRunner';
    }

    //########################################
}