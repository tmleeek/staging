<?php
/**
 * Shop By Brands
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitmanufacturers
 * @version      3.3.1
 * @license:     zAuKpf4IoBvEYeo5ue8Cll0eto0di8JUzOnOWiuiAF
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Adminhtml_Aitmanufacturers extends Mage_Adminhtml_Block_Template//Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    //$this->_controller = 'adminhtml_aitmanufacturers';
    //$this->_blockGroup = 'aitmanufacturers';
    //$this->_headerText = Mage::helper('aitmanufacturers')->__('Attributes Pages Manager');
    //$this->_addButtonLabel = Mage::helper('aitmanufacturers')->__('Add Attribute Page');

    //$this->_addButton('fillout', array(
    //        'label'     => Mage::helper('aitmanufacturers')->__('Fill Out Attributes Pages'),
    //        'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/fillOut') .'\')',
    //        'class'     => '',
    //    ));
    parent::__construct();
  }
  
    protected function getStoreId()
    {
        return Mage::registry('store_id');
    }
  
    protected function _prepareLayout()
    {
        $attributeCode = $this->getRequest()->get('attributecode');
        $attrName = Mage::getModel('aitmanufacturers/config')->getAttributeName($attributeCode);
        $this->setChild('add_new_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('aitmanufacturers')->__('Add '.$attrName.' Page'),
                    'onclick'   => "setLocation('".$this->getUrl('*/*/new', array('store' => $this->getStoreId(), 'attributecode' => $attributeCode))."')",
                    'class'   => 'add'
                    ))
        );
        
        $this->setChild('fillout_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('aitmanufacturers')->__('Fill Out '.$attrName.' Pages'),
                    'onclick'   => "setLocation('".$this->getUrl('*/*/fillOut', array('store' => $this->getStoreId(), 'attributecode' => $attributeCode))."')",
                    'class'   => ''
                    ))
        );
        
         $this->setChild('back_select',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('aitmanufacturers')->__('Return to Attribute Select'),
                    'onclick'   => "setLocation('".$this->getUrl('*/*/attribute', array('store' => $this->getStoreId()))."')",
                    'class'   => 'back'
                    ))
        );
        $this->setChild('grid', $this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_grid', 'aitmanufacturers.grid'));
        return parent::_prepareLayout();
    }

    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_new_button');
    }

    public function getFillOutButtonHtml()
    {
        return $this->getChildHtml('fillout_button');
    }
    
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_select');
    }
    
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
               return false;
        }
        return true;
    }
}