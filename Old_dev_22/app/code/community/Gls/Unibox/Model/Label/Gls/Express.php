<?php
/**
 * Gls_Unibox extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Gls
 * @package    Gls_Unibox
 * @copyright  Copyright (c) 2013 webvisum GmbH
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Webvisum
 * @package    Gls_Unibox
 */
class Gls_Unibox_Model_Label_Gls_Express extends Gls_Unibox_Model_Label_Abstract
{

	public function __construct() {
		parent::__construct();
		$this->insertExpressDefaults();
	}

	protected function insertExpressDefaults(){
		//defaults fuellen, von Express, allgemeine defaults wurden bereits durch den konstruktor von parent gefüllt
		$item = new Varien_Object(); $item->setTag('105')->setValue(null)->setPosx(57)->setPosy(6.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(28) ); $this->data->addItem($item); $item = null;
		//Express Versand specific Tags
		$item = new Varien_Object(); $item->setTag('640')->setValue(null)->setPosx(35)->setPosy(74)->setItem( Mage::getModel('glsbox/label_item_barcode')->setType('Code25interleaved')->setHeight(10)->setBarThickWidth(2)->setBarThinWidth(1)->setFactor(2.4) ); $this->data->addItem($item); $item = null;		
		$item = new Varien_Object(); $item->setTag('640')->setValue(null)->setPosx(50.5)->setPosy(87)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7)->setFace('bold') ); $this->data->addItem($item); $item = null;	
		
		
		$item = new Varien_Object(); $item->setTag('750')->setValue(null)->setPosx(1.5)->setPosy(64)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(12)->setFace('bold') ); $this->data->addItem($item); $item = null;
		$item = new Varien_Object(); $item->setTag('static')->setValue('Zustelldatum:')->setPosx(1.8)->setPosy(67.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;	
		$item = new Varien_Object(); $item->setTag('644')->setValue(null)->setPosx(22)->setPosy(67.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;		
		$item = new Varien_Object(); $item->setTag('753')->setValue(null)->setPosx(1.8)->setPosy(70)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;		
		$item = new Varien_Object(); $item->setTag('641')->setValue(null)->setPosx(18)->setPosy(85)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(18) ); $this->data->addItem($item); $item = null;		

		$item = new Varien_Object(); $item->setTag('static')->setValue('Zustellzeit bis:')->setPosx(50)->setPosy(67.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;	
		$item = new Varien_Object(); $item->setTag('static')->setValue('Tournummer:')->setPosx(50)->setPosy(70)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;	
		
		$item = new Varien_Object(); $item->setTag('643')->setValue(null)->setPosx(70)->setPosy(67.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;		
		$item = new Varien_Object(); $item->setTag('642')->setValue(null)->setPosx(70)->setPosy(70)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;		
	}
}