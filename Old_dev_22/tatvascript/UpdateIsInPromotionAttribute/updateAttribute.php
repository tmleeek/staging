<?php
require_once('../../app/Mage.php');
umask(0);

Mage::app('admin');
ini_set('max_execution_time', "-1");
ini_set('memory_limit', "-1");
ini_set('auto_detect_line_endings',TRUE);

Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);

$exporter = new updateAttribute();
class updateAttribute
{
	
	function __construct()
	{
		$this->getSaleProducts();
	}
	
	public function getSaleProducts(){


		$todayDate = date('m/d/y');
		$tomorrow = mktime(0, 0, 0, date('m'), date('d'), date('y'));
		$tomorrowDate = date('m/d/y', $tomorrow);
		
/*
		$_productCollection = Mage::getResourceModel('catalog/product_collection');
		$_productCollection->addAttributeToSelect(array('name','special_from_date','special_to_date','price','spcial_price','is_in_promotion'));
		$_productCollection->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate))
							->addAttributeToFilter('special_to_date', array('or'=> array(
								0 => array('date' => true, 'from' => $todayDate),
								1 => array('is' => new Zend_Db_Expr('null')))
							), 'left')
							->addAttributeToSort('special_from_date', 'desc');

*/


		$_productCollection = Mage::getResourceModel('catalog/product_collection');
		$_productCollection->addAttributeToSelect(array('name','special_from_date','special_to_date','price','spcial_price','is_in_promotion'));
		$_productCollection->addAttributeToFilter('special_price', array('gteq' => 0));
							/*->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate))
							->addAttributeToFilter('special_to_date', array('or'=> array(
								0 => array('date' => true, 'from' => $todayDate),
								1 => array('is' => new Zend_Db_Expr('null')))
							), 'left')
							->addAttributeToSort('special_from_date', 'desc');*/

	
		echo '<pre>';
		var_dump($_productCollection->count());
		foreach($_productCollection as $_product){
			var_dump($_product->getData());
			var_dump($_product->getId());	
		}
		exit;
		
		var_dump(count($_productCollection));
		exit;
			
		
		// Update Products Code
		/*foreach($products as $product)
		{
			$product->setIsInPromotion(1);
			$product->getResource()->saveAttribute($product, 'is_in_promotion');
		}*/	
		
	}
	
	public function getSpecialProducts(){

		$collection = Mage::getResourceModel('catalog/product_collection')
					->addAttributeToSelect('price')
					->addAttributeToSelect('special_price')
					->addAttributeToSelect('special_from_date')
					->addAttributeToSelect('special_to_date')
					->addStoreFilter(1);
					
		foreach($collection as $spProducts):
			if ($spProducts->getSpecialPrice() && (($spProducts->getPrice()) > ($spProducts->getSpecialPrice()))):
				if ($spProducts->getSpecialFromDate()):
					if ($spProducts->getSpecialToDate()):
						if (strtotime($spProducts->getSpecialFromDate()) < strtotime($spProducts->getSpecialToDate())):
							try {
								$spCollection[$spProducts->getId()] = $spProducts->getId();
							} catch (Exception $e) {
								echo '<pre>';
								var_dump($e->getMessage());
								echo '</pre>';
								exit;
							}
						endif;
					else:
						$todayDate = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
						if (strtotime($spProducts->getSpecialFromDate()) < strtotime($todayDate)):
							try {
								$spCollection[$spProducts->getId()] = $spProducts->getId();
							} catch (Exception $e) {
								echo '<pre>';
								var_dump($e->getMessage());
								echo '</pre>';
								exit;
							}
						endif;
					endif;						
				endif;
			endif;
		endforeach;
		
		echo "<pre>";
		print_r($spCollection);
		echo "</pre>";
		
		foreach($spCollection as $productId):
			$_product = Mage::getModel('catalog/product')->load($productId);
			//echo $_product->getName();
			//echo $productId."<br/>";
			//echo Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'is_in_promotion', 1);
		endforeach;
		
	}
	
}
