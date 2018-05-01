<?php

/**
 * WDCA - Sweet Tooth
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS 
 * License, which extends the Open Software License (OSL 3.0).
 * The Sweet Tooth License is available at this URL: 
 *      https://www.sweettoothrewards.com/terms-of-service
 * The Open Software License is available at this URL: 
 *      http://opensource.org/licenses/osl-3.0.php
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
 * Redeem
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_RewardsOnly_Model_Redeem extends TBT_RewardsOnly_Model_Redeem {

    /**
     * Retenders the item's redemption rules and final row total and returns it.
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array a map of the new item redemption data: 
     * 					array('redemptions_data'=>{...}, 'row_total'=>float)
     */
    protected function getUpdatedRedemptionData($item, $do_incl_tax = true)
    {
        $redeemed_points = Mage::helper('rewards')->unhashIt($item->getRedeemedPointsHash());
        
        // Loop through and force applicable qty == item qty for points-only products
        foreach ($redeemed_points as $key => &$redemption_instance) {
            $redemption_instance = (array) $redemption_instance;
            $rule_id = $redemption_instance [self::POINTS_RULE_ID];
            $rule = Mage::helper ( 'rewards/rule' )->getCatalogRule ( $rule_id );
            
            if($this->isOneRedemptionMode() && $rule->getPointsOnlyMode()) { 
                // The total quantity that the redemption instance should apply to should be equal
                // to the total quantity in the item
                $redemption_instance[self::POINTS_APPLICABLE_QTY] = $item->getQty();
            }
        }
        
        $item->setRedeemedPointsHash(Mage::helper('rewards')->hashIt($redeemed_points));
        
        return parent::getUpdatedRedemptionData($item, $do_incl_tax);
    }
    
    
    public function isOneRedemptionMode() {
    	$points_as_price =  Mage::helper('rewardsonly/config')->showPointsAsPrice();
    	$one_redemption_only = Mage::helper('rewardsonly/config')->forceOneRedemption();
    	$force_redemptions = Mage::helper('rewardsonly/config')->forceRedemptions();
    	$is_one_redemption_mode = ($points_as_price && $one_redemption_only && $force_redemptions);
    	return $is_one_redemption_mode;
    }
	
    
}
