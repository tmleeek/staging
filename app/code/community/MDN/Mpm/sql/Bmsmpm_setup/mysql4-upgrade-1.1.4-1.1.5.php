<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$ruleTypes = array(MDN_Mpm_Model_Rule::kTypeNoCompetitor);
foreach($ruleTypes as $ruleType)
{
    Mage::getModel('Mpm/Rule')
        ->setType($ruleType)
        ->setName('Default')
        ->setPriority(-1)
        ->setis_system(1)
        ->setenabled(1)
        ->setPreventIndex(1)
        ->save();
}

$installer->endSetup();

