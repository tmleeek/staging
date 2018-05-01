<?php

class MDN_DropShipping_Block_Widget_Grid_Column_Renderer_OrderLink extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $order) {
        return '<a href="' . Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view', array('order_id' => $order->getId())) . '">' . $order->getincrement_id() . '</a>';
    }

}