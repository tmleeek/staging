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

class Magmodules_Alternatelang_Block_Adminhtml_Config_Form_Field_Targeting extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

	protected $_renders = array();
   	
    public function __construct() 
    {        
        $layout = Mage::app()->getFrontController()->getAction()->getLayout();
        $renderer_store = $layout->createBlock('alternatelang/adminhtml_config_form_renderer_select', '', array('is_render_to_js_template' => true));                							                
        $renderer_store->setOptions(Mage::getModel('alternatelang/source_store')->toOptionArray());
        $renderer_group = $layout->createBlock('alternatelang/adminhtml_config_form_renderer_select', '', array('is_render_to_js_template' => true));                							                
        $renderer_group->setOptions(Mage::getModel('alternatelang/source_group')->toOptionArray());

        $this->addColumn('store_id', array(
            'label' => Mage::helper('alternatelang')->__('Storefront'),
            'style' => 'width:120px',
        	'renderer' => $renderer_store                        
        ));

        $this->addColumn('language_code', array(
            'label' => Mage::helper('alternatelang')->__('Language Code'),
            'style' => 'width:80px',
        ));        

        $this->addColumn('group', array(
            'label' => Mage::helper('alternatelang')->__('Group'),
            'style' => 'width:100px',
        	'renderer' => $renderer_group                        
        ));        
                                
        $this->_renders['store_id'] = $renderer_store; 
        $this->_renders['group'] = $renderer_group; 
        
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('alternatelang')->__('Add Option');
        parent::__construct();
    }
    
    protected function _prepareArrayRow(Varien_Object $row)
    {    	
    	foreach ($this->_renders as $key => $render){
	        $row->setData(
	            'option_extra_attr_' . $render->calcOptionHash($row->getData($key)),
	            'selected="selected"'
	        );
    	}
    } 

}