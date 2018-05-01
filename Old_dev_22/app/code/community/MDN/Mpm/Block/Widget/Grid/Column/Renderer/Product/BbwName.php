<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_BbwName extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $product)
    {
        $bbwName = $product->bbw_name;

        return empty($bbwName) && !empty(json_decode($product->debug)->adjustment->bbw) ?
            json_decode($product->debug)->adjustment->bbw :
            $product->bbw_name;
    }

}