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
 * Helper Transfer
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Helper_Rule extends Mage_Core_Helper_Abstract
{

    protected $_cr = array ();
    protected $_sr = array ();

    /**
     * Fetches a cached shopping cart rule model
     *
     * @param integer $rule_id
     * @return TBT_Rewards_Model_Salesrule_Rule
     */
    public function &getSalesRule($rule_id)
    {
        //@nelkaake -a 11/03/11: Failsafe for accidentally setting the param as a rule model instead of id.
        if ($rule_id instanceof Mage_SalesRule_Model_Rule) {
            $rule_id = $rule_id->getId ();
        }

        if (! isset ( $this->_sr [$rule_id] )) {
            $this->_sr [$rule_id] = Mage::getModel ( 'rewards/salesrule_rule' )->load ( $rule_id );
        }

        return $this->_sr [$rule_id];
    }

    /**
     * Fetches a cached catalog rule model
     *
     * @param integer $rule_id
     * @return TBT_Rewards_Model_Catalogrule_Rule
     */
    public function &getCatalogRule($rule_id)
    {
        //@nelkaake -a 11/03/11: Failsafe for accidentally setting the param as a rule model instead of id.
        if ($rule_id instanceof Mage_CatalogRule_Model_Rule) {
            $rule_id = $rule_id->getId ();
        }

        if (! isset ( $this->_cr [$rule_id] )) {
            $this->_cr [$rule_id] = Mage::getModel ( 'rewards/catalogrule_rule' )->load ( $rule_id );
        }

        return $this->_cr [$rule_id];
    }

    /**
     * Validates that a catalog rule is properly configured based on other admin settings.
     * This can be used to auto-update Magento config settings in the event that yoru settings are not ready.
     **/
    public function validateCatalogruleAdminSettings($rule, $displayWarning = true)
    {
        if ($rule instanceof Mage_CatalogRule_Model_Rule) {

            // If it's a give by profit rule make sure the config is setting the cost attribute as used_in_product_listing=true
            if ($rule->getPointsAction() == 'give_by_profit') {
                $attributeCode = 'cost';
                $_entityTypeId = Mage::getModel('eav/entity')->setType(Mage_Catalog_Model_Product::ENTITY)->getTypeId();
                $attrModel = Mage::getModel('catalog/resource_eav_attribute')->loadByCode($_entityTypeId, $attributeCode);

                if (!$attrModel->getUsedInProductListing()) {
                    $attrModel->setUsedInProductListing(true)->save();

                    $manageAttrUrl = Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product_attribute/edit', array('attribute_id' => $attrModel->getId()));

                    $warningMsg = Mage::helper('rewards')->__ ("We enabled the 'Used in Product Listing' option for the 'cost' attribute configuration of your store in order to optimize speed for your rewards rules that rely on product profits. <br />To disable this again, you can manage your product 'cost' attribute in [link]<i>Catalog > Attributes > Manage atrributes</i>[/link] section.");
                    $warningMsg = str_replace('[link]', "<a href='". $manageAttrUrl ."'>", $warningMsg);
                    $warningMsg = str_replace('[/link]', "</a>", $warningMsg);

                    if ($displayWarning) {
                        Mage::getSingleton ( 'adminhtml/session' )->addNotice($warningMsg);
                    }

                    return true;
                }
            }

        }

        return false;
    }


    /**
     * True if the store has any cart points rules enabled.
     *
     * @return boolean
     */
    public function storeHasAnyPointsShoppingCartRules($storeId = null)
    {
        $all_srules = Mage::getModel ( 'rewards/salesrule_rule' )->getPointsRuleCollection ( true );

        $websiteId = $this->_getWebsiteId();
        if ($storeId !== null) {
            $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        }

        if (Mage::helper ( 'rewards/version' )->isMageVersionBetween ( '1.3.0.0', '1.3.1.0' )) {
            $this->filterOutRulesByWebsiteId ( $all_srules, $websiteId );
        } else {
            $all_srules->addWebsiteFilter ( $websiteId );
        }

        $has_srules = sizeof ( $all_srules ) > 0;

        return $has_srules;
    }

    /**
     * Filter collection by specified website IDs
     *
     * @param Mage_CatalogRule_Model_Mysql4_Rule_Collection (or Salesrule collection) &$collection
     * @param int|array $websiteIds
     * @return $this
     */
    public function filterOutRulesByWebsiteId(&$collection, $websiteIds)
    {
        if (! is_array ( $websiteIds )) {
            $websiteIds = array ($websiteIds );
        }
        foreach ( $websiteIds as $websiteId ) {
            foreach ( $collection as &$item ) {

                $itemWebsiteIds = $item->getWebsiteIds ();
                //@nelkaake -a 1/12/10: If the item website ids is not an array for some reason make it into an array.
                if (! is_array ( $itemWebsiteIds )) {
                    $itemWebsiteIds = explode ( ",", $itemWebsiteIds );
                }

                if (array_search ( $websiteId, $itemWebsiteIds ) === false) {
                    $collection->removeItemByKey ( $item->getId () );
                }
            }
        }

        return $this;
    }

    /**
     * True if the store has any catalog points rules enabled.
     *
     * @return boolean
     */
    public function storeHasAnyPointsCatalogRules()
    {
        $all_crules = Mage::getModel ( 'catalogrule/rule' )->getCollection ()->addFieldToFilter ( "points_action", array ('neq' => '' ) )->addFilter ( "is_active", '1' );

        if (Mage::helper ( 'rewards/version' )->isMageVersionBetween ( '1.3.0.0', '1.3.1.0' )) {
            $this->filterOutRulesByWebsiteId ( $all_crules, $this->_getWebsiteId () );
        } else {
            $all_crules->addWebsiteFilter ( $this->_getWebsiteId () );
        }

        $has_cat_rules = sizeof ( $all_crules ) > 0;

        return $has_cat_rules;
    }

    /**
     * True if the store has any catalog points distribution rules enabled.
     *
     * @return boolean
     */
    public function storeHasAnyCatalogDistriRules()
    {
        $all_distri_crules = Mage::getModel ( 'catalogrule/rule' )->getCollection ()->addFieldToFilter ( "points_action", array ('IN' => Mage::getModel ( 'rewards/catalogrule_actions' )->getDistributionActions () ) )->addFilter ( "is_active", '1' );

        if (Mage::helper ( 'rewards/version' )->isMageVersionBetween ( '1.3.0.0', '1.3.1.0' )) {
            $this->filterOutRulesByWebsiteId ( $all_distri_crules, $this->_getWebsiteId () );
        } else {
            $all_distri_crules->addWebsiteFilter ( $this->_getWebsiteId () );
        }
        $has_cat_rules = sizeof ( $all_distri_crules ) > 0;

        return $has_cat_rules;
    }

    /**
     * Current session website id
     *
     * @return int
     */
    protected function _getWebsiteId()
    {
        return Mage::app ()->getWebsite ()->getId ();
    }

    /**
     * Generates an array map of cart redemption actions so that a proper point sstring can be created.
     *
     * @param TBT_Rewards_Model_Salesrule_Rule $salesrule
     * @param Mage_Checkout_Model_Cart $cart
     * @return array
     */
    public function getQuickCartRedemEntry($salesrule, $cart = null)
    {
        if ($cart == null) {
            $cart = Mage::getSingleton ( 'rewards/session' )->getQuote ();
        }
        $val = array ();
        $points = Mage::getSingleton ( 'rewards/session' )->calculateCartPoints ( $salesrule->getId (), $cart->getAllItems (), true, true );
        $val = $points;
        $val ['name'] = $salesrule->getName ();
        if ($salesrule->getSimpleFreeShipping ()) {
            $val ['action_str'] = Mage::helper ( 'rewards' )->__ ( "Free Shipping" );
        } else {
            $discount_amount = Mage::helper ( 'rewards/transfer' )->calculateCartDiscounts ( $salesrule->getId (), $cart->getAllItems (), null, true );
            $discount_amount = (($discount_amount < 0) ? (- 1 * $discount_amount) : $discount_amount);

            //@nelkaake Added on Wednesday May 5, 2010:
            //@nelkaake Changed on Wednesday May 5, 2010:
            if (strpos ( $salesrule->getPointsDiscountAction (), "percent" ) !== false) {
                $percent = round ( $discount_amount );
                if (Mage::getSingleton ( 'rewards/salesrule_actions' )->isDeductByAmountSpentAction ( $salesrule->getPointsAction () )) {
                    $percent = round ( $salesrule->getPointsDiscountAmount () );
                }
                $discount_amount_str = Mage::helper ( 'rewards' )->__ ( '%s%%', $percent );
            } else {
                //@nelkaake Added on Sunday May 30, 2010:
                $discount_amount = Mage::app ()->getStore ()->convertPrice ( $discount_amount );
                $discount_amount = Mage::app ()->getStore ()->roundPrice ( $discount_amount );
                $discount_amount_str = Mage::app ()->getStore ()->formatPrice ( $discount_amount );
            }
            $val ['action_str'] = Mage::helper ( 'rewards' )->__ ( "%s Off", $discount_amount_str );
        }

        $points_amt = (($points ['amount'] < 0) ? ($points ['amount'] * - 1) : $points ['amount']);
        $val ['points_cost'] = ( string ) (Mage::getModel ( 'rewards/points' )->set ( $points ['currency'], $points_amt ));
        $val ['is_coupon'] = $salesrule->getCouponCode ();
        $val ['is_coupon'] = ! empty ( $val ['is_coupon'] );
        $val ['is_dbps'] = ($salesrule->getPointsAction () == 'discount_by_points_spent');

        if (Mage::helper ( 'rewards/version' )->isMageVersionAtLeast ( '1.4.0.0' )) {
            $val ['caption'] = $salesrule->getStoreLabel ();
        }

        return $val;
    }

    /**
     * Safe method of checking if a rule is a points rule.
     * @param TBT_Rewards_Model_Salesrule_Rule|TBT_Rewards_Model_Catalogrule_Rule|Mage_CatalogRule_Model_Rule|Mage_SalesRule_Model_Rule $rule
     */
    public function isPointsRule($rule)
    {
        if ($rule instanceof TBT_Rewards_Model_Salesrule_Rule || $rule instanceof TBT_Rewards_Model_Catalogrule_Rule) {
            return $rule->isPointsRule ();
        }
        if ($rule instanceof Mage_CatalogRule_Model_Rule) {
            $rule = Mage::helper ( 'rewards/rule' )->getCatalogrule ( $rule->getId () );
            return $rule->isPointsRule ();
        }
        if ($rule instanceof Mage_SalesRule_Model_Rule) {
            $rule = Mage::helper ( 'rewards/rule' )->getSalesRule ( $rule->getId () );
            return $rule->isPointsRule ();
        }

        Mage::logException ( new Exception ( "Reached Helper_Rule::isPointsRule() with parameter that is not a catalog rule or cart or catalog rule." ) );

        return $this;
    }

    /**
     * Sorts the array cart rules information map using usort from lowest points to highest points.
     * @see compareRulePointsCost
     * @see getQuickCartRedemEntry
     *
     * @param array $entries
     * @return array
     */
    public function sortQuickCartRedemEntries($entries)
    {
        usort ( $entries, array ($this, 'compareRulePointsCost' ) );
        $sorted = $entries;

        return $sorted;
    }

    /**
     * Used in sorting or getQuickCartRedemEntry generated maps
     * @param array $a
     * @param array $b
     * @return boolean true if 'amount' key for a is greater than 'amount' key for b.
     */
    static function compareRulePointsCost($a, $b)
    {
        if(!isset($a['amount']) || !isset($b['amount'])) { // failsafe, but generally should not be in this func if the entry is not there
            return true;
        }

        return ($a['amount'] > $b['amount']);
    }

}