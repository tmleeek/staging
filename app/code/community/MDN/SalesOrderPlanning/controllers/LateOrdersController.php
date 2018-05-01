<?php

class MDN_SalesOrderPlanning_LateOrdersController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->loadLayout();

        $this->_setActiveMenu('erp');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Late orders'));

		$this->renderLayout();
	}
}