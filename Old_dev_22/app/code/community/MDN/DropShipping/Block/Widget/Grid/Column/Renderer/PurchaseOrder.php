<?php

class MDN_DropShipping_Block_Widget_Grid_Column_Renderer_PurchaseOrder extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        
        //get purchase orderS
        $orderId = $row->getId();
        $purchaseOrders = Mage::helper('DropShipping/Order')->getAssociatedPurchaseOrders($orderId);
        
        $pos = array();
        foreach($purchaseOrders as $po)
        {
            $url = Mage::helper('adminhtml')->getUrl('Purchase/Orders/Edit', array('po_num' => $po->getId()));
            $pos[] = '<a href="'.$url.'">'.$po->getpo_order_id().'</a>';
        }
        
        return implode('<br>',  $pos);
    }

}