<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : ALLAIRE Benjamin
 * @mail : benjamin@boostmyshop.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class MDN_AutoCancelOrder_Block_Adminhtml_System_Config_Form_Field_Date extends Mage_Adminhtml_Block_System_Config_Form_Field {

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $element->setFormat(Varien_Date::DATE_INTERNAL_FORMAT); //or other format
        $element->setImage($this->getSkinUrl('images/grid-cal.gif'));
        return parent::render($element);
    }

}
