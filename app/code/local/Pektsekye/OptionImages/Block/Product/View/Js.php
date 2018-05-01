<?php
class Pektsekye_OptionImages_Block_Product_View_Js extends  Mage_Core_Block_Template
{
    public function getOptionImages()
    { 
		$config = array();
		$mediaconfig = Mage::getSingleton('catalog/product_media_config');
		$helper = Mage::helper('optionimages');		
			 
		foreach ($helper->getOptionImages() as $value) {
				$config[$value->getOption_id()][$value->getId()] = $mediaconfig->getMediaUrl($value->getImage());
		}	


        return Zend_Json::encode($config);
    }
	
}