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
class Aitoc_Aitmanufacturers_Block_Adminhtml_Attribute_Edit extends Mage_Adminhtml_Block_Widget_Form
{
    var $_attributeCode;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitmanufacturers/attribute/edit.phtml');
        if (Mage::registry('entity_attribute')->getAttributeCode())
	    {
	        $this->setAttributeCode(Mage::registry('entity_attribute')->getAttributeCode());
	    }
    }
    
    protected function _prepareLayout()
    {
        $this->setChild('attribute_store_switch', $this->getLayout()->createBlock('aitmanufacturers/adminhtml_attribute_edit_switcher'));
        
    }
    
    protected function _prepareForm()
    {
      
	  $this->setForm($this->getBlankForm());
	  $storeId = $this->getStoreId();
     
      return parent::_prepareForm();
  }

  public function getFormValues($scope = 'default', $id = 0)
  {
      $returnValues = array();
      $values = Mage::getModel('aitmanufacturers/config')->getScopeConfig($this->getAttributeCode(), $scope, $id, true, true);
      if ($values)
      {
         // $returnValues[$scope.'_'.$id.'_is_active'] = true;
          //$returnValues[$scope.'_'.$id.'_use_default'] = false;
          foreach ($values as $key=>$val)
          {
              $returnValues[$scope.'_'.$id.'_'.$key] = $val;
          }
      }

      return $returnValues;
  }
  
  
  public function getBlankForm($scope = 'default', $id = 0)
  {
      $form = new Varien_Data_Form(array('method' => 'post'));
      //$form->setUseContainer(true);
      
      if ($scope == 'default') {
         $fieldset = $form->addFieldset($scope.'_'.$id.'_aitmanufacturers_config', array('legend'=>Mage::helper('aitmanufacturers')->__('Shop By Attribute')));
      } else {
         $fieldset = $form->addFieldset($scope.'_'.$id.'_aitmanufacturers_config', array()); 
      }
      

      $arrYesNoDrop = array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('aitmanufacturers')->__('No'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('aitmanufacturers')->__('Yes'),
              ),
          );
         
      if ($scope != 'default')
      {
          $fieldset->addField($scope.'_'.$id.'_use_default', 'select', array(
              'label'     => Mage::helper('aitmanufacturers')->__('Use Default Settings'),
              'note'      => 'Use settings of the higher scope',
              'required'  => false,
              'name'      => 'config['.$scope.'_'.$id.'][use_default]',
              'values'    => $arrYesNoDrop,
              'onchange'  => "StoreConfigSwitch.switchDefaultConfig('".$scope."_".$id."');",
              'value'     => 1,
          ));

          $fieldset = $fieldset->addFieldset($scope.'_'.$id.'_aitmanufacturers_activate_form', array('class' => 'hidden'));
      }     
           
      $fieldset->addField($scope.'_'.$id.'_is_active', 'select', array(
              'label'     => Mage::helper('aitmanufacturers')->__('Activate Shop By'),
              'note'      => 'Enables "Shop By Attribute" functionality for this attribute',
              'required'  => false,
              'name'      => 'config['.$scope.'_'.$id.'][is_active]',
              'values'    => $arrYesNoDrop,
              'onchange'  => "StoreConfigSwitch.switchActiveConfig('".$scope."_".$id."');"
      ));
      	  
      $fieldset = $fieldset->addFieldset($scope.'_'.$id.'_aitmanufacturers_form', array('class' => 'hidden'));
      
      if ($this->getAttributeCode())
      {
          $fieldset->addField($scope.'_'.$id.'_attribute_code', 'hidden', array(
              'name'      => 'config['.$scope.'_'.$id.'][attribute_code]',
              'value'     => $this->getAttributeCode(),
          ));
      }
      
      $fieldset->addField($scope.'_'.$id.'_url_prefix', 'text', array(
          'label'     => Mage::helper('aitmanufacturers')->__('URL Key'),
          'note'  =>  'eg: example.com/'.$this->getAttributeCode().'/',
          'required'  => true, 
          'value'     => $this->getAttributeCode(),
          'name'      => 'config['.$scope.'_'.$id.'][url_prefix]',
          
      ));
      $fieldset->addField($scope.'_'.$id.'_url_pattern', 'text', array(
          'label'     => Mage::helper('aitmanufacturers')->__('URL Pattern'),
          'note'  =>  "Note!: [attribute].html is used by default. [attribute] - is a mandatory part of pattern. Do not use special symbols in pattern else your attributes pages won't be displayed. The pattern will be used in this way - SiteName.com/brands/pattern ex: SiteName.com/brands/mybrand.html",
          'required'  => true,
          'value'     => '[attribute].html',
          'name'      => 'config['.$scope.'_'.$id.'][url_pattern]',
      ));
      $fieldset->addField($scope.'_'.$id.'_columns_num', 'text', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Number of Columns'),
          'note'  =>  "At the 'All Attributes' page",
          'required'  => true,
          'name'      => 'config['.$scope.'_'.$id.'][columns_num]',
          'value'     => 3
      ));
      $fieldset->addField($scope.'_'.$id.'_brief_num', 'text', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Number of Attributes'),
          'note'  =>  'Number of attributes in the sidebar block. Set 0 to display all',
          'required'  => true,
          'name'      => 'config['.$scope.'_'.$id.'][brief_num]',
          'value'     => 7
      ));
	  
      $fieldset->addField($scope.'_'.$id.'_show_brands_withproducts_only', 'select', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Show Attributes with Products Only'),
          'required'  => false,
          'name'      => 'config['.$scope.'_'.$id.'][show_brands_withproducts_only]',
          'values'    => $arrYesNoDrop,
          'value'     => 0
      ));
      $fieldset->addField($scope.'_'.$id.'_show_categories_as_tree', 'select', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Show Categories As Tree'),
          'required'  => false,
          'name'      => 'config['.$scope.'_'.$id.'][show_categories_as_tree]',
          'values'    => $arrYesNoDrop,
          'value'     => 0
      ));
      $fieldset->addField($scope.'_'.$id.'_show_brands_from_category_only', 'select', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Show Attributes From Current Category'),
          'note'  =>  'If set to "Yes", only attributes from the current category are displayed at the category view page.',
          'required'  => false,
          'name'      => 'config['.$scope.'_'.$id.'][show_brands_from_category_only]',
          'values'    => $arrYesNoDrop,
          'value'     => 0
      ));
      $fieldset->addField($scope.'_'.$id.'_show_logo', 'select', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Show Logo'),
          'note'  =>  'Show attribute logo on the product page',
          'required'  => false,
          'name'      => 'config['.$scope.'_'.$id.'][show_logo]',
          'values'    => $arrYesNoDrop,
          'value'     => 1
      ));
      $fieldset->addField($scope.'_'.$id.'_show_link', 'select', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Show Link'),
          'note'  =>  'Show Link "See other products by Attribute" on the product page',
          'required'  => false,
          'name'      => 'config['.$scope.'_'.$id.'][show_link]',
          'values'    => $arrYesNoDrop,
          'value'     => 1
      ));
      $fieldset->addField($scope.'_'.$id.'_show_brands_in_sitemap', 'select', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Show Attributes In Site Map'),
          'note'  =>  'Show Page "Attributes sitemap" in the site map',
          'required'  => false,
          'name'      => 'config['.$scope.'_'.$id.'][show_brands_in_sitemap]',
          'values'    => $arrYesNoDrop,
          'value'     => 1
      ));
      $fieldset->addField($scope.'_'.$id.'_show_brief_image', 'select', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Show Attributes Icons In Attributes Brief Block'),
          'note'  =>  'If uploaded show small icon with every attribute link in attributes brief sidebar block',
          'required'  => false,
          'name'      => 'config['.$scope.'_'.$id.'][show_brief_image]',
          'values'    => $arrYesNoDrop,
          'value'     => 1
      ));
      $fieldset->addField($scope.'_'.$id.'_show_list_image', 'select', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Show Attributes Icons On Attributes List Page'),
          'note'  =>  'If uploaded show small icon with every attribute link on attributes list page',
          'required'  => false,
          'name'      => 'config['.$scope.'_'.$id.'][show_list_image]',
          'values'    => $arrYesNoDrop,
          'value'     => 0
      ));
      $fieldset->addField($scope.'_'.$id.'_rename_pic', 'select', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Rename pics'),
          'note'  =>  'Rename uploaded pictures to attribute id',
          'required'  => false,
          'name'      => 'config['.$scope.'_'.$id.'][rename_pic]',
          'values'    => $arrYesNoDrop,
          'value'     => 1
      ));
      $fieldset->addField($scope.'_'.$id.'_layered_navigation', 'select', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Use Layered Navigation'),
          'note'  =>  'Shows up Layered Navigation on attributes page',
          'required'  => false,
          'name'      => 'config['.$scope.'_'.$id.'][layered_navigation]',
          'values'    => $arrYesNoDrop,
          'value'     => 1
      ));      

      
      $fieldset->addField($scope.'_'.$id.'_page_title', 'text', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Page Title'),
          'required'  => false,
          'name'      => 'config['.$scope.'_'.$id.'][page_title]',
          
      ));

      
      $fieldset->addField($scope.'_'.$id.'_meta_keywords', 'textarea', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Meta Keywords'),
          'required'  => false,
          'name'      => 'config['.$scope.'_'.$id.'][meta_keywords]',
          
      ));
      
      $fieldset->addField($scope.'_'.$id.'_meta_description', 'textarea', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Meta Description'),
          'required'  => false,
          'name'      => 'config['.$scope.'_'.$id.'][meta_description]',
          
      ));
      $fieldset->addField($scope.'_'.$id.'_include_in_navigation_menu', 'select', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Include In Navigation Menu'),
          'required'  => false,
          'name'      => 'config['.$scope.'_'.$id.'][include_in_navigation_menu]',
          'values'    => $arrYesNoDrop,
          'value'     => 0
      ));


      return $form;
  }
  
    
}