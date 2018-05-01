<?php

class MDN_DropShipping_Block_Widget_Grid_Column_Renderer_OrderItemPo extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        
        //get purchase orderS
        $orderId = $row->getpurchase_order_id();
        if ($orderId)
        {
            $po = Mage::getModel('Purchase/Order')->load($orderId);
            $url = Mage::helper('adminhtml')->getUrl('Purchase/Orders/Edit', array('po_num' => $po->getId()));
            $pos[] = '<a href="'.$url.'">'.$po->getpo_order_id().'</a> - '.$po->getSupplier()->getsup_name().' - '.$po->getpo_status();

            return implode('<br>',  $pos);
        }
    }

}