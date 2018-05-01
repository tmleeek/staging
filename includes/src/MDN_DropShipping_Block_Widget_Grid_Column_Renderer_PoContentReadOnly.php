<?php

class MDN_DropShipping_Block_Widget_Grid_Column_Renderer_PoContentReadOnly extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $po) {
        
        $html = '';
        foreach($po->GetProducts() as $poProduct)
        {
            $html .= $poProduct->getpop_qty().'x '.$poProduct->getpop_product_name().'<br>';
        }
        
        return $html;
    }

}