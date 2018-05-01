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
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Model_Review_Behavior extends TBT_Rewards_Model_Special_Configabstract {
	
	const ACTION_CODE = 'customer_writes_review';
	
	public function _construct()
	{
	    $helper = Mage::helper('rewards');
		$this->setCaption('Review writing');
		$this->setDescription('Customer will get points when they write a review.');
		$this->setCode(self::ACTION_CODE);
		return parent::_construct();
	}
	
	public function getNewCustomerConditions()
	{
		return array(
		    self::ACTION_CODE => Mage::helper('rewards')->__('Writes a review')
		);
	}
	
	public function visitAdminConditions(&$fieldset)
	{
		return $this;
	}
	
	public function visitAdminActions(&$fieldset)
	{
		return $this;
	}
	
	public function getNewActions()
	{
		return array ();
	}
	
	public function getAdminFormScripts()
	{
		return array ();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see TBT_Rewards_Model_Special_Configabstract::getAdminFormInitScripts()
	 */
	public function getAdminFormInitScripts()
	{
		return array ();
	}
}