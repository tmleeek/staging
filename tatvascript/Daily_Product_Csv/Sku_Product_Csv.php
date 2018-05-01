<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');

//echo "hi"; exit;
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');

$storeId = 0;

$filename = "sku_product_csv.csv";
$fp = fopen($filename, "w");

$row = 0;
$data='';
$val = array();
$line = "";
$qty_value=0;
$all_brand_value_ids_array = array();

$line.= '"SKU","COST","CURRENCY","CREATED_DATE"';
$line.= "\n";

$collections = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('sku')
            ->addAttributeToSelect('created_at');

$collections->joinAttribute('lowest_supplier_price2', 'catalog_product/price', 'entity_id', null, 'left', $storeId);

$totalProd = count($collections);
if($totalProd > 0)
{
	foreach($collections as $product_data)
    {
        //print_r($product_data->getData());
        //echo $product_data->getLowestSupplierPrice2();
        //exit;
        //$product_data = Mage::getModel('catalog/product')->setStoreId($storeId)->load($product->getId());
        $p_sku = $product_data->getSku();
        $p_lowest_supplier_price = $product_data->getLowestSupplierPrice2();
        $p_currency = "EUR";
        $p_created_at = $product_data->getCreatedAt();
        $line.= '"'.$p_sku.'","'.$p_lowest_supplier_price.'","'.$p_currency.'","'.$p_created_at.'"';
        $line.= "\n";
        $row++;
    }
}

fputs($fp, $line);
fclose($fp);
echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>