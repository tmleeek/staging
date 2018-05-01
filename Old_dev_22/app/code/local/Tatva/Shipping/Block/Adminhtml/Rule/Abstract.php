<?php


/**
 *
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Block_Adminhtml_Rule_Abstract extends Mage_Adminhtml_Block_Widget_Grid {

    
  public function __construct(){
      parent::__construct();
      $this->setId('shippingGrid');
      $this->setDefaultDir('ASC');
  }

	protected function _addColumnFilterToCollection($column) {
		if ($this->getCollection ()) {
			if ($column->getId () == 'areas') {
				$areasIds = $column->getFilter ()->getCondition ();
				$this->getCollection ()->joinAreas ( $areasIds );
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
     /* $this->addColumn('shipping_rule_id', array(
          'header'    => Mage::helper('tatvashipping')->__('ID'),
          'width'     => '50px',
          'index'     => 'shipping_rule_id',
      ));*/
      $this->addColumn('weight_min', array(
          'header'    => Mage::helper('tatvashipping')->__('Minimum weight'),
       	  'type' 	  => 'number',
          'index'     => 'weight_min',
      ));
      $this->addColumn('weight_max', array(
          'header'    => Mage::helper('tatvashipping')->__('Maximum weight'),
       	  'type' 	  => 'number',
          'index'     => 'weight_max',
      ));
      
      $this->addColumn('amount', array(
          'header'    => Mage::helper('tatvashipping')->__('Amount'),
       	  'type' 	  => 'number',
          'index'     => 'amount',
      ));
     
	 $this->addColumn ( 'areas', 
		array (
				'header' => Mage::helper ( 'tatvashipping' )->__ ( 'Areas' ), 
				'width' => '6px', 
				'sortable' => false, 
				'index' => 'areas', 
				'type' => 'options', 
				'options' =>$this->areasToOptionArray()
		));
	  		
      return parent::_prepareColumns();
  }
  
    private function areasToOptionArray()
    {
	  	$options = array();
	  	$collection = Mage::getModel('tatvashipping/area')->getCollection();
	  	foreach($collection as $area){
	  		$options[$area->getId()] = $area->getAreaLabel();
	  	}
	  	return $options;
    }
  
    
}