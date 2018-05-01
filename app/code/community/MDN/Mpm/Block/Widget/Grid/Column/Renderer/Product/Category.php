<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_Category extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $product)
    {
        return $product->category;
    }

    public function renderExport(Varien_Object $row)
    {
        $html = $this->render($row);
        return str_replace('<br>', ', ', $html);
    }

}