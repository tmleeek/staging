<?php

class MDN_DropShipping_Block_Tools_Widget_Grid_Column_Renderer_Order
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    /**
     * get the increment id of the Order + link
     * 
     * @param Varien_Object $row
     * @return type 
     */
    public function render(Varien_Object $row)
    {
    	
        $html = '';

        $order = Mage::getModel("Sales/Order")->load( $row->getdsposl_sales_order_id() );

        $url = Mage::Helper('adminhtml')->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getdsposl_sales_order_id() ));
        
        $html = '<span><a href="'.$url.'" title="Order" id="Order_'.$row->getdsposl_purchase_order_id().'" name="Order_'.$row->getdsposl_purchase_order_id().'">#'.$order->getincrement_id().'</span>';

        return $html;
        
    }
    
}