<?php

define('MAGENTOROOT', dirname(__FILE__));
require_once(MAGENTOROOT.'/app/Mage.php');

$exporter = new UpdateLSP();


class UpdateLSP
{


	// Initialize the Mage application
	function __construct()
	{
		ini_set('max_execution_time', "-1");
		ini_set('memory_limit', "-1");
		ini_set('auto_detect_line_endings',TRUE);
		chdir(MAGENTOROOT);
		umask(0);

		if($_SERVER['REMOTE_ADDR'] == '103.24.183.252'){
			//Mage::setIsDeveloperMode(true);
			//ini_set('display_errors', 1);	
		}

		// Initialize the admin application
		Mage::app('admin');


		//$this->checkAssociatedBundle();
		//exit;
		

		//To Import Lowest Supplier Price for all products
		$this->importLspAttribute();
		exit;

	}
	
	public function checkAssociatedBundle(){

		$bundleId = 6792;
		$storeId = 4;
		$bProdu = Mage::getModel('catalog/product')->setStoreId($storeId)->load($bundleId);
		//$bProdu = Mage::getModel('catalog/product')->load($bundleId);

		$collection = $bProdu->getTypeInstance(true)->getSelectionsCollection($bProdu->getTypeInstance(true)->getOptionsIds($bProdu), $bProdu);
		echo "<pre>";
		if(count($collection)) {
			foreach ($collection as $item) {
				//$itemIds[] = $item->getId();
				$prodId = $item->getId();
				$asProduct = Mage::getModel('catalog/product')->setStoreId($storeId)->load($prodId);
				var_dump($prodId);
				var_dump($asProduct->getSku());
				var_dump($asProduct->getPrice());
				var_dump($asProduct->getLowestSupplierPrice2());
				echo "<br /><br /><br /><hr />";

			}
		}
		//echo "<pre>";
		//var_dump($itemIds);
		echo "</pre>";
		exit;

	}
	
	public function importLspAttribute(){

		$collections = Mage::getModel('catalog/product')->getCollection()
					->setStoreId(0)
					->addAttributeToSelect('*')
                    ->addAttributeToFilter('type_id', array('eq' => 'simple'))
					->addAttributeToSelect('lowest_supplier_price1')
					->addAttributeToSelect('lowest_supplier_price2');

		//var_dump(count($collections));
		$totalProd = count($collections);
		if($totalProd > 0){
			foreach($collections as $product){
				if($product->getId() > 3) { continue; }
				$oldLsp = $product->getLowestSupplierPrice1();
				$product->setLowestSupplierPrice2($oldLsp);
				//$product->getResource()->saveAttribute($product, 'lowest_supplier_price2',0);\
                $product->save();
				echo "Product ".$product->getSku()." is Saved";
				exit;
			}
			echo "Total {$totalProd} Product's lowest_supplier_price2 Is updated";
		}
		return ;
	}

	public function importLspAttributeOld(){

		$collections = Mage::getModel('catalog/product')->getCollection()
					->setStoreId(0)
					->addAttributeToSelect('*')
					->addAttributeToSelect('lowest_supplier_price1')
					->addAttributeToSelect('lowest_supplier_price2');

		//var_dump(count($collections));
		$totalProd = count($collections);
		if($totalProd > 0){
			foreach($collections as $product){
				if($product->getId() > 3) { continue; }
				$oldLsp = $product->getLowestSupplierPrice1();
				$product->setLowestSupplierPrice2($oldLsp);
				$product->getResource()->saveAttribute($product, 'lowest_supplier_price2',0);		
				echo "Product ".$product->getSku()." is Saved";
				exit;
			}
			echo "Total {$totalProd} Product's lowest_supplier_price2 Is updated";
		}
		return ;
	}
}

?>