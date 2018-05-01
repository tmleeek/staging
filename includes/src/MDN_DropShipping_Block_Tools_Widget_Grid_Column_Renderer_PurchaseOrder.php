<?php

class MDN_DropShipping_Block_Tools_Widget_Grid_Column_Renderer_PurchaseOrder
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    /**
     * get the increment id of the PO + link
     * 
     * @param Varien_Object $row
     * @return type 
     */
    public function render(Varien_Object $row)
    {
    	
        $html = '';

        $purchaseOrder = Mage::getModel("Purchase/Order")->load( $row->getdsposl_purchase_order_id() );

        $url = Mage::Helper('adminhtml')->getUrl('Purchase/Orders/Edit', array('po_num' => $row->getdsposl_purchase_order_id() ));
        
        $html = '<span><a href="'.$url.'" title="purchaseOrder" id="purchaseOrder_'.$row->getdsposl_purchase_order_id().'" name="purchaseOrder_'.$row->getdsposl_purchase_order_id().'">#'.$purchaseOrder->getpo_order_id().'</span>';
        
        return $html;
        
    }
    
}