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
 * Product Predict Points
 * options:
 * - setHideEarning(true); // hides earning line.
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
abstract class TBT_Rewards_Block_Product_List_Abstract extends Mage_Core_Block_Template
{

    protected $_customer = null;

    /**
     * Checkes whether the Points Optimizer should be shown in Product List page. Depends on config option in admin
     * 'Show Points Optimizer' and if any rule applies to the user.
     *
     * @return bool
     */
    public function doShowAsLowAs()
    {
        $hasRulesForCustomer = $this->getHasRulesForCustomer($this->getCurrentCustomer());
        $configShowOptimizer = Mage::getStoreConfigFlag('rewards/display/showPointsOptimizer');

        return $hasRulesForCustomer && $configShowOptimizer;
    }

    public function doGraphicalEarning()
    {
        return Mage::getStoreConfigFlag ( 'rewards/display/showEarningGraphic' );
    }

    /**
     * Set the product to create the predict points block for.
     *
     * @param TBT_Rewards_Model_Catalog_Product $_product
     */
    public function setProduct($_product)
    {
        if (! ($_product instanceof TBT_Rewards_Model_Catalog_Product)) {
            $_product = TBT_Rewards_Model_Catalog_Product::wrap ( $_product );
        }
        $this->product = $_product;

        return $this;
    }


    public function getPredictedPoints()
    {
        if ($this->product) {
            $predict = $this->product->getRewardAdjustedPrice ();
        } else {
            $predict = array ('points_price' => 0, 'points_string' => "" );
        }

        return $predict;
    }

    public function getPredictedPointsEarned()
    {
        if ($this->product) {
            $earnable = $this->product->getEarnablePoints();
        } else {
            $earnable = array ();
        }

        return $earnable;
    }

    public function hasEarnablePoints()
    {
        $earnable_points = $this->getPredictedPointsEarned ();
        $has_earnable_points = (sizeof ( $earnable_points ) > 0);
        return $has_earnable_points;
    }

    public function getEarnablePointsString()
    {
        $earnable_points = $this->getPredictedPointsEarned ();
        $earnable_points_str = Mage::helper ( 'rewards' )->getPointsString ( $earnable_points );

        return $earnable_points_str;
    }

    /**
     * @nelkaake -a 5/11/10:
     * @return TBT_Rewards_Model_Catalog_Product
     */
    public function getProduct()
    {
        return ($this->product == null ? Mage::getModel ( 'rewards/catalog_product' ) : $this->product);
    }

    /**
     * Check if any rule applies for current customer and product
     *
     * @param  TBT_Rewards_Model_Customer $customer
     * @return bool True if any rules applies for customer, false otherwise
     */
    public function getHasRulesForCustomer($customer)
    {
        if (!$this->product) {
            return false;
        }

        $rules = $this->product->getCatalogRedemptionRules($customer);

        return !empty($rules);
    }

    /**
     * Fetches the current session customer
     *
     * @return TBT_Rewards_Model_Customer
     */
    public function getCurrentCustomer()
    {
        if ($this->_customer) {
            return $this->_customer;
        }

        if ($this->_getRS ()->isCustomerLoggedIn ()) {
            $this->_customer = Mage::getModel('rewards/customer')->getRewardsCustomer($this->_getRS()->getSessionCustomer());
        }

        return $this->_customer;
    }

    /**
     * Fetches the rewards session singleton
     * @nelkaake -a 5/11/10:      *
     *
     * @return TBT_Rewards_Model_Session
     */
    protected function _getRS()
    {
        return Mage::getSingleton ( 'rewards/session' );
    }

}

