<?php

/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Block_Adminhtml_Area_Grid extends Mage_Adminhtml_Block_Widget_Grid {

  
  public function __construct(){
      parent::__construct();
      $this->setId('shippingGrid');
      $this->setDefaultSort('shipping_area_id');
      $this->setDefaultDir('ASC');
	   $this->setSaveParametersInSession(true);       
      
  }

	 protected function _prepareCollection(){
	      $collection = Mage::getModel('tatvashipping/area')->getCollection();
	
	      $this->setCollection($collection);
	      parent::_prepareCollection();
	      
	      $this->getCollection()->addCountriesToResult();
	      return $this;
	  }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', 
        	array(
        		'shipping_area_id' => $row->getId()
        	));
    }
    
	protected function _addColumnFilterToCollection($column) {
		if ($this->getCollection ()) {
			if ($column->getId () == 'countries') {
				$countriesIds = $column->getFilter ()->getCondition ();
				$this->getCollection ()->joinCountries ( $countriesIds );
				return $this;
			}
		}
		return parent::_addColumnFilterToCollection ( $column );
	}
  /**
   * Préparation des colonnes
   *
   */ 
  protected function _prepareColumns() {
      $this->addColumn('area_code', array(
          'header'    => Mage::helper('tatvashipping')->__('Code'),
          'width'     => '50px',
          'index'     => 'area_code',
      ));
      $this->addColumn('area_label', array(
          'header'    => Mage::helper('tatvashipping')->__('Label'),
      		'width'     => '50px',
          'index'     => 'area_label',
      ));
     
	 $this->addColumn ( 'countries', 
		array (
				'header' => Mage::helper ( 'tatvashipping' )->__ ( 'Countries' ),
				'width' => '60px', 
				'sortable' => false, 
				'index' => 'countries', 
				'type' => 'options', 
				'options' =>$this->countriesToOptionArray()
		));
	  		
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
  
    
}