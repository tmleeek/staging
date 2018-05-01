<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Helper_Component_Amazon_Business extends Mage_Core_Helper_Abstract
{
    //########################################

    public function isEnabled()
    {
        return (bool)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/amazon/business/', 'mode');
    }

    //########################################
}