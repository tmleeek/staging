<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$ruleTypes = array(MDN_Mpm_Model_Rule::kTypeCost,
                  MDN_Mpm_Model_Rule::kTypeShipping,
                  MDN_Mpm_Model_Rule::kTypeMargin,
                  MDN_Mpm_Model_Rule::kTypeAdjustment);
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

