<?php

class MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Renderer_Barcode
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
		$productId = $row->getId();
		$retour = mage::helper('AdvancedStock/Product_Barcode')->getBarcodeForProduct($productId);
		return $retour;
    }
    
}