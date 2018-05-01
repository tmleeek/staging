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

class Magmodules_Alternatelang_Block_Adminhtml_System_Config_Form_Field_Heading extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {

    public function render(Varien_Data_Form_Element_Abstract $element) 
    {
        $useContainerId = $element->getData('use_container_id');
        return sprintf('<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="5"><h4>%s</h4></td></tr>',
            $element->getHtmlId(), $element->getLabel()
        );
    }
    
}
