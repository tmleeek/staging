<?php
$installer = $this;

$installer->startSetup();

//Setup the customer points summary notification flag
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute('customer', 'rewards_points_notification', array(
    'label' => "Send Customer Points Notification",
    'type' => 'int',
    'input' => 'select',
    'visible' => false,
    'required' => false,
    'default' => 0,
    'source' => 'eav/entity_attribute_source_boolean'
));

$installer->endSetup(); 
