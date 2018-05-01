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
 * Manage Currency Controller
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
 
require_once(Mage::getModuleDir('controllers', 'TBT_Rewards') . DS . 'Admin' . DS . 'AbstractController.php');
class TBT_Rewards_Adminhtml_Sales_OrderController extends TBT_Rewards_Admin_AbstractController {
	const EXPORT_FILE_NAME = 'order_transfers';
	
	/**
	 * Export product grid to CSV format
	 */
	public function exportCsvAction() {
		$fileName = self::EXPORT_FILE_NAME . '-' . date ( "m.d.y.H.i.s" ) . '.xml';
		$content = $this->getLayout ()->createBlock ( 'rewards/adminhtml_sales_order_view_tab_points' );
		$csv = $content->getCsv ();
		
		$this->_sendUploadResponse ( $fileName, $csv );
	}
	
	/**
	 * Export product grid to XML format
	 */
	public function exportXmlAction() {
		$fileName = self::EXPORT_FILE_NAME . '-' . date ( "m.d.y.H:i:s" ) . '.xml';
		$content = $this->getLayout ()->createBlock ( 'rewards/adminhtml_sales_order_view_tab_points' );
		$xml = $content->getXml ();
		
		$this->_sendUploadResponse ( $fileName, $xml );
	}
	
	protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
		$response = $this->getResponse ();
		$response->setHeader ( 'HTTP/1.1 200 OK', '' );
		$response->setHeader ( 'Pragma', 'public', true );
		$response->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true );
		$response->setHeader ( 'Content-Disposition', 'attachment; filename=' . $fileName );
		$response->setHeader ( 'Last-Modified', date ( 'r' ) );
		$response->setHeader ( 'Accept-Ranges', 'bytes' );
		$response->setHeader ( 'Content-Length', strlen ( $content ) );
		$response->setHeader ( 'Content-type', $contentType );
		$response->setBody ( $content );
		$response->sendResponse ();
		die ();
	}
	
	public function transfersGridAction() {
		$id = $this->getRequest ()->getParam ( 'order_id' );
		$model = Mage::getModel ( 'sales/order' );
		if ($id) {
			$model->load ( $id );
		}
		Mage::register ( 'order', $model );
		$this->getResponse ()->setBody ( $this->getLayout ()->createBlock ( 'rewards/adminhtml_sales_order_view_tab_points' )->toHtml () );
	}
	
	public function changePointsSpendingAction()
	{
		$new_points_spending = $this->getRequest()->getParam("points_spending");
		$quote = $this->_getRewardsSession()->getQuote();
		$quote->setPointsSpending($new_points_spending);
		
		// if there are still products in the shopping cart
		if ($quote->getItemsCount()) {
			$rewardsQuote = Mage::getModel('rewards/sales_quote');
			
			$rewardsQuote->updateItemCatalogPoints($quote);
			
			$quote->collectTotals();
			$quote->getShippingAddress()->setCollectShippingRates(true);
			$quote->getShippingAddress()->collectShippingRates();
			
			$rewardsQuote->updateDisabledEarnings($quote);
		}
	}
	
	/**
	 * Adds a series of rule ids to the cart after validating them against the customers point balance
	 * @param string $rule_ids
	 */
	public function cartaddAction()
	{
		Varien_Profiler::start("TBT_Rewards:: Add shopping cart redemption to cart");
		$rule_ids = $this->getRequest()->get('rids');
		
		try {
			$customer = $this->_getRewardsSession()->getSessionCustomer();
			if (!$customer->getId()) {
				Mage::getSingleton('adminhtml/session')->addError($this->__("Must select an existing customer to spend points."));
				$this->_redirectReferer();
				return $this;
			}
			
			if (empty($rule_ids) && $rule_ids != 0) {
				throw new Exception($this->__("A valid redemption id to apply to this order was not selected."));
			}
			if (!is_array($rule_ids)) {
				$rule_list = explode(",", $rule_ids); //Turn the string of rule ids into an array
			}
			
			$quote = $this->_getRewardsSession()->getQuote();
			$store = $quote->getStore();
			
			//Load in a temp summary of the customers point balance, so we can check to see if the applied rules will overdraw their points
			$customer_point_balance = array();
			foreach (Mage::getSingleton('rewards/currency')->getAvailCurrencyIds() as $currency_id) {
				$customer_point_balance[$currency_id] = $customer->getUsablePointsBalance($currency_id);
			}
			$currency_captions = Mage::getSingleton('rewards/currency')->getAvailCurrencies();
			
			$flag = true;
			$doSave = false;
			foreach ($rule_list as $rule_id) {
				$rule = Mage::helper('rewards/rule')->getSalesRule($rule_id);
				
				//If the rule does not apply to the cart add that to the error message
				if (array_search((int) $rule_id, explode(',', $quote->getCartRedemptions())) === false) {
					Mage::getSingleton('adminhtml/session')->addError($this->__("The rule %s does not apply to this order.", $rule->getStoreLabel($store) ? $rule->getStoreLabel($store) : $rule->getName()));
				} else {
					if ($customer_point_balance[$rule->getPointsCurrencyId()] < $rule->getPointsAmount()) {
						Mage::getSingleton('adminhtml/session')->addError(
							$this->__("You do not have enough %s Points.", $currency_captions[$rule->getPointsCurrencyId()])
							. "<br/>\n"
							. $this->__("The rule entitled '%s' was not applied to the order.", $rule->getStoreLabel($store) ? $rule->getStoreLabel($store) : $rule->getName())
						);
						$flag = false;
					} else {
						$applied = Mage::getModel('rewards/salesrule_list_applied')->initQuote($quote);
						$applied->add($rule_id)->saveToQuote($quote);
						$doSave = true;
					}
				}
			}
			//If the customer does not have enough points to complete the redemption
			if (!$flag) {
				// At least one of the redemption rules that were applied could not be completed because the customer did not have enough points
			}
			if ($doSave) {
				$quote->getShippingAddress()->setCollectShippingRates(true);
				$quote->save();
				
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__("All requested reward redemptions were applied to the order."));
			}
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($this->__("An error occurred while trying to apply the redemption to the order."));
			Mage::getSingleton('adminhtml/session')->addError($this->__($e->getMessage()));
		}
		Varien_Profiler::stop("TBT_Rewards:: Add shopping cart redemption to cart");
		$this->_redirectReferer();
	}
	
	public function cartremoveAction()
	{
		Varien_Profiler::start("TBT_Rewards:: remove shopping cart redemption from cart");
		$rule_ids = $this->getRequest()->get('rids');
		try {
			if (!is_array($rule_ids)) {
				$rule_list = explode(",", $rule_ids); //Turn the string of rule ids into an array
			}
			
			$quote = $this->_getRewardsSession()->getQuote();
			$store = $quote->getStore();
			
			$flag = true;
			$doSave = false;
			foreach ($rule_list as $rule_id) {
				$rule = Mage::helper('rewards/rule')->getSalesRule($rule_id);
				$applied_redemptions = explode(',', $quote->getAppliedRedemptions());
				$applicable_redemptions = explode(',', $quote->getCartRedemptions());
				
				//If the rule does not apply to the cart add it to the error message
				if (array_search((int) $rule_id, $applied_redemptions) === false) {
					Mage::getSingleton('adminhtml/session')->addError($this->__("The rule %s was not applied to the order.", $rule->getStoreLabel($store) ? $rule->getStoreLabel($store) : $rule->getName()));
				} else {
					// index at which the possibly removable rule id was found.
					$applied = Mage::getModel('rewards/salesrule_list_applied')->initQuote($quote);
					$applied->remove($rule_id)->saveToQuote($quote);
					$doSave = true;
				}
			}
			//If the customer does not have enough points to complete the redemption
			if (!$flag) {
				// At least one of the redemption rules that were applied could not be completed because the customer did not have enough points
			}
			if ($doSave) {
				$quote->getShippingAddress()->setCollectShippingRates(true);
				$quote->save();
				
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__("All requested reward redemptions were removed from the order."));
			}
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($this->__("An error occurred while trying to remove the redemption from the order."));
			Mage::getSingleton('adminhtml/session')->addError($this->__($e->getMessage()));
		}
		Varien_Profiler::stop("TBT_Rewards:: remove shopping cart redemption from cart");
		$this->_redirectReferer();
	}
	
	/**
	 * @return TBT_Rewards_Model_Session
	 */
	protected function _getRewardsSession()
	{
		return Mage::getSingleton('rewards/session');
	}
}
