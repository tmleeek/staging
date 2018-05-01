<?php

class MDN_DropShipping_Block_Widget_Grid_Column_Renderer_PoLink extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $po) {

        return '<a href="' . Mage::helper('adminhtml')->getUrl('Purchase/Orders/Edit', array('po_num' => $po->getId())) . '">' . $po->getpo_order_id() . '</a>';
    }

}