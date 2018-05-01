<?php

/**
 * WDCA - Sweet Tooth
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS
 * License, which extends the Open Software License (OSL 3.0).
 * The Sweet Tooth License is available at this URL:
 * https://www.sweettoothrewards.com/terms-of-service
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
 * Product View Points
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Block_Product_View_Points_Earned extends TBT_Rewards_Block_Product_View_Points_Abstract
{
    /**
     * Calculates all the points being earned from distribution rules.
     *
     * @return unknown
     */
    public function getDistriRules()
    {
        return $this->getProduct ()->getDistriRules ();
    }

    /**
     * Get distribution rule rewards.
     * Sums up the rewards in the standard currency=>amt array format
     *
     * @return array
     */
    public function getDistriRewards()
    {
        return $this->getProduct ()->getDistriRewards ();
    }

    /**
     * Checks if product is configurable, bundle or simple with custom options, so that we display earning on catalog
     * page like 'Earn starting from X points for buying this product'
     *
     * @return bool
     */
    public function getShowEarningFrom()
    {
        if (Mage::helper('rewards')->isBaseMageVersionAtLeast('1.4.0.0') == false) {
            return false;
        }
        $has = $this->getProduct()->isComposite() && $this->getProduct()->getHasOptions();

        return $has;
    }
}