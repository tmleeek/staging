<?php

class MDN_DropShipping_Block_Widget_Grid_Column_Renderer_SalesOrder extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $po) {
    
        $html = '';
        $orderItems = Mage::helper('DropShipping/Order')->getAssociatedOrderItems($po->getId());
        $processedOrderIds = array();
        foreach($orderItems as $orderItem)
        {
            if (in_array($orderItem->getorder_id(), $processedOrderIds))
                    continue;
            
            $order = $orderItem->getOrder();
            $url = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view', array('order_id' => $order->getId()));
            $html .= '<a href="'.$url.'">'.$order->getincrement_id().'</a> ('.$order->getCustomerName().')';
            
            $processedOrderIds[] = $order->getId();
        }

        return $html;
    }

}