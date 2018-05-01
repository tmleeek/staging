<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_Bbw extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $product)
    {
        $html = '-';
        if ($product->getbest_price() > 0) {
            $html = $product->getbbw_name().'<br />'.$product->getbbw_price();
        }

        return $html;
    }

}