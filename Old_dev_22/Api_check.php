<?php
define('MAGENTO', realpath(dirname(__FILE__)));
require_once 'app/Mage.php';
Mage::app();

$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');

$data = file_get_contents ('http://api.envoidunet.com/labels/dhl/JD014600004533955109.pdf');
$fileContent = addslashes($data);

$sql = 'UPDATE `sales_flat_shipment` SET `shipping_label`= '.$fileContent.' WHERE `entity_id` = 32829' ;
$write->query($sql);

/*$client = new SoapClient('http://api.envoidunet.com/?wsdl');


$login = new stdClass();
$login->api_account = 'azboutique_2017';
$login->api_key = 'NWU2ZmNkMDlmNDlmYjdiYzYxZmNlNTk1Mzc3YTI4NmI=';

$params = new stdClass();

$params->reference = 'MY_REFERENCE';
$params->value = 100;
$params->carrier = 'dhl';
$params->format = 'A6';

$params->from = new stdClass();
$params->from->company = 'Envoi du Net';
$params->from->firstname = 'Jane';
$params->from->lastname = 'Doe';
$params->from->address1 = '21 allée des Métallos';
$params->from->postcode = '06700';
$params->from->city = 'Saint Laurent du Var';
$params->from->country = 'FR';
$params->from->email = 'test@envoidunet.com';
$params->from->phone = '0123456789';

$params->to = new stdClass();
$params->to->firstname = 'John';
$params->to->lastname = 'Doe';
$params->to->address1 = '21 teststrasse';
$params->to->postcode = '10115';
$params->to->city = 'Berlin';
$params->to->country = 'DE';
$params->to->email = 'client@envoidunet.com';
$params->to->phone = '0123456789';

$package = new stdClass();
$package->weight = 5;

$product = new stdClass();
$product->name = 'Test product';
$product->price = 10;
$product->qty = 1;
$product->weight = 5;

$package->products = array($product);
$params->packages = array($package);

try {
    $result = $client->createShipment($login, $params);
    if ($result['error']->error_id > 0) {
        $error = $result['error'];
        $message = $error->error_message;
        $message .= isset($error->error_description) ? ' ('.$error->error_description.')' : '';
        echo $message."\n";
    }

    var_dump($result);
} catch (SoapFault $e) {
    echo $e->getMessage()."\n";
}*/
?>