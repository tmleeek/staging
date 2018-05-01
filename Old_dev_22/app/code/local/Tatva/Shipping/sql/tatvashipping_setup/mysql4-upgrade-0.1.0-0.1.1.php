<?php

$installer = $this;

$installer->startSetup();

$data = array(
    array('FR', 'ML', 'Saint Martin'),
    array('FR', 'BL', 'Saint Barthélémy')
);

foreach ($data as $row) {
    $bind = array(
        'country_id'    => $row[0],
        'code'          => $row[1],
        'default_name'  => $row[2],
    );
$installer->getConnection()->insert($installer->getTable('directory/country_region'), $bind);
$regionId = $installer->getConnection()->lastInsertId($installer->getTable('directory/country_region'));

    $bind = array(
        'locale'    => 'fr_FR',
        'region_id' => $regionId,
        'name'      => $row[2]
    );
    $installer->getConnection()->insert($installer->getTable('directory/country_region_name'), $bind);
}

$installer->endSetup();