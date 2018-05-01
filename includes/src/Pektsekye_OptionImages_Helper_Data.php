<?php

class Pektsekye_OptionImages_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_product;  
	
    /**
     * Retrieve product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
			if ($product = Mage::registry('current_product')) {
                $this->_product = $product;				
            } elseif ($product = Mage::registry('product')) {
                $this->_product = $product;
            } else {
                $this->_product = Mage::getSingleton('catalog/product');
            }
        }		
        return $this->_product;
    }	
	
	
    /**
     * Get product options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->getProduct()->getOptions();
    }
	
	
	
    public function getOptionImages()
    { 
		$storeId = Mage::app()->getStore()->getId();
		$resource = Mage::getSingleton('core/resource'); 
		$read= $resource->getConnection('core_read');
		
        $optionIds = array();
        foreach ($this->getOptions() as $option){
			$optionIds[] = $option->getId();
        }
		

		$values = Mage::getModel('catalog/product_option_value')
		->getCollection()
		->addOptionToFilter($optionIds);	
		$values->getSelect()
		->join(array('default_value_image'=>$resource->getTableName('optionimages/product_option_type_image')),
			'`default_value_image`.option_type_id=`main_table`.option_type_id AND `default_value_image`.image != \'\' AND '.$read->quoteInto('`default_value_image`.store_id=?',0),
			array('default_image'=>'image'))
		->joinLeft(array('store_value_image'=>$resource->getTableName('optionimages/product_option_type_image')),
			'`store_value_image`.option_type_id=`main_table`.option_type_id AND `store_value_image`.image != \'\' AND '.$read->quoteInto('`store_value_image`.store_id=?',$storeId),
			array('store_image'=>'image','image'=>new Zend_Db_Expr('IFNULL(`store_value_image`.image,`default_value_image`.image)')));	

        return $values;
    }

}