<?php

class MDN_AdvancedStock_SalesHistoryController extends Mage_Adminhtml_Controller_Action
{

	/**
	 * Update stats for all products
	 */
	public function UpdateForAllProductsAction()
	{
		$helper = mage::helper('AdvancedStock/Sales_History');
		$helper->updateForAllProducts();
	}
	
	/**
	 * Update stats for one product
	 */
	public function RefreshForProductAction()
	{
		$productId = $this->getRequest()->getParam('product_id');
		$helper = mage::helper('AdvancedStock/Sales_History');
		$helper->RefreshForOneProduct($productId);
		
		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Sales History Updated')); 
		$this->_redirect('AdvancedStock/Products/Edit', array('product_id' => $productId, 'tab' => 'tab_history'));		
	}
	
}