<?php

class MDN_DropShipping_Block_Widget_Grid_Column_Renderer_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        
        $purchaseOrder = Mage::getModel('Purchase/Order')->load($row->getdsposl_purchase_order_id());
        return $purchaseOrder->getpo_date();
    }

}