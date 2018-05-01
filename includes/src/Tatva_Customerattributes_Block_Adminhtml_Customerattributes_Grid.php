<?php

class Tatva_Customerattributes_Block_Adminhtml_Customerattributes_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
  protected function _prepareCollection()
  {
      $collection = Mage::getResourceModel('customer/attribute_collection');
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('attribute_code', array(
          'header'    => Mage::helper('customer')->__('Attribute Code'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'attribute_code',
          'align' => 'left',
      ));

      $this->addColumn('frontend_label', array(
          'header'    => Mage::helper('customer')->__('Attribute Label'),
          'align'     =>'left',
          'index'     => 'frontend_label',
          'align' => 'left',
      ));

      $this->addColumn('is_required', array(
          'header'    => Mage::helper('customer')->__('Required'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'is_required',
          'type'      => 'options',
          'options'   => array(
              1 => Mage::helper('customer')->__('Yes'),
              0 => Mage::helper('customer')->__('No'),
          ),
          'align' => 'center',
      ));

      $this->addColumn('is_user_defined', array(
          'header'    => Mage::helper('customer')->__('System'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'is_user_defined',
          'type'      => 'options',
          'options'   => array(
              1 => Mage::helper('customer')->__('No'),
              0 => Mage::helper('customer')->__('Yes'),
          ),
          'align' => 'center',
      ));

	  return $this;
  }

}