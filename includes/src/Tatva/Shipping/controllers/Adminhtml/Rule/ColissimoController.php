<?php


/**
 * 
 * @package Tatva_Shipping
 */
require_once ('Tatva/Shipping/controllers/Adminhtml/Rule/AbstractController.php');
class Tatva_Shipping_Adminhtml_Rule_ColissimoController extends Tatva_Shipping_Adminhtml_Rule_AbstractController
{

	public function indexAction() {
		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('tatvashipping/adminhtml_rule_colissimo'))
			->renderLayout();
	}
}