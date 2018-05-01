<?php
chdir(dirname(__FILE__));
require 'app/Mage.php';
$storeId = Mage::app()->getStore()->getId();
$collection = Mage::getModel('sales/order_item')
->getCollection()
->join('catalog/product', 'entity_id=product_id')
->addFieldToSelect('order_id')->addFieldToSelect('qty_ordered')
->addExpressionFieldToSelect('product_id', 'group_concat(entity_id)')
->addExpressionFieldToSelect('sku', 'group_concat(`catalog/product`.`sku`)')
->addExpressionFieldToSelect('qty','group_concat(CONVERT(`qty_ordered`,UNSIGNED INTEGER))');
$collection->getSelect()->group('order_id');
$model = Mage::getModel('whoalsoview/whoalsoview');
foreach($collection as $data)
{
    $product_ids = $data['product_id'];
    $product_skus = $data['sku'];
    $product_qty = $data['qty'];
    $model->setData(array('product_id' => addslashes($product_ids),'product_sku'=>  addslashes($product_skus),'product_quantity'=>  addslashes($product_qty)));
    $model->save();
}
?>