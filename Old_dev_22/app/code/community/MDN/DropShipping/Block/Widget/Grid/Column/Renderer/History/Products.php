<?php

class MDN_DropShipping_Block_Widget_Grid_Column_Renderer_History_Products extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        
        $html = array();
        $collection = Mage::getModel('Purchase/OrderProduct')->getCollection()->addFieldToFilter('pop_order_num', $row->getpo_num());
        foreach($collection as $item)
        {
            $html[] = $item->getpop_qty().'x '.$item->getpop_product_name();
        }
        
        return implode('<br>', $html);
        
    }

}