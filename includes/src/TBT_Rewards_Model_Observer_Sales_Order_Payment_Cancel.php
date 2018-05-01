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
 * Observer Sales Order Payment Cancel
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Model_Observer_Sales_Order_Payment_Cancel extends Varien_Object
{
    /**
     * @deprecated due to fundamental changes through ST-964 and ST-950, check
     * TBT_Rewards_Model_Sales_Order_Payment_Observer::automaticCancel()
     *
     * @param Varien_Event_Observer $observer
     * @return self
     */
	public function revokeAssociatedPendingTransfersOnCancel($observer)
	{
		return $this;
	}
	
	/**
	 * remove messages of pending points because they were revoked
	 * @return $this
	 */
	protected function _cancelDispatchedMsgs() {
        Mage::getSingleton ( 'core/session' )->getMessages()->deleteMessageByIdentifier('TBT_Rewards_Model_Observer_Sales_Order_Save_After_Create(pending points)');
       // Mage::getSingleton ( 'core/session' )->addSuccess ( Mage::helper ( 'rewards' )->__ ( 'Successfully cancelled pending point transactions' ) );
        
	     return $this;
	}

}
