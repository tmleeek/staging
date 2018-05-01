<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Synchronization_Global_Launcher
    extends Ess_M2ePro_Model_Synchronization_Task_Global_Abstract
{
    //########################################

    protected function getType()
    {
        return NULL;
    }

    protected function getNick()
    {
        return NULL;
    }

    // ---------------------------------------

    protected function getPercentsStart()
    {
        return 0;
    }

    protected function getPercentsEnd()
    {
        return 100;
    }

    //########################################

    protected function performActions()
    {
        $result = true;

        $result = !$this->processTask('Global_Processing') ? false : $result;
        $result = !$this->processTask('Global_MagentoProducts') ? false : $result;
        $result = !$this->processTask('Global_StopQueue') ? false : $result;

        return $result;
    }

    //########################################

    protected function getFullSettingsPath()
    {
        return '/global/';
    }

    //########################################
}