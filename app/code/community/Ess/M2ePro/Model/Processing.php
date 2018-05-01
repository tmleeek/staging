<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Processing extends Ess_M2ePro_Model_Abstract
{
    //####################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/Processing');
    }

    //####################################

    public function getModel()
    {
        return $this->getData('model');
    }

    public function getParams()
    {
        return $this->getSettings('params');
    }

    public function getResultData()
    {
        return $this->getSettings('result_data');
    }

    public function getResultMessages()
    {
        return $this->getSettings('result_messages');
    }

    public function isCompleted()
    {
        return (bool)$this->getData('is_completed');
    }

    //####################################
}