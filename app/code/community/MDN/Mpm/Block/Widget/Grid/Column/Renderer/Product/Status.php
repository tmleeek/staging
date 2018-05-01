<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $imageUrl = Mage::getSingleton('Mpm/System_Config_PricingStatus')->getSmileyUrl($row->getStatus());
        return '<img src="'.$imageUrl.'" width="24">';

        return $html;
    }

    public function renderExport(Varien_Object $row)
    {
        return $row->getStatus();
    }

}