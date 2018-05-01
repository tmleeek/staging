<?php
require_once '../../app/Mage.php';

umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$userModel = Mage::getModel('admin/user');
$userModel->setUserId(0);

$storeId = 0;

$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');

$attribute_id = '261';
$entity_type_id = '4';

$collections = Mage::getModel('catalog/product')->getCollection()
			->setStoreId($storeId)
			->addAttributeToSelect('*')
            ->addAttributeToFilter('type_id', array('eq' => 'bundle'))
            ->addAttributeToFilter('status', array('in' => array(1,2)));

$totalProd = count($collections);
if($totalProd > 0)
{
	foreach($collections as $product)
    {
        $bundleId = $product->getId();

        $stores = array('0','3','4','12','13','14');

        $bProdu = Mage::getModel('catalog/product')->setStoreId($storeId)->load($bundleId);
        $collection = $bProdu->getTypeInstance(true)->getSelectionsCollection($bProdu->getTypeInstance(true)->getOptionsIds($bProdu), $bProdu);

        foreach($stores as $store)
        {
            if(count($collection))
            {
                $totalLsp = 0;
        		foreach ($collection as $item)
                {

        			$prodId = $item->getId();
                    $prodQty = $item->getSelectionQty();

                    $temp_simple_price = '';
                    $sql_simple_option = "SELECT `value` FROM `catalog_product_entity_decimal` WHERE `attribute_id` = '".$attribute_id."' AND `store_id` = '".$store."' AND `entity_id` = '".$prodId."'" ;
                    $temp_simple_price = $read->FetchOne($sql_simple_option);
                    $temp_simple_price = $temp_simple_price*$prodQty;
                    $totalLsp += number_format($temp_simple_price,2);
        		}

                echo $bundleId ." => ".$store." = ".$totalLsp;
                echo "<br />";
                $bundle_data = '';
                $sql_bundle_option="SELECT * FROM `catalog_product_entity_decimal` WHERE `attribute_id` = '".$attribute_id."' AND `store_id` = '".$store."' AND `entity_id` = '".$bundleId."'" ;
                $bundle_data = $read->FetchOne($sql_bundle_option);

                if($bundle_data == '')
                {
                    $insert_bundle_data = "INSERT INTO `catalog_product_entity_decimal` (entity_type_id, attribute_id, store_id, entity_id, value) VALUES ('".$entity_type_id."', '".$attribute_id."', '".$store."', '".$bundleId."', '".$totalLsp."')";
                    $write->query($insert_bundle_data);

                }
                else
                {
                    $update_bundle_data = "UPDATE `catalog_product_entity_decimal` SET `value` = '".$totalLsp."' WHERE `attribute_id` = '".$attribute_id."' AND `store_id` = '".$store."' AND `entity_id` = '".$bundleId."'";
                    $write->query($update_bundle_data);
                }
        	}
        }

    }
    echo "<br />";
}

/*//Dev_Data
$attribute_id = '261';
$entity_type_id = '4';
$uk_store_id = '3';
$usa_store_id = '4';
$suisse_store_id = '12';
$schweiz_store_id = '13';
$svizzera_store_id = '14';


$sql = "SELECT * FROM `catalog_product_entity_decimal` WHERE `attribute_id` = 261 AND `store_id` = 0 AND `entity_id` > 6982" ;
//$sql = "SELECT * FROM `catalog_product_entity_decimal` WHERE `attribute_id` = '".$attribute_id."' AND `store_id` = 0";
$all_decimal_data = $read->FetchAll($sql);

$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();

//Uk Code
$UkCurrencyCode = Mage::app()->getStore($uk_store_id)->getCurrentCurrencyCode();

//USA Code
$UsaCurrencyCode = Mage::app()->getStore($usa_store_id)->getCurrentCurrencyCode();

//Suisse Code
$SuisseCurrencyCode = Mage::app()->getStore($suisse_store_id)->getCurrentCurrencyCode();

//Schweiz Code
$SchweizCurrencyCode = Mage::app()->getStore($schweiz_store_id)->getCurrentCurrencyCode();

//Svizzera Code
$SvizzeraCurrencyCode = Mage::app()->getStore($svizzera_store_id)->getCurrentCurrencyCode();



foreach($all_decimal_data as $decimal_data_default)
{
    echo "product_id =".$product_id = $decimal_data_default['entity_id'];
    echo "<br />";
    echo "default_store_price =".$default_store_price = $decimal_data_default['value'];
    echo "<br />";

    //UK Price Update
    $temp_uk = '';
    $sql_uk_option="SELECT * FROM `catalog_product_entity_decimal` WHERE `attribute_id` = '".$attribute_id."' AND `store_id` = '".$uk_store_id."' AND `entity_id` = '".$product_id."'" ;
    $temp_uk = $read->FetchOne($sql_uk_option);

    echo "Uk Price = ".$uk_price = Mage::helper('directory')->currencyConvert($default_store_price, $baseCurrencyCode, $UkCurrencyCode);
    echo "<br />";

    $uk_price = number_format($uk_price,2);

    if($temp_uk == '')
    {
        echo $update_uk_prie = "INSERT INTO `catalog_product_entity_decimal` (entity_type_id, attribute_id, store_id, entity_id, value) VALUES ('".$entity_type_id."', '".$attribute_id."', '".$uk_store_id."', '".$product_id."', '".$uk_price."')";
        $write->query($update_uk_prie);

    }
    else
    {
        echo $update_uk_prie = "UPDATE `catalog_product_entity_decimal` SET `value` = '".$uk_price."' WHERE `attribute_id` = '".$attribute_id."' AND `store_id` = '".$uk_store_id."' AND `entity_id` = '".$product_id."'";
        $write->query($update_uk_prie);
    }
    echo "<br /><br />";

    //USA Price Update
    $temp_usa = '';
    $sql_us_option="SELECT * FROM `catalog_product_entity_decimal` WHERE `attribute_id` = '".$attribute_id."' AND `store_id` = '".$usa_store_id."' AND `entity_id` = '".$product_id."'" ;
    $temp_usa = $read->FetchOne($sql_us_option);

    echo "US Price = ".$us_price = Mage::helper('directory')->currencyConvert($default_store_price, $baseCurrencyCode, $UsaCurrencyCode);
    echo "<br />";

    $us_price = number_format($us_price,2);

    if($temp_usa == '')
    {
        echo $update_us_prie = "INSERT INTO `catalog_product_entity_decimal` (entity_type_id, attribute_id, store_id, entity_id, value) VALUES ('".$entity_type_id."', '".$attribute_id."', '".$usa_store_id."', '".$product_id."', '".$us_price."')";
        $write->query($update_us_prie);

    }
    else
    {
        echo $update_us_prie = "UPDATE `catalog_product_entity_decimal` SET `value` = '".$us_price."' WHERE `attribute_id` = '".$attribute_id."' AND `store_id` = '".$usa_store_id."' AND `entity_id` = '".$product_id."'";
        $write->query($update_us_prie);
    }
    echo "<br /><br />";

    //Suisse Price Update
    $temp_suisse = '';
    $sql_suisse_option="SELECT * FROM `catalog_product_entity_decimal` WHERE `attribute_id` = '".$attribute_id."' AND `store_id` = '".$suisse_store_id."' AND `entity_id` = '".$product_id."'" ;
    $temp_suisse = $read->FetchOne($sql_suisse_option);

    echo "Suisse Price = ".$suisse_price = Mage::helper('directory')->currencyConvert($default_store_price, $baseCurrencyCode, $SuisseCurrencyCode);
    echo "<br />";
    $suisse_price = number_format($suisse_price,2);

    if($temp_suisse == '')
    {
        echo $update_suisse_prie = "INSERT INTO `catalog_product_entity_decimal` (entity_type_id, attribute_id, store_id, entity_id, value) VALUES ('".$entity_type_id."', '".$attribute_id."', '".$suisse_store_id."', '".$product_id."', '".$suisse_price."')";
        $write->query($update_suisse_prie);

    }
    else
    {
        echo $update_suisse_prie = "UPDATE `catalog_product_entity_decimal` SET `value` = '".$suisse_price."' WHERE `attribute_id` = '".$attribute_id."' AND `store_id` = '".$suisse_store_id."' AND `entity_id` = '".$product_id."'";
        $write->query($update_suisse_prie);
    }
    echo "<br /><br />";

    //Schweiz Price Update
    $temp_schweiz = '';
    $sql_schweiz_option="SELECT * FROM `catalog_product_entity_decimal` WHERE `attribute_id` = '".$attribute_id."' AND `store_id` = '".$schweiz_store_id."' AND `entity_id` = '".$product_id."'" ;
    $temp_schweiz = $read->FetchOne($sql_schweiz_option);

    echo "Schweiz Price = ".$schweiz_price = Mage::helper('directory')->currencyConvert($default_store_price, $baseCurrencyCode, $SchweizCurrencyCode);
    echo "<br />";
    $schweiz_price = number_format($schweiz_price,2);

    if($temp_schweiz == '')
    {
        echo $update_schweiz_prie = "INSERT INTO `catalog_product_entity_decimal` (entity_type_id, attribute_id, store_id, entity_id, value) VALUES ('".$entity_type_id."', '".$attribute_id."', '".$schweiz_store_id."', '".$product_id."', '".$schweiz_price."')";
        $write->query($update_schweiz_prie);
    }
    else
    {
        echo $update_schweiz_prie = "UPDATE `catalog_product_entity_decimal` SET `value` = '".$schweiz_price."' WHERE `attribute_id` = '".$attribute_id."' AND `store_id` = '".$schweiz_store_id."' AND `entity_id` = '".$product_id."'";
        $write->query($update_schweiz_prie);
    }
    echo "<br /><br />";

    //Svizzera Price Update
    $temp_svizzera = '';
    $sql_svizzera_option="SELECT * FROM `catalog_product_entity_decimal` WHERE `attribute_id` = '".$attribute_id."' AND `store_id` = '".$svizzera_store_id."' AND `entity_id` = '".$product_id."'" ;
    $temp_svizzera = $read->FetchOne($sql_svizzera_option);

     echo "Svizzera Price = ".$svizzera_price = Mage::helper('directory')->currencyConvert($default_store_price, $baseCurrencyCode, $SvizzeraCurrencyCode);
     echo "<br />";

     $svizzera_price = number_format($svizzera_price,2);
    if($temp_svizzera == '')
    {
        echo $update_svizzera_prie = "INSERT INTO `catalog_product_entity_decimal` (entity_type_id, attribute_id, store_id, entity_id, value) VALUES ('".$entity_type_id."', '".$attribute_id."', '".$svizzera_store_id."', '".$product_id."', '".$svizzera_price."')";
        $write->query($update_svizzera_prie);
    }
    else
    {
        echo $update_svizzera_prie = "UPDATE `catalog_product_entity_decimal` SET `value` = '".$svizzera_price."' WHERE `attribute_id` = '".$attribute_id."' AND `store_id` = '".$svizzera_store_id."' AND `entity_id` = '".$product_id."'";
        $write->query($update_svizzera_prie);
    }
    echo "<br /><br />";
    echo "-----------------------------------------";
    echo "<br /><br />";

}*/


?>