<?php
$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute('customer', 'rewardsref_notify_on_referral', array(
    'label' => Mage::helper('rewardsref')->__('Notify on Referral'), 
    'type' => 'int', 
    'input' => 'select', 
    'visible' => true, 
    'required' => false, 
    'position' => 1, 
    'default' => 1, 
    'default_value' => 1, 
    'source' => "rewardsref/attribute_notify"
));

/* Adding extra column to specify type of points being awarded (percentage vs. fixed) as "simple_action" inside customer behaviour rules */
Mage::helper('rewards/mysql4_install')->addColumns($installer, $this->getTable('rewards_special'), 
array(
    "`simple_action` VARCHAR(32) NOT NULL DEFAULT 'by_percent'"
));

$installer->endSetup();