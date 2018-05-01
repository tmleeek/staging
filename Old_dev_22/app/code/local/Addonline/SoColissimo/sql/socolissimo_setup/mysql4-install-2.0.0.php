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

/**
 * On nettoie la table core_config dans le cas d'une installation préalable
 * des Modules SocolissimoFlexibilite ou SocolissimoLiberte, et on récupère les
 * paramètres de l'installation préalable
 */
$installer->run("
	INSERT INTO {$this->getTable('core_config_data')} (scope, scope_id, path, value) SELECT scope, scope_id, 'carriers/socolissimo/rep_fichier_liberte', value FROM {$this->getTable('core_config_data')} WHERE path='carriers/socolissimoliberte/rep_fichier_socolissimo';
	INSERT INTO {$this->getTable('core_config_data')} (scope, scope_id, path, value) SELECT scope, scope_id, 'socolissimo/licence/serial', value FROM {$this->getTable('core_config_data')} WHERE path='socolissimoliberte/licence/serial';
	DELETE FROM {$this->getTable('core_config_data')} WHERE path like '%socolissimoliberte%';		      
	INSERT INTO {$this->getTable('core_config_data')} (scope, scope_id, path, value) SELECT scope, scope_id, 'carriers/socolissimo/id_socolissimo_flexibilite', value FROM {$this->getTable('core_config_data')} WHERE path='carriers/socolissimoflexibilite/id_socolissimo';
	INSERT INTO {$this->getTable('core_config_data')} (scope, scope_id, path, value) SELECT scope, scope_id, 'carriers/socolissimo/password_socolissimo_flexibilite', value FROM {$this->getTable('core_config_data')} WHERE path='carriers/socolissimoflexibilite/password_socolissimo';
	INSERT INTO {$this->getTable('core_config_data')} (scope, scope_id, path, value) SELECT scope, scope_id, 'socolissimo/licence/serial', value FROM {$this->getTable('core_config_data')} WHERE path='socolissimoflexibilite/licence/serial';
	DELETE FROM {$this->getTable('core_config_data')} WHERE path like '%socolissimoflexibilite%';		      

	DELETE FROM {$this->getTable('core_resource')} WHERE code = 'socolissimoflexibilite_setup';		      
	DELETE FROM {$this->getTable('core_resource')} WHERE code = 'socolissimoliberte_setup';		      

	UPDATE {$this->getTable('sales_flat_order')} SET shipping_method = replace(shipping_method, 'socolissimoflexibilite', 'socolissimo') WHERE shipping_method LIKE 'socolissimoflexibilite%';
	UPDATE {$this->getTable('sales_flat_order')} SET shipping_method = replace(shipping_method, 'socolissimoliberte', 'socolissimo') WHERE shipping_method LIKE 'socolissimoliberte%';
	
");

/**
 * On crée les tables pour la version Liberté si elles n'existent pas déjà
 */

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('socolissimoliberte_periode_fermeture')};
DROP TABLE IF EXISTS {$this->getTable('socolissimoliberte_horaire_ouverture')};
DROP TABLE IF EXISTS {$this->getTable('socolissimoliberte_relais')};

CREATE TABLE {$this->getTable('socolissimoliberte_relais')} (
  id_relais int(11) NOT NULL auto_increment,
  identifiant varchar(6) NOT NULL,
  libelle varchar(50) NOT NULL,
  adresse varchar(38) NOT NULL,
  complement_adr varchar(38) default NULL,
  lieu_dit varchar(38) default NULL,
  indice_localisation varchar(70) default NULL,
  code_postal varchar(5) NOT NULL,
  commune varchar(32) NOT NULL,
  latitude double(10,8) NOT NULL,
  longitude double(10,8) NOT NULL,
  indicateur_acces int(11) default NULL,
  type_relais varchar(3) NOT NULL,
  point_max double(2,0) default NULL,
  lot_acheminement varchar(10) default NULL,
  distribution_sort varchar(10) default NULL,
  version varchar(2) default NULL,
  PRIMARY KEY  (id_relais)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->run("

CREATE TABLE {$this->getTable('socolissimoliberte_horaire_ouverture')} (
  id_horaire_ouverture int(11) NOT NULL auto_increment,
  id_relais_ho int(11) NOT NULL,
  deb_periode_horaire varchar(5) NOT NULL,
  fin_periode_horaire varchar(5) NOT NULL,
  horaire_lundi varchar(23) default NULL,
  horaire_mardi varchar(23) default NULL,
  horaire_mercredi varchar(23) default NULL,
  horaire_jeudi varchar(23) default NULL,
  horaire_vendredi varchar(23) default NULL,
  horaire_samedi varchar(23) default NULL,
  horaire_dimanche varchar(23) default NULL,
  PRIMARY KEY  (id_horaire_ouverture),
  KEY fk_socolissimo_relais (id_relais_ho),
  CONSTRAINT fk_socolissimo_relais FOREIGN KEY (id_relais_ho) REFERENCES {$this->getTable('socolissimoliberte_relais')} (id_relais)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->run("

CREATE TABLE {$this->getTable('socolissimoliberte_periode_fermeture')} (
  id_periode_fermeture int(11) NOT NULL auto_increment,
  id_relais_fe int(11) NOT NULL,
  deb_periode_fermeture date default NULL,
  fin_periode_fermeture date default NULL,
  PRIMARY KEY  (id_periode_fermeture),
  KEY fk_socilissimo_relais (id_relais_fe),
  CONSTRAINT fk_socilissimo_relais FOREIGN KEY (id_relais_fe) REFERENCES {$this->getTable('socolissimoliberte_relais')} (id_relais)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

/**
 * Les attributs suivants sont les mêmes que ceux créés par le module SocolissimoSimplicité et utilisés
 * par le module ExpeditorInet qui permet de faire l'export vers l'éditeur de vignettes
 * On les crée tels quel pour être compatible avec ExpeditorInet
 */
$this->addAttribute('order', 'soco_product_code', array(
    'type' => 'varchar',
    'label' => 'Code produit Colissimo',
    'visible' => true,
    'required' => false,
    'input' => 'text'
));

$this->addAttribute('order', 'soco_shipping_instruction', array(
    'type' => 'varchar',
    'label' => 'Instructions de livraison Colissimo',
    'visible' => true,
    'required' => false,
    'input' => 'text'
));

$this->addAttribute('order', 'soco_door_code1', array(
    'type' => 'varchar',
    'label' => 'Code porte 1 Colissimo',
    'visible' => true,
    'required' => false,
    'input' => 'text'
));

$this->addAttribute('order', 'soco_door_code2', array(
    'type' => 'varchar',
    'label' => 'Code porte 2 Colissimo',
    'visible' => true,
    'required' => false,
    'input' => 'text'
));

$this->addAttribute('order', 'soco_interphone', array(
    'type' => 'varchar',
    'label' => 'Interphone Colissimo',
    'visible' => true,
    'required' => false,
    'input' => 'text'
));

$this->addAttribute('order', 'soco_relay_point_code', array(
    'type' => 'varchar',
    'label' => 'Code du point de retrait Colissimo',
    'visible' => true,
    'required' => false,
    'input' => 'text'
));

$this->addAttribute('order', 'soco_civility', array(
    'type' => 'varchar',
    'label' => 'Civilité',
    'visible' => true,
    'required' => false,
    'input' => 'text'
));

$this->addAttribute('order', 'soco_phone_number', array(
    'type' => 'varchar',
    'label' => 'Numéro de portable',
    'visible' => true,
    'required' => false,
    'input' => 'text'
));

$this->addAttribute('order', 'soco_email', array(
    'type' => 'varchar',
    'label' => 'E-mail du destinataire',
    'visible' => true,
    'required' => false,
    'input' => 'text'
));

$installer->endSetup();