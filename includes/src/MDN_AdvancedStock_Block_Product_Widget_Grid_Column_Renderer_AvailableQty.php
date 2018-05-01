<?php

class MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Renderer_AvailableQty
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
		$retour = $row->getAvailableQty();		
		if ($retour == 0)
			$retour = '0';
		return $retour;
    }
    
}