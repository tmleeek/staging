<?php

$installer = $this;

$installer->startSetup();

$installer->run("

  ALTER TABLE advice DROP COLUMN advice_fr;
  ALTER TABLE advice DROP COLUMN advice_en;
  ALTER TABLE advice ADD COLUMN advice_text text  NOT NULL default '';
  ALTER TABLE advice ADD COLUMN store_id varchar(255)  NOT NULL default '';

");

$installer->endSetup();