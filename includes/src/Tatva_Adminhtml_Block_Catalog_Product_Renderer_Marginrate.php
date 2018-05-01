<?php
class Tatva_Adminhtml_Block_Catalog_Product_Renderer_Marginrate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
        public function render(Varien_Object $row)
	{
            //echo "<pre>";print_r($row->getData());die();
            $productprice = number_format($row->getPrice(),2);
            $pps_last_price = $row->getLowestSupplierPrice1(); 
            $finalformulaprice = ($productprice - $pps_last_price) / $productprice * 100;
            return number_format($finalformulaprice,2);

	}
}

	
	
?>
