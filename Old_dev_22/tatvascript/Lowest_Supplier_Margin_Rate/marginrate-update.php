<?php
chdir(dirname(__FILE__));
require '../../app/Mage.php';

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$userModel = Mage::getModel('admin/user');
$userModel->setUserId(0);

$storeId = 0;

$productIds = array();

$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

$collection = Mage::getModel('catalog/product')->getCollection()
			->setStoreId($storeId)
			->addAttributeToSelect('sku')
            ->addAttributeToFilter('status', array('in' => array(1,2)));

            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        if ($storeId)
        {
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $storeId);
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $storeId);
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $storeId);
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $storeId);
        }
        else
        {

            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }
        $collection->getSelect()->joinLeft("purchase_product_supplier","pps_product_id = e.entity_id",array("purchase_product_supplier.*"));

        //echo $collection->getSelect();
        //exit;
        //echo count($collection);die();
        $newarray = array();
        //echo "<pre>";print_r($collection->getData());die();
        foreach($collection->getData() as $data)
        {
            //echo "<pre>";print_r($data["entity_id"]);echo "</pre>";
            if((!isset($newarray[$data["entity_id"]]["lowest_cost"])) || (isset($newarray[$data["entity_id"]]["lowest_cost"]) && $newarray[$data["entity_id"]]["lowest_cost"] > $data["pps_last_price"]))
            {
                $productprice = number_format($data["price"],2);
                if($data["pps_last_price"] == 0.00)
                {
                    $pps_last_price = 0;
                }
                else
                {
                    $pps_last_price = $data["pps_last_price"];
                }
                
                //$finalformulaprice = ($productprice - $pps_last_price) / $productprice * 100;
                //$formattedprice = number_format($finalformulaprice,2);
                $newarray[$data["entity_id"]]["product_id"] = $data["entity_id"];
                $newarray[$data["entity_id"]]["lowest_cost"] = $data["pps_last_price"];
                //$newarray[$data["entity_id"]]["margin_rate"] = $formattedprice;
            }
        }

        //exit;

        $flag = 0;
        foreach($newarray as $data)
        {
            $product_id = $data["product_id"];
            $pps_last_price = $data["lowest_cost"];

            $productmodel = Mage::getModel("catalog/product")->load($product_id)->setStoreId(0);
            //echo "<pre>";print_r($productmodel->getSku());echo "</pre>";
            // exit;

            if($productmodel->getSku() == "BET0150001")
            {
                /*if($productmodel->setData('lowest_supplier_price1', $pps_last_price)->getResource()->saveAttribute($productmodel, 'lowest_supplier_price1') &&
                $productmodel->setData('marginrate', $margin_rate)->getResource()->saveAttribute($productmodel, 'marginrate'))
                {
                   $flag = 1;
                   echo "product id $product_id updated successfully". PHP_EOL;
                }*/

                //$pps_last_price = "33.33";
                $productmodel->setData('lowest_supplier_price1', $pps_last_price)->getResource()->saveAttribute($productmodel, 'lowest_supplier_price1');
                $productmodel->setData('lowest_supplier_price2', $pps_last_price)->getResource()->saveAttribute($productmodel, 'lowest_supplier_price2');
                $productmodel->setData('cost', $pps_last_price)->getResource()->saveAttribute($productmodel, 'cost');

                $flag = 1;
                echo "product id $product_id updated successfully with price $pps_last_price";
                echo "<br />";


                //if($productmodel->setData('lowest_supplier_price1', $pps_last_price)->getResource()->saveAttribute($productmodel, 'lowest_supplier_price1') &&
                //$productmodel->setData('lowest_supplier_price2', $pps_last_price)->getResource()->saveAttribute($productmodel, 'lowest_supplier_price2'))
                //{
                //   $flag = 1;
                //   echo "product id $product_id updated successfully". PHP_EOL;
                //}
               // print_r($productmodel->getData());
                //exit;
            }

        }
        if($flag == 1)
        {
            echo "All products updated successfully". PHP_EOL;
        }
?>