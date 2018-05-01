<?php
class Tatva_Adminhtml_Block_Catalog_Product_Renderer_Lowestprice extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
        public function render(Varien_Object $row)
	{
            
            if($row->getLowestSupplierPrice1() == 0)
            {
                $pps_last_price = '00,00 &euro;';
            }
            else
            {
                $pps_last_price = sprintf('%0.2f',$row->getLowestSupplierPrice1());
                $pps_last_price = str_replace(".",",",$pps_last_price." &euro;"); 
                
            }
            return $pps_last_price;
            
	}
}

	
	
?>
