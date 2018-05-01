<?php

class Tatva_Ekomiflagupdate_Adminhtml_EkomiflagupdateController extends Mage_Adminhtml_Controller_action
{
	protected function _initAction() { 
		$this->loadLayout()
			->_setActiveMenu('azboutique')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
	}   

	public function indexAction() {
		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('ekomiflagupdate/adminhtml_ekomiflagupdate'))
			->renderLayout();
         /*   $this->loadLayout();
          $this->_addContent($this->getLayout()->createBlock('ekomiflagupdate/adminhtml_ekomiflagupdate_edit_form1'));
          $this->renderLayout();*/
	}
	
	public function saveAction() {
		
	   $orderIncrementId = $this->getRequest()->getPost('order_increment_id'); 
	   
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		$shipment = $order->getShipmentsCollection()->getFirstItem();
		$shipmentIncrementId = $shipment->getIncrementId();
		
		$shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);
		$shipment->setData('ekomi_flag',0);
		$shipment->save();
		
		$this->_getSession()->addSuccess($this->__('Ekomi flag was successfully updated.'));
		$this->_redirect('ekomiflagupdate/adminhtml_ekomiflagupdate');
		
		
		
	}
}