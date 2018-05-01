<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Buy_Connector_Account_Add_EntityResponser
    extends Ess_M2ePro_Model_Buy_Connector_Command_Pending_Responser
{
    //########################################

    protected function validateResponse()
    {
        $responseData = $this->getResponse()->getData();
        if (empty($responseData['hash']) || !isset($responseData['info'])) {
            return false;
        }

        return true;
    }

    protected function processResponseData()
    {
        $responseData = $this->getPreparedResponseData();

        /** @var $buyAccount Ess_M2ePro_Model_Buy_Account */
        $buyAccount = $this->getAccount()->getChildObject();

        $dataForUpdate = array(
            'server_hash' => $responseData['hash'],
            'info'        => Mage::helper('M2ePro')->jsonEncode($responseData['info'])
        );

        if (!empty($responseData['info']['seller_id'])) {
            $dataForUpdate['seller_id'] = $responseData['info']['seller_id'];
        }

        $buyAccount->addData($dataForUpdate)->save();
    }

    //########################################

    /**
     * @return Ess_M2ePro_Model_Account
     */
    protected function getAccount()
    {
        return $this->getObjectByParam('Account','account_id');
    }

    //########################################
}