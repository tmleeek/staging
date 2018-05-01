<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_Separator extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function renderCss()
    {
        $css = parent::renderCss();
        $css .= ' separator_column';
        return $css;
    }

    public function renderExport(Varien_Object $row)
    {
        return "";
    }

}