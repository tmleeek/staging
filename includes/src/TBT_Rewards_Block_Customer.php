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
 * Customer
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Block_Customer extends Mage_Core_Block_Template {
	
	protected function _construct() {
		$this->_controller = 'customer';
		$this->_blockGroup = 'rewards';
		parent::_construct ();
	}

	//    protected function _prepareLayout()
//    {
//        $summary_block = $this->getLayout()->createBlock('rewards/customer_summary', 'customer.summary');
//        $this->setChild('summary', $summary_block);
//        
//        $spendings_block = $this->getLayout()->createBlock('rewards/customer_transfers_spendings', 'customer.spendings');
//        $this->setChild('spendings', $spendings_block);
//        
//        $earnings_block = $this->getLayout()->createBlock('rewards/customer_transfers_earnings', 'customer.earnings');
//        $this->setChild('earnings', $earnings_block);
//        
//        $sendpoints_block = $this->getLayout()->createBlock('rewards/customer_sendpoints', 'customer.sendpoints');
//        $this->setChild('sendpoints', $sendpoints_block);
//
//        
//        $sendpoints_block = $this->getLayout()->createBlock('rewards/customer_sendpoints', 'customer.sendpoints');
//        $this->setChild('sendpoints', $sendpoints_block);
//        
//    	parent::_prepareLayout();
//    }
}
