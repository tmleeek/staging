<?php
// Chargement des 4 stores
$storeFR1 = Mage::getModel('core/store')->load('az_fr_part');
$storeFR2 = Mage::getModel('core/store')->load('az_fr_pro');
$storeEN1 = Mage::getModel('core/store')->load('az_en_part');
$storeEN2 = Mage::getModel('core/store')->load('az_en_pro');

$now = date('Y-m-d H:i:s');

// Création des blocs CMS

$paiement = "<span>Latius iam disseminata licentia onerosus bonis omnibus Caesar nullum post haec adhibens modum orientis latera cuncta vexabat nec honoratis parcens nec urbium primatibus nec plebeiis.</span><br class=\"cache\" /><span>Saraceni tamen nec amici nobis umquam nec hostes optandi, ultro citroque discursantes <a href=\"{{ribfileurl}}\" class=\"texte-rose souligne\" target=\"_blank\">RIB</a> inveniri poterat momento temporis parvi vastabant milvorum rapacium similes, qui si praedam dispexerint celsius, volatu rapiunt celeri, aut nisi impetraverint, non inmorantur.</span>";

Mage::getModel('cms/block')
	->setTitle('Mode de paiement - Virement - FR')
	->setIdentifier('paiement_virement')
	->setContent($paiement)
	->setIsActive(true)
	->setCreationTime($now)
	->setUpdateTime($now)
	->setStores(array($storeFR1, $storeFR2))
	->save();
	
Mage::getModel('cms/block')
	->setTitle('Mode de paiement - Virement - EN')
	->setIdentifier('paiement_virement')
	->setContent($paiement)
	->setIsActive(true)
	->setCreationTime($now)
	->setUpdateTime($now)
	->setStores(array($storeEN1, $storeEN2))
	->save();
	
?>