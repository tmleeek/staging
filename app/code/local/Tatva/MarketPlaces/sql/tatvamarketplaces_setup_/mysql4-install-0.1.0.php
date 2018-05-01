<?php 
// EXIG REF-005
// REG BO-311
$storeFR1 = Mage::getModel('core/store')->load('az_fr_part');

Mage::getModel('core/config_data')
	->setScope('default')
	->setScopeId(0)
	->setPath('tatvamarketplaces_orders/configuration/storeview')
	->setValue($storeFR1->getId())
	->save();

