<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Adminhtml_Development_Module_SynchronizationController
    extends Ess_M2ePro_Controller_Adminhtml_Development_CommandController
{
    //########################################

    /**
     * @title "Run All"
     * @description "Run all cron synchronization tasks as developer mode"
     * @confirm "Are you sure?"
     * @components
     * @new_line
     */
    public function synchCronTasksAction()
    {
        $this->processSynchTasks(array(
            Ess_M2ePro_Model_Synchronization_Task_Global_Abstract::PROCESSING,
            Ess_M2ePro_Model_Synchronization_Task_Global_Abstract::MAGENTO_PRODUCTS,
            Ess_M2ePro_Model_Synchronization_Task_Global_Abstract::STOP_QUEUE,
            Ess_M2ePro_Model_Synchronization_Task_Component_Abstract::GENERAL,
            Ess_M2ePro_Model_Synchronization_Task_Component_Abstract::LISTINGS_PRODUCTS,
            Ess_M2ePro_Model_Synchronization_Task_Component_Abstract::TEMPLATES,
            Ess_M2ePro_Model_Synchronization_Task_Component_Abstract::ORDERS,
            Ess_M2ePro_Model_Synchronization_Task_Component_Abstract::OTHER_LISTINGS,
            Ess_M2ePro_Model_Synchronization_Task_Component_Abstract::POLICIES
        ));
    }

    //########################################

    /**
     * @title "General"
     * @description "Run only general synchronization as developer mode"
     * @confirm "Are you sure?"
     * @components
     */
    public function generalAction()
    {
        $this->processSynchTasks(array(
            Ess_M2ePro_Model_Synchronization_Task_Component_Abstract::GENERAL
        ));
    }

    /**
     * @title "Processing"
     * @description "Run only defaults synchronization as developer mode"
     * @confirm "Are you sure?"
     */
    public function synchProcessingAction()
    {
        $this->processSynchTasks(array(
            Ess_M2ePro_Model_Synchronization_Task_Global_Abstract::PROCESSING
        ));
    }

    //########################################

    /**
     * @title "Listings Products"
     * @description "Run only listings products synchronization as developer mode"
     * @confirm "Are you sure?"
     * @components
     */
    public function synchListingsProductsAction()
    {
        $this->processSynchTasks(array(
            Ess_M2ePro_Model_Synchronization_Task_Component_Abstract::LISTINGS_PRODUCTS
        ));
    }

    /**
     * @title "Other Listings"
     * @description "Run only Other listings synchronization as developer mode"
     * @confirm "Are you sure?"
     * @components
     */
    public function synchOtherListingsAction()
    {
        $this->processSynchTasks(array(
            Ess_M2ePro_Model_Synchronization_Task_Component_Abstract::OTHER_LISTINGS
        ));
    }

    //########################################

    /**
     * @title "Templates"
     * @description "Run only stock level synchronization as developer mode"
     * @confirm "Are you sure?"
     * @components
     */
    public function synchTemplatesAction()
    {
        $this->processSynchTasks(array(
            Ess_M2ePro_Model_Synchronization_Task_Component_Abstract::TEMPLATES
        ));
    }

    //########################################

    /**
     * @title "Marketplaces"
     * @description "Run only marketplaces synchronization as developer mode"
     * @prompt "Please enter Marketplace ID."
     * @prompt_var "marketplace_id"
     * @components
     */
    public function synchMarketplacesAction()
    {
        $params = array();

        $marketplaceId = (int)$this->getRequest()->getParam('marketplace_id');
        if (!empty($marketplaceId)) {
            $params['marketplace_id'] = $marketplaceId;
        }

        $this->processSynchTasks(array(
            Ess_M2ePro_Model_Synchronization_Task_Component_Abstract::MARKETPLACES
        ), $params);
    }

    //########################################

    /**
     * @title "Orders"
     * @description "Run only orders synchronization as developer mode"
     * @confirm "Are you sure?"
     * @components
     */
    public function synchOrdersAction()
    {
        $this->processSynchTasks(array(
            Ess_M2ePro_Model_Synchronization_Task_Component_Abstract::ORDERS
        ));
    }

    //########################################

    /**
     * @title "Magento Products"
     * @description "Run only magento products synchronization as developer mode"
     * @confirm "Are you sure?"
     */
    public function synchMagentoProductsAction()
    {
        $this->processSynchTasks(array(
            Ess_M2ePro_Model_Synchronization_Task_Global_Abstract::MAGENTO_PRODUCTS
        ));
    }

    //########################################

    private function processSynchTasks($tasks, $params = array())
    {
        session_write_close();

        /** @var $dispatcher Ess_M2ePro_Model_Synchronization_Dispatcher */
        $dispatcher = Mage::getModel('M2ePro/Synchronization_Dispatcher');

        $components = Mage::helper('M2ePro/Component')->getComponents();
        if ($this->getRequest()->getParam('component')) {
            $components = array($this->getRequest()->getParam('component'));
        }

        $dispatcher->setAllowedComponents($components);
        $dispatcher->setAllowedTasksTypes($tasks);

        $dispatcher->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_DEVELOPER);
        $dispatcher->setParams($params);

        $dispatcher->process();

        echo '<pre>'.$dispatcher->getOperationHistory()->getFullProfilerInfo().'</pre>';
    }

    //########################################
}