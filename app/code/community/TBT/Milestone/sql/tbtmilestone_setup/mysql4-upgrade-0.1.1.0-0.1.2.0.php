<?php

$this->startSetup();

// drop foreign key
$this->dropForeignKey($this->getTable('tbtmilestone/rule_log'), "FK_RULE_ID");
// re-create foreign key
$this->addForeignKey('FK_RULE_ID',
    $this->getTable('tbtmilestone/rule_log'),
    "rule_id",
    $this->getTable('tbtmilestone/rule'),
    "rule_id"
);

$this->modifyColumn(
    $this->getTable('tbtmilestone/rule_log'),
    "executed_date",
    "TIMESTAMP NULL DEFAULT NULL"
);

$this->addColumns($this->getTable('tbtmilestone/rule_log'), "
    `milestone_details_json` VARCHAR(1023) NOT NULL AFTER `action_type`
");

// clear cache
$this->prepareForDb();

$this->endSetup();
