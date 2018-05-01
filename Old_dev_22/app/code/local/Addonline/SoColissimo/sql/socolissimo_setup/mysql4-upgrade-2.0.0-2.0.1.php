<?php
/**
 * Addonline
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline (http://www.addonline.fr)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;

$installer->startSetup();

$installer->run("

		ALTER TABLE {$this->getTable('socolissimoliberte_relais')} 
		ADD COLUMN code_reseau VARCHAR(3) NOT NULL DEFAULT '',
		ADD COLUMN libelle_nl varchar(50) NOT NULL DEFAULT '',
		ADD COLUMN adresse_nl varchar(38) NOT NULL DEFAULT '',
		ADD COLUMN commune_nl varchar(38) NOT NULL DEFAULT '',
		MODIFY COLUMN commune varchar(38) NOT NULL,
		ADD INDEX type_relais (type_relais ASC);
");

$installer->endSetup();