<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Buy_Connector_Orders_Get_ItemsResponser
    extends Ess_M2ePro_Model_Buy_Connector_Command_Pending_Responser
{
    // ########################################

    protected function validateResponse()
    {
        $responseData = $this->getResponse()->getData();
        return isset($responseData['orders']) && array_key_exists('latest_creation_time', $responseData);
    }

    // ########################################
}