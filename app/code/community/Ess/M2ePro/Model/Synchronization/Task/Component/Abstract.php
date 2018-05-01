<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Synchronization_Task_Component_Abstract
    extends Ess_M2ePro_Model_Synchronization_Task_Abstract
{
    const GENERAL           = 'general';
    const LISTINGS_PRODUCTS = 'listings_products';
    const TEMPLATES         = 'templates';
    const ORDERS            = 'orders';
    const MARKETPLACES      = 'marketplaces';
    const OTHER_LISTINGS    = 'other_listings';
    const POLICIES          = 'policies';

    //########################################

    protected function buildTaskPath($taskPath)
    {
        return ($this->isComponentTask() ? ucfirst($this->getComponent()).'_' : '').'Synchronization_'.$taskPath;
    }

    //########################################

    protected function isPossibleToRun()
    {
        if ($this->isComponentLauncherTask() &&
            !Mage::helper('M2ePro/Component_'.ucfirst($this->getComponent()))->isActive()) {
            return false;
        }

        return parent::isPossibleToRun();
    }

    // ---------------------------------------

    protected function beforeStart()
    {
        if (!$this->getParentLockItem()) {
            $this->getLockItem()->create();
            $this->getLockItem()->makeShutdownFunction();
        }

        if (!$this->getParentOperationHistory() || $this->isComponentLauncherTask() || $this->isContainerTask()) {

            $operationHistoryNickSuffix = str_replace('/','_',trim($this->getFullSettingsPath(),'/'));

            $operationHistoryParentId = $this->getParentOperationHistory() ?
                $this->getParentOperationHistory()->getObject()->getId() : NULL;

            $this->getOperationHistory()->start('synchronization_'.$operationHistoryNickSuffix,
                $operationHistoryParentId,
                $this->getInitiator());

            $this->getOperationHistory()->makeShutdownFunction();
        }

        $this->configureLogBeforeStart();
        $this->configureProfilerBeforeStart();
        $this->configureLockItemBeforeStart();
    }

    protected function afterEnd()
    {
        $this->configureLockItemAfterEnd();
        $this->configureProfilerAfterEnd();
        $this->configureLogAfterEnd();

        if ($this->intervalIsEnabled()) {
            $this->intervalSetLastTime(Mage::helper('M2ePro')->getCurrentGmtDate(true));
        }

        if (!$this->getParentOperationHistory() || $this->isComponentLauncherTask() || $this->isContainerTask()) {
            $this->getOperationHistory()->stop();
        }

        if (!$this->getParentLockItem()) {
            $this->getLockItem()->remove();
        }
    }

    //########################################

    abstract protected function getComponent();

    // ---------------------------------------

    /**
     * @return bool
     */
    private function isComponentTask()
    {
        return (bool)$this->getComponent();
    }

    /**
     * @return bool
     */
    private function isComponentLauncherTask()
    {
        return $this->isComponentTask() && $this->isLauncherTask();
    }

    // ---------------------------------------

    /**
     * @return bool
     */
    protected function isStandardTask()
    {
        return !$this->isComponentLauncherTask() && !$this->isContainerTask();
    }

    //########################################

    /**
     * @return string
     */
    protected function getTitle()
    {
        if ($this->isComponentLauncherTask()) {
            return ucfirst($this->getComponent());
        }

        return parent::getTitle();
    }

    /**
     * @return int
     */
    protected function getLogTask()
    {
        switch ($this->getType()) {
            case self::GENERAL:
                return Ess_M2ePro_Model_Synchronization_Log::TASK_GENERAL;
            case self::LISTINGS_PRODUCTS:
                return Ess_M2ePro_Model_Synchronization_Log::TASK_LISTINGS_PRODUCTS;
            case self::TEMPLATES:
                return Ess_M2ePro_Model_Synchronization_Log::TASK_TEMPLATES;
            case self::ORDERS:
                return Ess_M2ePro_Model_Synchronization_Log::TASK_ORDERS;
            case self::MARKETPLACES:
                return Ess_M2ePro_Model_Synchronization_Log::TASK_MARKETPLACES;
            case self::OTHER_LISTINGS:
                return Ess_M2ePro_Model_Synchronization_Log::TASK_OTHER_LISTINGS;
            case self::POLICIES:
                return Ess_M2ePro_Model_Synchronization_Log::TASK_POLICIES;
        }

        return parent::getLogTask();
    }

    protected function getActionForLog()
    {
        $action = Ess_M2ePro_Model_Listing_Log::ACTION_UNKNOWN;

        switch ($this->getNick())
        {
            case '/synchronization/list/':
                $action = Ess_M2ePro_Model_Listing_Log::ACTION_LIST_PRODUCT_ON_COMPONENT;
                break;
            case '/synchronization/relist/':
                $action = Ess_M2ePro_Model_Listing_Log::ACTION_RELIST_PRODUCT_ON_COMPONENT;
                break;
            case '/synchronization/revise/':
                $action = Ess_M2ePro_Model_Listing_Log::ACTION_REVISE_PRODUCT_ON_COMPONENT;
                break;
            case '/synchronization/stop/':
                $action = Ess_M2ePro_Model_Listing_Log::ACTION_STOP_PRODUCT_ON_COMPONENT;
                break;
        }

        return $action;
    }

    // ---------------------------------------

    /**
     * @return string
     */
    protected function getFullSettingsPath()
    {
        $path = '/'.($this->getComponent() ? strtolower($this->getComponent()).'/' : '');
        $path .= $this->getType() ? strtolower($this->getType()).'/' : '';
        $path .= $this->getNick() ? trim(strtolower($this->getNick()),'/').'/' : '';
        return $path;
    }

    //########################################

    protected function configureLogBeforeStart()
    {
        if ($this->isComponentLauncherTask()) {
            $this->getLog()->setComponentMode($this->getComponent());
        }

        parent::configureLogBeforeStart();
    }

    protected function configureLogAfterEnd()
    {
        if ($this->isComponentLauncherTask()) {
            $this->getLog()->setComponentMode(NULL);
        }

        parent::configureLogAfterEnd();
    }

    //########################################

    protected function configureLockItemBeforeStart()
    {
        $suffix = Mage::helper('M2ePro')->__('Synchronization');

        if ($this->isComponentLauncherTask() || $this->isContainerTask()) {

            $title = $suffix;

            if ($this->isContainerTask()) {
                $title = $this->getTitle().' '.$title;
            }

            if ($this->isComponentTask() && count(Mage::helper('M2ePro/Component')->getActiveComponents()) > 1) {

                $componentHelper = Mage::helper('M2ePro/Component_'.ucfirst($this->getComponent()));

                $this->getActualLockItem()
                    ->setTitle(Mage::helper('M2ePro')
                        ->__('%component% ' . $title, $componentHelper->getTitle()));
            } else {
                $this->getActualLockItem()->setTitle(Mage::helper('M2ePro')->__($title));
            }
        }

        $this->getActualLockItem()->setPercents($this->getPercentsStart());

        // M2ePro_TRANSLATIONS
        // Task "%task_title%" is started. Please wait...
        $status = 'Task "%task_title%" is started. Please wait...';
        $title = ($this->isComponentLauncherTask() || $this->isContainerTask()) ?
            $this->getTitle().' '.$suffix : $this->getTitle();

        $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status,$title));
    }

    protected function configureLockItemAfterEnd()
    {
        $suffix = Mage::helper('M2ePro')->__('Synchronization');

        if ($this->isComponentLauncherTask() || $this->isContainerTask()) {

            $title = $suffix;

            if ($this->isContainerTask()) {
                $title = $this->getTitle().' '.$title;
            }

            if ($this->isComponentTask() && count(Mage::helper('M2ePro/Component')->getActiveComponents()) > 1) {

                $componentHelper = Mage::helper('M2ePro/Component_'.ucfirst($this->getComponent()));

                $this->getActualLockItem()
                    ->setTitle(Mage::helper('M2ePro')
                        ->__('%component% ' . $title, $componentHelper->getTitle()));
            } else {
                $this->getActualLockItem()->setTitle(Mage::helper('M2ePro')->__($title));
            }
        }

        $this->getActualLockItem()->setPercents($this->getPercentsEnd());

        // M2ePro_TRANSLATIONS
        // Task "%task_title%" is finished. Please wait...
        $status = 'Task "%task_title%" is finished. Please wait...';
        $title = ($this->isComponentLauncherTask() || $this->isContainerTask()) ?
            $this->getTitle().' '.$suffix : $this->getTitle();

        $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status,$title));
    }

    //########################################
}