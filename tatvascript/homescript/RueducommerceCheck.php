<?php

/*$xml = '<?xml version="1.0" encoding="utf-8"?>
<mmie version="2.0">
  <orders>
    <acknowledged morid="MOR-AA471M17199126" datetime="2014-11-19T7:29:08Z"/>
  </orders>
</mmie>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://marchands.rueducommerce.fr/merchant/syndication/orders/az/new_orders_adfaf77a809f1628edff4b71757aeb77de2cc4ac.xml');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$result = curl_exec($ch);
if($result===false){
    print("Error: ".curl_error($ch));
}else{
    print("Success");
}
curl_close($ch);echo '<pre>';print_r($result);exit;*/

chdir(dirname(__FILE__));
require '../../app/Mage.php';
Mage::app();

Mage::getModel('tatvamarketplaces/rueducommerce')->execute();exit;

//Mage::getModel('tatvamarketplaces/status_fbdinvoice')->execute();exit;
//Mage::getModel('tatvamarketplaces/status_fbdshipment')->execute();exit;
//Mage::getModel('tatvamarketplaces/status_fbdrefund')->execute();exit;

?>
    

?>