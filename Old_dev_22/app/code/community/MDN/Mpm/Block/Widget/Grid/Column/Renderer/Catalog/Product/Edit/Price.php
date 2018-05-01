<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Catalog_Product_Edit_Price extends
    Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $currencySymbol  =  Mage::helper('Mpm/Pricing')->getCurrency($row->getchannel());
        $value =  $row->getData($this->getColumn()->getIndex());
        return $currencySymbol . ' ' . $value;

    }

}