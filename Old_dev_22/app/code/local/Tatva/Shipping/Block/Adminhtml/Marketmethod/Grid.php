<?php

/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Block_Adminhtml_Marketmethod_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('shippingGrid');
        $this->setDefaultSort('shipping_marketmethod_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);

    }

	protected function _prepareCollection()
    {
	    $collection = Mage::getModel('tatvashipping/marketmethod')->getCollection();
	    $this->setCollection($collection);
	    parent::_prepareCollection();
	    return $this;
	}

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('shipping_marketmethod_id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('shipping_marketmethod_id');
        $this->getMassactionBlock()->setFormFieldName('shipping_marketmethod_id');

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
    protected function _prepareColumns()
    {

      /*$this->addColumn('shipping_marketmethod_id', array(
          'header'    => Mage::helper('tatvashipping')->__('MarketPlace ID'),
          'align'     =>'right',
          'width'     => '150px',
          'index'     => 'shipping_marketmethod_id',
      ));*/

      $this->addColumn('shipping_code_amazon', array(
          'header'    => Mage::helper('tatvashipping')->__('Shipping Amazone %'),
          'width'     => '40px',
          'index'     => 'shipping_code_amazon',
      ));

      $this->addColumn('shipping_code_ebay', array(
          'header'    => Mage::helper('tatvashipping')->__('Shipping Ebay %'),
      		'width'     => '40px',
          'index'     => 'shipping_code_ebay',
      ));

      $this->addColumn('market_weight_from', array(
          'header'    => Mage::helper('tatvashipping')->__('Weight From'),
      		'width'     => '20px',
          'index'     => 'market_weight_from',
      ));

      $this->addColumn('market_weight_to', array(
          'header'    => Mage::helper('tatvashipping')->__('Weight To'),
      		'width'     => '20px',
          'index'     => 'market_weight_to',
      ));

      $this->addColumn('market_ordertotal_from', array(
          'header'    => Mage::helper('tatvashipping')->__('Order Total From'),
      		'width'     => '40px',
          'index'     => 'market_ordertotal_from',
      ));

      $this->addColumn('market_ordertotal_to', array(
          'header'    => Mage::helper('tatvashipping')->__('Order Total To'),
      		'width'     => '40px',
          'index'     => 'market_ordertotal_to',
      ));

      $this->addColumn('market_shipping_code', array(
          'header'    => Mage::helper('tatvashipping')->__('MarketPlace Method'),
      	  'width'     => '50px',
          'index'     => 'market_shipping_code',
          'type' => 'options',
		  'options' => $this->getShippingMethodsName()
      ));

      $this->addColumn('countries_ids', array(
          'header'    => Mage::helper('tatvashipping')->__('Countries'),
      	  'width'     => '50px',
          'index'     => 'countries_ids'
          //'type' => 'options',
		  //'options' => $this->countriesToOptionArray()
      ));

	  $this->addExportType('*/*/exportCsv', Mage::helper('advice')->__('CSV'));

      return parent::_prepareColumns();
    }

    private function countriesToOptionArray()
    {
    	$countries = Mage::getModel('directory/country')->getCollection();

        foreach ($countries as $data) {
            $name = Mage::app()->getLocale()->getCountryTranslation($data['country_id']);
            if (!empty($name)) {
                $sort[$name] = $data['country_id'];
            }
        }
        ksort($sort);
        $options = array();
        foreach ($sort as $label=>$value) {
            $options["'".$value."'"] = $label;
        }
        $options['.'] = '...';
        return $options;
    }

    public function getShippingMethodsName()
    {
        $method_array = array();
        $methods = mage::helper('Orderpreparation/ShippingMethods')->getArray();
        foreach($methods as $key => $label)
        {
            $method_array[$key] = $key;
        }

        return $method_array;
    }

}