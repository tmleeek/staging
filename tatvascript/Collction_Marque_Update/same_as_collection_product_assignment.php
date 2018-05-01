<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
ini_set ( 'memory_limit', '2048M' );


$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;

/* empty existing table */
$marque_data_sql = 'Select * from `marqueproducts`';
$product_colls = $read->fetchAll($marque_data_sql);
$row = 0;
$i = 0;
foreach($product_colls as $products)
{
    $same_collection = explode(',', $products['product_ids']);
    print_r($same_collection);
    echo "<br /><br />";

    $produt_count = count($same_collection)-1;
    echo "produt_count = ".$produt_count;
    echo "<br /><br />";
    foreach($same_collection as $collection)
    {
        echo "collection = ".$collection;
        echo "<br /><br />";

        if(!empty($collection))
        {
            $_product = Mage::getModel('catalog/product')->load($collection);

            $aParams = buildUpsellArray($collection, $same_collection);
            print_r($aParams);
            echo "<br /><br />";
            //exit;
		    try
            {
		        // Set the upsell data on the model and save
			    $_product->setUpSellLinkData($aParams)->save();
		    }
		    catch(Exception $e)
		    {
                print_r($e->getMessage());
                echo "\n Problem saving upsell link data";
                exit;
		    }

            $row++;
        }
    }
}


function buildUpsellArray($product_id, $aUpsells)
{

    echo "product_id = ".$product_id;
    echo "<br /><br />";
	$aReturn = array();
	$counter = 0;
	foreach($aUpsells as $upsell_id)
    {
		if($upsell_id!=$product_id)
        {
			$aReturn[$upsell_id] = array('position'=>$counter);
			$counter++;
		}
	}
	return $aReturn;
}

echo "Total Inserted Item:--".$row.'<br>';
echo 'Done';
?>