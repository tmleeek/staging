<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Alternatelang
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2016 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Alternatelang_Model_System_Config_Model_Enable extends Mage_Core_Model_Config_Data {
   
    protected function _beforeSave() 
    {
        Mage::register('alternatelang_modify_event', true, true);
        parent::_beforeSave();
    }

    public function has_value_for_configuration_changed($observer)
    {
        if(Mage::registry('alternatelang_modify_event') == true) {
            Mage::unregister('alternatelang_modify_event');
            Magmodules_Alternatelang_Model_System_Config_Model_License::isEnabled();
        }
    }
    
}
