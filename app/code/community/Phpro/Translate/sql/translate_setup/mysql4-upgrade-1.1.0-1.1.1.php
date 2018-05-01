<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    CREATE INDEX idx_store ON {$this->getTable('phpro_translate')} (store_id);
    ALTER TABLE {$this->getTable('phpro_translate')} 
    CHANGE  `store_id`  `store_id` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT  '0';
    ALTER TABLE {$this->getTable('phpro_translate')}
    ADD CONSTRAINT fk_storeview_untranslated FOREIGN KEY (store_id) REFERENCES {$this->getTable('core_store')} (store_id) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->endSetup();
