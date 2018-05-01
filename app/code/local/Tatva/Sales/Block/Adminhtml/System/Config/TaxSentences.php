<?php

class Tatva_Sales_Block_Adminhtml_System_Config_TaxSentences extends Tatva_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	protected $_areas = null;

	public function __construct() {
		$this->addColumn('areas', array(
			'label' => Mage::helper('tatvasales')->__('Areas'),
			'style' => 'width:150px',
			'type' => 'multiselect',
			'values' => $this->areasToOptionHtml(),
		));
		$this->addColumn('sentence', array(
			'label' => Mage::helper('tatvasales')->__('Sentence'),
			'style' => 'width:200px',
			'type' => 'text'
		));
		$this->_addAfter = false;
		$this->_addButtonLabel = Mage::helper('tatvasales')->__('Add New Sentence');
		parent::__construct();
	}

	/**
	 * Return list of shipping areas
	 * @return array
	 */
	private function areasToOptionHtml() {
		$areas = Mage::getModel('tatvashipping/area')->getCollection();

		$result = array();
		foreach ($areas as $area) {
			$result [] = array('value'=>$area->getAreaId(),'label'=>$area->getAreaLabel());
		}
		return $result;
	}
}

?>