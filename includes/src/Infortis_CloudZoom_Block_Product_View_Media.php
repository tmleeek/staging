<?php

class Infortis_CloudZoom_Block_Product_View_Media extends Mage_Catalog_Block_Product_View_Media
{

    public function getVideos()
    {

        $collection = Mage::getModel('tatvavideo/item')
    		->getCollection()
    		->addProductIdFilter($this->getProduct()->getId())
			->addOrderFilter();
        return $collection;
    }



   
}
