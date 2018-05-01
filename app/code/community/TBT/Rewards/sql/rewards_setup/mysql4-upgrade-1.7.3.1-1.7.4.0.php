<?php
$installer = $this;

$installer->startSetup();


$this->attemptQuery("
    ALTER TABLE `{$this->getTable('rewards/transfer_reference')}`
    ADD INDEX `REWARDS_TRANSFER_REFERENCE_REFERENCE_TYPE_REFERENCE_ID_FK` (`reference_type`, `reference_id`);
");


$installer->endSetup(); 
