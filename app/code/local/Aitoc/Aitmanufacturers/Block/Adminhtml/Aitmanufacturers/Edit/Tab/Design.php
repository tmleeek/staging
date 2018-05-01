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

class Aitoc_Aitmanufacturers_Block_Adminhtml_Aitmanufacturers_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('aitmanufacturers_display', array('legend'=>Mage::helper('aitmanufacturers')->__('Custom Design')));
        
        $layouts = array();
        foreach (Mage::getConfig()->getNode('global/aitmanufacturers/layouts')->children() as $layoutName=>$layoutConfig) {
            //if ('empty' == $layoutName) continue;
            
            $layouts[$layoutName] = (string)$layoutConfig->label;
                
        }

        $fieldset->addField('root_template', 'select', array(
            'name'      => 'root_template',
            'label'     => Mage::helper('aitmanufacturers')->__('Layout'),
            'required'  => true,
            'options'   => $layouts,
        ));
        
        $fieldset->addField('layout_update_xml', 'editor', array(
            'name'      => 'layout_update_xml',
            'label'     => Mage::helper('aitmanufacturers')->__('Layout Update XML'),
            'style'     => 'height:24em;'
        ));

     
      if ( Mage::getSingleton('adminhtml/session')->getAitmanufacturersData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getAitmanufacturersData());
          Mage::getSingleton('adminhtml/session')->setAitmanufacturersData(null);
      } elseif ( Mage::registry('aitmanufacturers_data') ) {
          $form->setValues(Mage::registry('aitmanufacturers_data')->getData());
      }
      return parent::_prepareForm();
  }
}