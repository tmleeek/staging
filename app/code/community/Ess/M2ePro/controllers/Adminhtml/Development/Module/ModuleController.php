<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Adminhtml_Development_Module_ModuleController
    extends Ess_M2ePro_Controller_Adminhtml_Development_CommandController
{
    //########################################

    /**
     * @title "Run All"
     * @description "Emulate starting cron"
     * @new_line
     */
    public function runCronAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('Cron was successfully performed.');
        } else {
            $this->_getSession()->addError('Cron was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    //########################################

    /**
     * @title "Process Issues Resolver"
     * @description "Process Issues Resolver Task"
     */
    public function issuesResolverAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');
        $cronRunner->setAllowedTasks(array(
            Ess_M2ePro_Model_Cron_Task_IssuesResolver::NICK
        ));

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('Issues Resolver Task was successfully performed.');
        } else {
            $this->_getSession()->addError('Issues Resolver Task was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    //########################################

    /**
     * @title "Process Synchronization"
     * @description "Process Synchronization Task"
     */
    public function synchronizationAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');
        $cronRunner->setAllowedTasks(array(
            Ess_M2ePro_Model_Cron_Task_Synchronization::NICK
        ));

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('Synchronization was successfully performed.');
        } else {
            $this->_getSession()->addError('Synchronization was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    //########################################

    /**
     * @title "Process Servicing"
     * @description "Process Servicing Task"
     */
    public function processServicingAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');
        $cronRunner->setAllowedTasks(array(
            Ess_M2ePro_Model_Cron_Task_Servicing::NICK
        ));

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('Servicing was successfully performed.');
        } else {
            $this->_getSession()->addError('Servicing was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    //########################################

    /**
     * @title "Process Logs Clearing"
     * @description "Process Logs Clearing Task"
     */
    public function processLogsAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');
        $cronRunner->setAllowedTasks(array(
            Ess_M2ePro_Model_Cron_Task_LogsClearing::NICK
        ));

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('Logs Clearing was successfully performed.');
        } else {
            $this->_getSession()->addError('Logs Clearing was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    //########################################

    /**
     * @title "Process eBay Actions"
     * @description "Process eBay Actions Task"
     */
    public function ebayActionsAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');
        $cronRunner->setAllowedTasks(array(
            Ess_M2ePro_Model_Cron_Task_EbayActions::NICK
        ));

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('eBay Actions was successfully performed.');
        } else {
            $this->_getSession()->addError('eBay Actions was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    //########################################

    /**
     * @title "Process Amazon Actions"
     * @description "Process Amazon Actions Task"
     */
    public function amazonActionsAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');
        $cronRunner->setAllowedTasks(array(
            Ess_M2ePro_Model_Cron_Task_AmazonActions::NICK
        ));

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('Amazon Actions was successfully performed.');
        } else {
            $this->_getSession()->addError('Amazon Actions was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    //########################################

    /**
     * @title "Process Request Pending Single"
     * @description "Process Request Pending Single Task"
     */
    public function requestPendingSingleAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');
        $cronRunner->setAllowedTasks(array(
            Ess_M2ePro_Model_Cron_Task_RequestPendingSingle::NICK
        ));

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('Request Pending Single was successfully performed.');
        } else {
            $this->_getSession()->addError('Request Pending Single was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    /**
     * @title "Process Request Pending Partial"
     * @description "Process Request Pending Partial Task"
     */
    public function requestPendingPartialAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');
        $cronRunner->setAllowedTasks(array(
            Ess_M2ePro_Model_Cron_Task_RequestPendingPartial::NICK
        ));

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('Request Pending Partial was successfully performed.');
        } else {
            $this->_getSession()->addError('Request Pending Partial was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    //########################################

    /**
     * @title "Process Connector Pending Single"
     * @description "Process Connector Pending Single Task"
     */
    public function connectorPendingSingleAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');
        $cronRunner->setAllowedTasks(array(
            Ess_M2ePro_Model_Cron_Task_ConnectorRequesterPendingSingle::NICK
        ));

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('Connector Pending Single was successfully performed.');
        } else {
            $this->_getSession()->addError('Connector Pending Single was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    /**
     * @title "Process Connector Pending Partial"
     * @description "Process Connector Pending Partial Task"
     */
    public function connectorPendingPartialAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');
        $cronRunner->setAllowedTasks(array(
            Ess_M2ePro_Model_Cron_Task_ConnectorRequesterPendingPartial::NICK
        ));

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('Connector Pending Partial was successfully performed.');
        } else {
            $this->_getSession()->addError('Connector Pending Partial was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    //########################################

    /**
     * @title "Process Repricing Update Settings"
     * @description "Process Repricing Update Settings"
     */
    public function repricingUpdateSettingsAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');
        $cronRunner->setAllowedTasks(array(
            Ess_M2ePro_Model_Cron_Task_RepricingUpdateSettings::NICK
        ));

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('Repricing Send Data was successfully performed.');
        } else {
            $this->_getSession()->addError('Repricing Send Data was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    /**
     * @title "Process Repricing Synchronization General"
     * @description "Process Repricing Synchronization General"
     */
    public function repricingSynchronizationGeneralAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');
        $cronRunner->setAllowedTasks(array(
            Ess_M2ePro_Model_Cron_Task_RepricingSynchronizationGeneral::NICK
        ));

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('Repricing Synchronization General was successfully performed.');
        } else {
            $this->_getSession()->addError('Repricing Synchronization General was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    /**
     * @title "Process Repricing Synchronization Actual Price"
     * @description "Process Repricing Synchronization Actual Price"
     */
    public function repricingSynchronizationActualPriceAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');
        $cronRunner->setAllowedTasks(array(
            Ess_M2ePro_Model_Cron_Task_RepricingSynchronizationActualPrice::NICK
        ));

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('Repricing Synchronization Actual Price was successfully performed.');
        } else {
            $this->_getSession()->addError('Repricing Synchronization Actual Price was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    /**
     * @title "Process Repricing Inspect Products"
     * @description "Process Repricing Inspect Products Task"
     */
    public function repricingRepricingInspectProductsAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');
        $cronRunner->setAllowedTasks(array(
            Ess_M2ePro_Model_Cron_Task_RepricingInspectProducts::NICK
        ));

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('Repricing Inspect Products was successfully performed.');
        } else {
            $this->_getSession()->addError('Repricing Inspect Products was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    /**
     * @title "Process Archive Orders Entities"
     * @description "Process Archive Orders Entities Task"
     */
    public function archiveOrdersEntitiesAction()
    {
        $cronRunner = Mage::getModel('M2ePro/Cron_Runner_Developer');
        $cronRunner->setAllowedTasks(array(
            Ess_M2ePro_Model_Cron_Task_ArchiveOrdersEntities::NICK
        ));

        if ($cronRunner->process()) {
            $this->_getSession()->addSuccess('Archive Orders Entities was successfully performed.');
        } else {
            $this->_getSession()->addError('Archive Orders Entities was performed with errors.');
        }

        echo '<pre>'.$cronRunner->getOperationHistory()->getFullDataInfo().'</pre>';
    }

    //########################################
}