<?php
class Tatva_Adminhtml_Block_Catalog_Product_Renderer_Marginrate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
	{
		if($row->getTypeId() == 'bundle')
        {
        	$storeId = (int) $this->getRequest()->getParam('store', 0);
			$bundleId = $row->getId();

			$totalPrice = 0.00;
			$totalLsp = 0.00;

			$bProdu = Mage::getModel('catalog/product')->setStoreId($storeId)->load($bundleId);
			$collection = $bProdu->getTypeInstance(true)->getSelectionsCollection($bProdu->getTypeInstance(true)->getOptionsIds($bProdu), $bProdu);
			if(count($collection))
            {
				foreach ($collection as $item)
                {
					$prodId = $item->getId();
					$asProduct = Mage::getModel('catalog/product')->setStoreId($storeId)->load($prodId);

					$totalPrice += $asProduct->getPrice();
					$totalLsp += $asProduct->getLowestSupplierPrice2();
				}
			}

			if($totalPrice > 0){
				$newTotalMprice = ($totalPrice - $totalLsp)/$totalPrice;
				$newTotalMprice = $newTotalMprice * 100;
				return number_format($newTotalMprice,2).'%';
			}
			return null;


		}
        else
        {
			return number_format($row->getCustomMarginRate(),2).'%';
		}
	}
}


	
?>
