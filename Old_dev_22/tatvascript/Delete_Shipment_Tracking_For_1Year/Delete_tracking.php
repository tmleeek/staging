<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();

$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');

$sql = 'SELECT count(*) FROM `sales_flat_shipment_track` WHERE `created_at` <= curdate( ) - INTERVAL 1 year';
$track_data = $read->fetchOne($sql);

echo $track_data;
exit;
//$a = 0;
if($track_data > 0)
{
    $sql = 'delete FROM `sales_flat_shipment_track` WHERE `created_at` <= curdate( ) - INTERVAL 1 year';
    $write->query($sql);

}
?>