<?php

class Tatva_Customerattributes_Model_Customerattributes extends Mage_Eav_Model_Entity_Attribute
{
    const CUSTOMER_YES    = 1;
    const CUSTOMER_NO   = 0;
    public function _construct()
    {
        parent::_construct();
        $this->_init('customerattributes/customerattributes');
    }
    public function toOptionArray()
    {
           $attributes = Mage::getResourceModel('customer/attribute_collection')
                        ->addFieldToFilter('is_user_defined',array("eq" => 1));
         
           foreach($attributes as $_attribute)
           {
                $collection[]=array('label'=>$_attribute->getName(),'value'=>$_attribute->getId());
           }
		  //print_r ($collection);exit;	
          return $collection;
    }
    public function getOption($i)
    {
        $attribute1 = Mage::getModel('catalog/resource_eav_attribute')->load($i);
        $attribute_code = Mage::getSingleton('eav/config')->getAttribute('customer', $attribute1->getAttributeCode());
        $prodCodeOptions = $attribute_code->getSource()->getAllOptions(false);
        $prodOptions = array();
        foreach($prodCodeOptions as $k)
            $prodOptions[$k['value']] = $k['label'];
        return $prodOptions;
    }
    static public function getOptionArray()
    {
        return array(
            self::CUSTOMER_YES    => Mage::helper('catalog')->__('Yes'),
            self::CUSTOMER_NO   => Mage::helper('catalog')->__('No')
        );
    }

}