<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_XSitemap_Block_Adminhtml_System_Config_Frontend_Alternate extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected $_off = false;

    public function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        if($this->_off){
            return '';
        }
        return parent::_getElementHtml($element);
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if(Mage::helper('xsitemap/version')->isEnterpriseSince113()){
            $element->setComment('For magento EE since 1.13.0 this feature for the present in development.');
            $this->_off = true;
        }
        return parent::render($element);
    }
}