<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Buy_Connector_Account_Delete_ProcessingRunner
    extends Ess_M2ePro_Model_Connector_Command_Pending_Processing_Runner_Single
{
    //########################################

    protected function setLocks()
    {
        parent::setLocks();

        $params = $this->getParams();

        /** @var Ess_M2ePro_Model_Account $account */
        $account = Mage::helper('M2ePro/Component_Buy')->getCachedObject('Account', $params['account_id']);

        $account->addProcessingLock(NULL, $this->getProcessingObject()->getId());
        $account->addProcessingLock('server_synchronize', $this->getProcessingObject()->getId());
        $account->addProcessingLock('deleting_from_server', $this->getProcessingObject()->getId());
    }

    protected function unsetLocks()
    {
        parent::unsetLocks();

        $params = $this->getParams();

        try {
            /** @var Ess_M2ePro_Model_Account $account */
            $account = Mage::helper('M2ePro/Component_Buy')->getCachedObject('Account', $params['account_id']);

            $account->deleteProcessingLocks(NULL, $this->getProcessingObject()->getId());
            $account->deleteProcessingLocks('server_synchronize', $this->getProcessingObject()->getId());
            $account->deleteProcessingLocks('deleting_from_server', $this->getProcessingObject()->getId());
        } catch (Exception $exception) {}
    }

    //########################################
}