<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_SalesOrderPlanning_ProductAvailabilityStatusController extends Mage_Adminhtml_Controller_Action
{
	
    /**
     * Display grid
     *
     */
	public function GridAction()
    {
    	$this->loadLayout();

        $this->_setActiveMenu('erp');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Product Availability Statuses'));

        $this->renderLayout();
    }
    
    /**
     * Refresh the product availibility sttaus of all product
     *
     */
    public function RefreshAllAction()
    {		
    	mage::helper('SalesOrderPlanning/ProductAvailabilityStatus')->RefreshAll();
    }
    
	/**
     * Refresh the product availibility sttaus of the product that doe snot have any availability status
     *
     */
	public function RefreshOnlyMissingAction()
	{
    	mage::helper('SalesOrderPlanning/ProductAvailabilityStatus')->RefreshOnlyMissing();	
	}

    /**
     * Refresh the product availibility stataus of the product selected in the grid
     *
     */
    public function RefreshOnlySelectedAction()
	{
        $products = $this->getRequest()->getParam('product');
    	mage::helper('SalesOrderPlanning/ProductAvailabilityStatus')->RefreshOnlySelected($products);
	}
    
    /**
     * Refresh one product
     *
     */
    public function RefreshProductAction()
    {
    	try 
    	{
    		$productId = $this->getRequest()->getParam('product_id');
    		mage::helper('SalesOrderPlanning/ProductAvailabilityStatus')->RefreshForOneProduct($productId);
    		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Availability Status Refreshed'));
    	}
    	catch (Exception $ex)
    	{
    		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('An error occured : ').$ex->getMessage());
    	}
    	$this->_redirect('SalesOrderPlanning/ProductAvailabilityStatus/Grid/');
    }
    
    public function RefreshProductAndGoBackToProductSheetAction()
    {
    	try 
    	{
    		$productId = $this->getRequest()->getParam('product_id');
    		mage::helper('SalesOrderPlanning/ProductAvailabilityStatus')->RefreshForOneProduct($productId);
    		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Availability Status Refreshed'));
    	}
    	catch (Exception $ex)
    	{
    		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('An error occured : ').$ex->getMessage());
    	}
    	$this->_redirect('AdvancedStock/Products/Edit', array('product_id' => $productId, 'tab' => 'tab_availability_status'));
    	
    }
}