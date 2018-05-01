<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Adminhtml_Development_Module_ServicingController
    extends Ess_M2ePro_Controller_Adminhtml_Development_CommandController
{
    //########################################

    /**
     * @title "Process License"
     * @description "Process License"
     */
    public function runLicenseAction()
    {
        $servicingDispatcher = Mage::getModel('M2ePro/Servicing_Dispatcher');
        $servicingDispatcher->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_DEVELOPER);

        if ($servicingDispatcher->processTask('license')) {
            $this->_getSession()->addSuccess('Servicing License was successfully performed.');
        } else {
            $this->_getSession()->addError('Servicing License was performed with errors.');
        }

        $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
    }

    //########################################

    /**
     * @title "Process Messages"
     * @description "Process Messages Task"
     */
    public function runMessagesAction()
    {
        $servicingDispatcher = Mage::getModel('M2ePro/Servicing_Dispatcher');
        $servicingDispatcher->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_DEVELOPER);

        if ($servicingDispatcher->processTask('messages')) {
            $this->_getSession()->addSuccess('Servicing Messages was successfully performed.');
        } else {
            $this->_getSession()->addError('Servicing Messages was performed with errors.');
        }

        $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
    }

    //########################################

    /**
     * @title "Process Settings"
     * @description "Process Settings Task"
     */
    public function runSettingsAction()
    {
        $servicingDispatcher = Mage::getModel('M2ePro/Servicing_Dispatcher');
        $servicingDispatcher->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_DEVELOPER);

        if ($servicingDispatcher->processTask('settings')) {
            $this->_getSession()->addSuccess('Servicing Settings was successfully performed.');
        } else {
            $this->_getSession()->addError('Servicing Settings was performed with errors.');
        }

        $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
    }

    //########################################

    /**
     * @title "Process Exceptions"
     * @description "Process Exceptions Task"
     */
    public function runExceptionsAction()
    {
        $servicingDispatcher = Mage::getModel('M2ePro/Servicing_Dispatcher');
        $servicingDispatcher->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_DEVELOPER);

        if ($servicingDispatcher->processTask('exceptions')) {
            $this->_getSession()->addSuccess('Servicing Exceptions was successfully performed.');
        } else {
            $this->_getSession()->addError('Servicing Exceptions was performed with errors.');
        }

        $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
    }

    //########################################

    /**
     * @title "Process Marketplaces"
     * @description "Process Marketplaces Task"
     */
    public function runMarketplacesAction()
    {
        $servicingDispatcher = Mage::getModel('M2ePro/Servicing_Dispatcher');
        $servicingDispatcher->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_DEVELOPER);

        if ($servicingDispatcher->processTask('marketplaces')) {
            $this->_getSession()->addSuccess('Servicing Marketplaces was successfully performed.');
        } else {
            $this->_getSession()->addError('Servicing Marketplaces was performed with errors.');
        }

        $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
    }

    //########################################

    /**
     * @title "Process Cron"
     * @description "Process Cron Task"
     */
    public function runCronAction()
    {
        $servicingDispatcher = Mage::getModel('M2ePro/Servicing_Dispatcher');
        $servicingDispatcher->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_DEVELOPER);

        if ($servicingDispatcher->processTask('cron')) {
            $this->_getSession()->addSuccess('Servicing Cron was successfully performed.');
        } else {
            $this->_getSession()->addError('Servicing Cron was performed with errors.');
        }

        $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
    }

    //########################################

    /**
     * @title "Process Statistic"
     * @description "Process Statistic Task"
     */
    public function runStatisticAction()
    {
        $servicingDispatcher = Mage::getModel('M2ePro/Servicing_Dispatcher');
        $servicingDispatcher->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_DEVELOPER);

        if ($servicingDispatcher->processTask('statistic')) {
            $this->_getSession()->addSuccess('Servicing Statistic was successfully performed.');
        } else {
            $this->_getSession()->addError('Servicing Statistic was performed with errors.');
        }

        $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
    }

    //########################################
}