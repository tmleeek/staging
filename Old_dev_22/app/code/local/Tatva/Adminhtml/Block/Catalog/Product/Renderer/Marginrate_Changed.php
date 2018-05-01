<?php
class Tatva_Adminhtml_Block_Catalog_Product_Renderer_Marginrate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
        public function render(Varien_Object $row)
	{
		
		if($row->getLowestSupplierPrice1() == 0)
		{
			return '00,00 &euro;';
		}
            //echo "<pre>";print_r($row->getData());die();
		else{
            $productprice = $row->getPrice();
            $pps_last_price = $row->getLowestSupplierPrice1(); 
            $finalformulaprice = ($productprice - $pps_last_price) / $productprice * 100;
            $finalformulaprice = number_format($finalformulaprice,2);			
			//$finalformulaprice = str_replace(".",",",$finalformulaprice." &euro;");
			if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) // store level
			{
				$store_id = Mage::getModel('core/store')->load($code)->getId();
			}
			elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) // website level
			{
				$website_id = Mage::getModel('core/website')->load($code)->getId();
				$store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
			}
			else // default level
			{
				$store_id = 0;
			}
			//return $store_id;
			return $finalformulaprice.' '.Mage::app()->getLocale()->currency(Mage::app()->getStore($store_id)->getCurrentCurrencyCode())->getSymbol();
		}	

	}
}

	
	
?>
