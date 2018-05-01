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

		UPDATE  {$this->getTable('socolissimoliberte_relais')}  SET code_reseau = 'R01' WHERE code_reseau='';
		ALTER TABLE {$this->getTable('socolissimoliberte_relais')} 
		ADD UNIQUE INDEX identifiant_reseau (identifiant ASC, code_reseau ASC);
		
");

$installer->endSetup();