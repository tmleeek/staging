<?php

class Tatva_Video_Model_Mysql4_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('tatvavideo/item');
    }
    

    public function addProductIdFilter($id){
    	$this->getSelect()->where('product_id = ?',$id);
    	return $this;
    }
	
	public function addOrderFilter(){
    	$this->getSelect()->order('video_item_id');
    	return $this;
    }



}
