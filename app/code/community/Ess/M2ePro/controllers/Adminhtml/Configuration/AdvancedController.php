<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Adminhtml_Configuration_AdvancedController
    extends Ess_M2ePro_Controller_Adminhtml_Configuration_MainController
{
    //########################################

    public function saveAction()
    {
        $this->_redirectUrl($this->_getRefererUrl());
    }

    //########################################
}