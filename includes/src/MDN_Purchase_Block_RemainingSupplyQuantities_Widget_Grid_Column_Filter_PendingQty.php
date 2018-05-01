<?php

class MDN_Purchase_Block_RemainingSupplyQuantities_Widget_Grid_Column_Filter_PendingQty extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
	protected function _getOptions()
    {
        $retour = array();
        $retour[] = array('label' => '', 'value' => '');
        $retour[] = array('label' => $this->__('Greater than 0'), 'value' => 'greater_than_zero');
        return $retour;
    }	
    
    public function getCondition()
    {
    	$searchString = $this->getValue();
    	if ($searchString == '')
    		return;
    		
    	//create array with pending qty > 0
    	$productIds = array();
    	$model = mage::getResourceModel('cataloginventory/stock_item_collection');
    	$sql = $model->getSelect()
    				->where("stock_ordered_qty_for_valid_orders > qty");
    	$collection = $model->getConnection()->fetchAll($sql);
		foreach ($collection as $item)
		{
			$productIds[] = $item['product_id'];
		}
    	
    	return array('in' => $productIds);
    }
    
}