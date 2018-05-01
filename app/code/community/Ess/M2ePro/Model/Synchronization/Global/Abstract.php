<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Synchronization_Global_Abstract
    extends Ess_M2ePro_Model_Synchronization_Task_Global_Abstract
{
    //########################################

    protected function processTask($taskPath)
    {
        return parent::processTask('Global_'.$taskPath);
    }

    //########################################

    protected function getFullSettingsPath()
    {
        $path = '/global/';
        $path .= $this->getType() ? strtolower($this->getType()).'/' : '';
        return $path.trim(strtolower($this->getNick()),'/').'/';
    }

    //########################################
}