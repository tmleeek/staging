<?php

/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Block_Adminhtml_System_Config_FreeShippingSubtotal
    extends Tatva_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	protected $_countries = null;
		
    
    public function __construct()
    {
        $this->addColumn('countries', array(
            'label' => Mage::helper('tatvashipping')->__('Countries'),
            'style' => 'width:150px',
        	'type' => 'multiselect',
        	'values' => $this->countriesToOptionHtml(),
        ));
        $this->addColumn('value', array(
            'label' => Mage::helper('tatvashipping')->__('Amount'),
            'style' => 'width:60px',
        	'type' => 'text'
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('tatvashipping')->__('Add New Rule');
        parent::__construct();
    }
    
    private function countriesToOptionHtml()
    {
    	$allCountries = Mage::getModel('directory/country')->getCollection();
  		$sort = array();
        foreach ($allCountries as $data) {
            $name = Mage::app()->getLocale()->getCountryTranslation($data['country_id']);
            if (!empty($name)) {
                $sort[$name] = $data['country_id'];
            }
        }
        ksort($sort);
        $result = array();
        foreach ($sort as $label=>$value) {
        	$result[] = array(
        		'value'=>$value ,
        		'label'=> $label
        	);
        }
        return $result;
    }

}