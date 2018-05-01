<?php
class Tatva_Adminhtml_Block_Catalog_Product_Renderer_Lowestprice extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
	{

            /*if($row->getLowestSupplierPrice1() == 0)
            {
                $pps_last_price = '00,00 &euro;';
            }
            else
            {
                $pps_last_price = sprintf('%0.2f',$row->getLowestSupplierPrice1());
                $pps_last_price = str_replace(".",",",$pps_last_price." &euro;");

            }
            return $pps_last_price;*/

        if($row->getTypeId() == 'bundle')
        {

			$storeId = (int) $this->getRequest()->getParam('store', 0);
			$bundleId = $row->getId();

			$totalLsp = 0.00;

			$bProdu = Mage::getModel('catalog/product')->setStoreId($storeId)->load($bundleId);
			$collection = $bProdu->getTypeInstance(true)->getSelectionsCollection($bProdu->getTypeInstance(true)->getOptionsIds($bProdu), $bProdu);
			if(count($collection))
            {
				foreach ($collection as $item)
                {
					$prodId = $item->getId();
					$asProduct = Mage::getModel('catalog/product')->setStoreId($storeId)->load($prodId);

				   $totalLsp += $asProduct->getLowestSupplierPrice2();
				}
			}

			if($totalLsp > 0)
            {
                return number_format($totalLsp,2);
			}
			return null;


		}
        else
        {
			return $row->getLowestSupplierPrice2();
		}

	}
}



?>
