<?php

/**
 * WDCA - Sweet Tooth
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS 
 * License, which extends the Open Software License (OSL 3.0).

 * The Open Software License is available at this URL: 
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * DISCLAIMER
 * 
 * By adding to, editing, or in any way modifying this code, WDCA is 
 * not held liable for any inconsistencies or abnormalities in the 
 * behaviour of this code. 
 * By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by WDCA, outlined in the 
 * provided Sweet Tooth License. 
 * Upon discovery of modified code in the process of support, the Licensee 
 * is still held accountable for any and all billable time WDCA spent 
 * during the support process.
 * WDCA does not guarantee compatibility with any other framework extension. 
 * WDCA is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to 
 * support@sweettoothrewards.com or call 1.855.699.9322, so we can send you a copy 
 * immediately.
 * 
 * @category   [TBT]
 * @package    [TBT_Rewards]
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Observer Sales Order Place After
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Model_Observer_Sales_Order_Place_After implements TBT_Rewards_Model_Customer_Listener {
	
	/**
	 * Order model by reference for quoteToOrder conversions event
	 *
	 * @var TBT_Rewards_Model_Sales_Order
	 */
	protected $order = null;
	
	protected $_reservedIncrementId = null;
	
	public function __construct() {
	
	}
	
	/**
	 * Applies the special price percentage discount
	 * @param   Varien_Event_Observer $observer
	 * @return  Xyz_Catalog_Model_Price_Observer
	 */
	public function prepareCartPointsTransfers($observer) {
		$event = $observer->getEvent ();
		$this->order = $event->getOrder ();
		$quote = $this->_getRewardsSession()->getQuote ();
		
		try {
			if (! $this->order) {
				return $this;
			}
			//@nelkaake Added on Thursday May 27, 2010: If mage 1.4 then add "true" tothe checkout method get function
			if (Mage::helper ( 'rewards' )->isBaseMageVersionAtLeast ( '1.4.0.0' )) {
			    if ($quote->getCheckoutMethod (true) == Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER) {
					Mage::helper ( 'rewards' )->notice ( "Checkout method is REGISTER to added customer listener in TBT_Rewards_Model_Observer_Sales_Order_Place_After..." );
					$this->_reservedIncrementId = $this->order->getIncrementId();
					$this->_getRewardsSession ()->addCustomerListener ( $this );
				} else {
					$this->order->prepareCartPointsTransfers ();
				}
			} else {
				//@nelkaake Added on Thursday June 17, 2010: changed constant to register method. 
				if ($quote->getCheckoutMethod () == Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER) {
					Mage::helper ( 'rewards' )->notice ( "Checkout method is REGISTER to added customer listener in TBT_Rewards_Model_Observer_Sales_Order_Place_After..." );
					$this->_reservedIncrementId = $this->order->getIncrementId();
					$this->_getRewardsSession ()->addCustomerListener ( $this );
				} else {
					$this->order->prepareCartPointsTransfers ();
				}
			}
		} catch ( Exception $e ) {
			Mage::logException ( $e );
			Mage::log ( $e->getMessage () );
			throw $e;
		}
		
		return $this;
	}
	
	/**
	 * Fetches the rewards session
	 *
	 * @return TBT_Rewards_Model_Session
	 */
	protected function _getRewardsSession() {
		return Mage::getSingleton ( 'rewards/session' );
	}
	
	/**
	 * Triggered when customer model is created
	 *
	 * @param TBT_Rewards_Model_Customer $customer
	 * @return TBT_Rewards_Model_Customer_Listener
	 */
	public function onNewCustomerCreate(&$customer) {
		Mage::helper ( 'rewards' )->notice ( "Triggered customer registration listener to generate order cart points in TBT_Rewards_Model_Observer_Sales_Order_Place_After." );
		
		$this->order->prepareCartPointsTransfers ();
		Mage::getSingleton('rewards/observer_sales_carttransfers')->setIncrementId($this->_reservedIncrementId);
		
		//$this->order->load($this->order->getId());
		$event = new Varien_Event(array('order' => $this->order));
		$observer = new Varien_Event_Observer(array('event' => $event));
		Mage::getSingleton('rewards/observer_sales_order_save_after_create')->createPointsTransfers($observer);
		
		return $this;
	}

}
