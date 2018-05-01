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

class MDN_AutoCancelOrder_Block_System_Config_Button_ShowLogs extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * when clicking on button from config page, call AdminController to load and render layout,
     * then create dynamicaly the block to display page that contain data base's entry
     * 
     * @param Varien_Data_Form_Element_Abstract $element
     * @return type 
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    { 
        $this->setElement($element);
        $url = $this->getUrl('AutoCancelOrder/Admin/showLogs');

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Show logs')
                    ->setOnClick("setLocation('$url')")
                    ->toHtml();

        return $html;
    }
}