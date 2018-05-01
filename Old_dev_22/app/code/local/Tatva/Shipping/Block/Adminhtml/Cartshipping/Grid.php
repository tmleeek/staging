<?php

/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Block_Adminhtml_Cartshipping_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('shippingGrid');
        $this->setDefaultSort('shipping_apimethod_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);

    }

	protected function _prepareCollection()
    {
	    $collection = Mage::getModel('tatvashipping/apimethod')->getCollection();
	    $this->setCollection($collection);
	    parent::_prepareCollection();
	    return $this;
	}

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('shipping_apimethod_id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('shipping_apimethod_id');
        $this->getMassactionBlock()->setFormFieldName('shipping_apimethod_id');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('tatvashipping')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('tatvashipping')->__('Are you sure?')
        ));

        return $this;
    }

  /**
   * Préparation des colonnes
   *
   */
  protected function _prepareColumns() {

      $this->addColumn('shipping_apimethod_id', array(
          'header'    => Mage::helper('tatvashipping')->__('API ID'),
          'align'     =>'right',
          'width'     => '150px',
          'index'     => 'shipping_apimethod_id',
      ));

      $this->addColumn('shipping_method_name', array(
          'header'    => Mage::helper('tatvashipping')->__('shipping_method_name'),
          'width'     => '50px',
          'index'     => 'shipping_method_name',
      ));

      $this->addColumn('shipping_method_code', array(
          'header'    => Mage::helper('tatvashipping')->__('Shipping Method Code'),
      		'width'     => '50px',
          'index'     => 'shipping_method_code',
      ));

      $this->addColumn('api_shipping_code', array(
          'header'    => Mage::helper('tatvashipping')->__('API Method'),
      		'width'     => '50px',
          'index'     => 'api_shipping_code',
      ));

	  $this->addExportType('*/*/exportCsv', Mage::helper('advice')->__('CSV'));

      return parent::_prepareColumns();
  }

}