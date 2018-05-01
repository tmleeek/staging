<?php
// -- Mapping des modes de transport de :: 

#### RueDuCommerce #####
//REG MARK-32208
$shipping = array(
	array('shipping_code' => 'Colissimo' , 'shipping_mapping' => 'colissimo'),
	array('shipping_code' => 'Chronopost' , 'shipping_mapping' => 'chronopost')
);

$serializeShipping = serialize($shipping);

Mage::getModel('core/config_data')
	->setScope('default')
	->setScopeId(0)
	->setPath('tatvamarketplaces_rueducommerce/shipping_methods/mapping')
	->setValue($serializeShipping)
	->save();

#### PriceMinister #####	
//REG MARK-32304
$shipping = array(
	array('shipping_code' => 'NORMAL'        , 'shipping_mapping' => 'colissimo'),
	array('shipping_code' => 'SUIVI'         , 'shipping_mapping' => 'colissimo'),
	array('shipping_code' => 'RECOMMANDE_R1' , 'shipping_mapping' => 'chronopost'),
	array('shipping_code' => 'RECOMMANDE_R2' , 'shipping_mapping' => 'colissimo'),
	array('shipping_code' => 'RECOMMANDE_R3' , 'shipping_mapping' => 'chronopost')		
);

$serializeShipping = serialize($shipping);

Mage::getModel('core/config_data')
	->setScope('default')
	->setScopeId(0)
	->setPath('tatvamarketplaces_priceminister/shipping_methods/mapping')
	->setValue($serializeShipping)
	->save();