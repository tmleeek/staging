<?php
$this->startSetup();
$this->run("ALTER TABLE `{$this->getTable('core/website')}` ADD COLUMN `store_fbd_id` VARCHAR(255)");//change the name and type of the column if you need.
$this->endSetup();
?>