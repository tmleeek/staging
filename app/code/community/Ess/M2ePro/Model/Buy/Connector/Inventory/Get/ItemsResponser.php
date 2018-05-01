<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Buy_Connector_Inventory_Get_ItemsResponser
    extends Ess_M2ePro_Model_Buy_Connector_Command_Pending_Responser
{
    // ########################################

    protected function validateResponse()
    {
        $responseData = $this->getResponse()->getData();
        return isset($responseData['data']);
    }

    protected function prepareResponseData()
    {
        $preparedData = array(
            'data' => array(),
        );

        $responseData = $this->getResponse()->getData();

        foreach ($responseData['data'] as $receivedItem) {
            if (empty($receivedItem['sku'])) {
                continue;
            }

            $preparedData['data'][$receivedItem['sku']] = $receivedItem;
        }

        $this->preparedResponseData = $preparedData;
    }

    // ########################################
}