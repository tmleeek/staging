<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_MatchingStatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $product)
    {
        return $product->matching_status;
    }
}