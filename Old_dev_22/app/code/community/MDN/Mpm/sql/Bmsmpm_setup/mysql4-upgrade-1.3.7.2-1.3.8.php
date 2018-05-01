<?php


$installer = $this;

$installer->startSetup();

$installer->addAttribute('catalog_product', 'smartprice_google_position', array(
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Smartprice Google Position',
    'input'             => '',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false
));

$installer->endSetup();

