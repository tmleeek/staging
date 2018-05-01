<?php

class MDN_DropShipping_Block_Widget_Grid_Column_Renderer_Supplier extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        
        //get purchase orderS
        $orderId = $row->getId();
        $purchaseOrders = Mage::helper('DropShipping/Order')->getAssociatedPurchaseOrders($orderId);
        
        $suppliers = array();
        foreach($purchaseOrders as $po)
        {
            $suppliers[] = $po->getSupplier()->getsup_name();
        }
        
        return implode('<br>',  $suppliers);
    }

}