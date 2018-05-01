<?php
abstract class Extendware_EWCore_Block_Frontend_Widget_Form extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Form
{
	protected $rendererPrefix = 'ewcore/frontend_widget_form_renderer';
	
	protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('extendware/ewcore/frontend/widget/form.phtml');
    }
    
	protected function _prepareLayout()
    {
    	parent::_prepareLayout();

        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock($this->rendererPrefix . '_element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock($this->rendererPrefix . '_fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock($this->rendererPrefix . '_fieldset_element')
        );
        
        return $this;
    }
    
	protected function _toHtml()
    {
    	// do this so adminhtml specific callback is not called in the frontend
        return Mage_Core_Block_Template::_toHtml();
    }
}
