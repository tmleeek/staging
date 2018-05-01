<?php

class Tatva_Video_Model_Mysql4_Item extends Mage_Core_Model_Mysql4_Abstract
{
	
	/**
	 * Constructor
	 */
    protected function _construct()
    {
        $this->_init('tatvavideo/video_item', 'video_item_id');
    }
    
    /**
     * Retourne la somme des stocks courants d'un produit
     * @param $productId
     * @return int
     */
    public function sumCurrentVideo($productId){
    	$select = $this->_getReadAdapter ()
    		->select ()
    		->from(array('item'=>$this->getMainTable()),'SUM(current_stock) as sum')
      		->where('product_id = ?',$productId);

      	$data = $this->_getReadAdapter()->fetchRow($select);
      	if($data && sizeof($data)>0){
      		return $data['sum'];
      	}
      	return false;
    }
}

?>