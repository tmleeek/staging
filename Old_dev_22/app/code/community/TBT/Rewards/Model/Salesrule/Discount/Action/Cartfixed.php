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
 * Shopping Cart Rule Validator
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Model_Salesrule_Discount_Action_Cartfixed extends TBT_Rewards_Model_Salesrule_Discount_Action_Abstract {

    /**
     * Information about item totals for rules.
     * @var array
     */
    protected $_rewardsRulesItemTotals = array();

    public function applyDiscounts(&$cartRules, $address, $item, $rule, $qty) {

        // WDCA CODE BEGIN

        if (! isset ( $cartRules [$rule->getId ()] )) {
            $this->initRewardsTotals($address->getAllItems(), $rule);
            $cartRules [$rule->getId ()] = $this->_getTotalFixedDiscountOnCart ( $item, $address, $rule );
        }
        //@nelkaake Wednesday May 5, 2010 RM:
        if ($cartRules [$rule->getId ()] <= 0) {
            return array(0,0);
        }

        list ( $discountAmount, $baseDiscountAmount ) = $this->_getTotalFixedDiscountOnitem ( $item, $address, $rule, $cartRules );

        if($cartRules [$rule->getId ()] - $baseDiscountAmount >= 0) {
            $cartRules [$rule->getId ()] -= $baseDiscountAmount;
        } else {
            $baseDiscountAmount = $cartRules [$rule->getId ()];
            $discountAmount = $item->getQuote()->getStore()->convertPrice($baseDiscountAmount);
            $cartRules [$rule->getId ()] = 0;
        }

        //@nelkaake -a 11/03/11: Save our discount due to spending points
        if ($rule->getPointsAction () == TBT_Rewards_Model_Salesrule_Actions::ACTION_DISCOUNT_BY_POINTS_SPENT) {
            $new_total_rsd = (float)$address->getTotalBaseRewardsSpendingDiscount();
            $new_total_rsd = $new_total_rsd + $baseDiscountAmount;
            $address->setTotalBaseRewardsSpendingDiscount($new_total_rsd);
        }

        return array ( $discountAmount, $baseDiscountAmount );
    }

    public function calcItemDiscount($item, $address, $rule, $qty = null){
        return $this->_getTotalFixedDiscountOnCart ( $item, $address, $rule );
    }
    public function calcCartDiscount($item, $address, $rule, &$cartRules, $qty = null) {
        return $this->_getTotalFixedDiscountOnitem ( $item, $address, $rule, $cartRules );
    }

    /**
     * Calculate quote total for rule and save results
     *
     * @param mixed $items
     * @param TBT_Rewards_Model_Salesrule_Rule $rule
     * @return TBT_Rewards_Model_Salesrule_Discount_Action_Cartfixed
     */
    protected function initRewardsTotals($items, $rule) {

        if (!$items) {
            return $this;
        }

        $ruleTotalItemsPrice = 0;
        $ruleTotalBaseItemsPrice = 0;
        $validItemsCount = 0;

        foreach ($items as $item) {
            //Skipping child items to avoid double calculations
            if ($item->getParentItemId()) {
                continue;
            }
            if (!$rule->getActions()->validate($item)) {
                continue;
            }

            $qty = $this->_getItemQty($item, $rule);

            $ruleTotalItemsPrice += $this->_getItemPrice($item) * $qty;
            $ruleTotalBaseItemsPrice += $this->_getItemBasePrice($item) * $qty;
            // we also need to subtract any discounts form rules with higher priority
            $ruleTotalItemsPrice -= $item->getDiscountAmount();
            $ruleTotalBaseItemsPrice -= $item->getBaseDiscountAmount();

            $validItemsCount++;
        }

        $this->_rewardsRulesItemTotals[$rule->getId()] = array(
            'items_price' => $ruleTotalItemsPrice,
            'base_items_price' => $ruleTotalBaseItemsPrice,
            'items_count' => $validItemsCount,
        );

        return $this;
    }

    /**
     * Returns a total discount on the cart from the provided items
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @param Mage_Sales_Model_Quote_Address $address
     * @param TBT_Rewards_Model_Sales_Rule $rule
     * @return float
     */
    protected function _getTotalFixedDiscountOnCart($item, $address, $rule) {

        if ( $rule->getPointsAction() == TBT_Rewards_Model_Salesrule_Actions::ACTION_DISCOUNT_BY_POINTS_SPENT ) {
            $points_spent = $item->getQuote()->getPointsSpending();
            $totalDiscountOnCart = $rule->getPointsDiscountAmount() * floor(($points_spent / $rule->getPointsAmount()));
        } else {
            $totalDiscountOnCart = $rule->getPointsDiscountAmount();
        }

        return $totalDiscountOnCart;
    }
    /**
     * Returns a total discount on the cart from the provided items
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @param Mage_Sales_Model_Quote_Address $address
     * @param TBT_Rewards_Model_Sales_Rule $rule
     * @param array() &$cartRules
     * @return array($discountAmount, $baseDiscountAmount)
     */
    protected function _getTotalFixedDiscountOnitem($item, $address, $rule, &$cartRules) {
        $quote = $item->getQuote();
        $store = $item->getQuote()->getStore();

        if ( $rule->getPointsAction() == TBT_Rewards_Model_Salesrule_Actions::ACTION_DISCOUNT_BY_POINTS_SPENT ) {
            $points_spent = $item->getQuote()->getPointsSpending();
            $multiplier = floor(($points_spent / $rule->getPointsAmount()));
        } else {
            $multiplier = 1;
        }

        list($item_row_total, $item_base_row_total) = $this->_getDiscountableRowTotal($address, $item, $rule);

        if (is_array($this->_rewardsRulesItemTotals[$rule->getId()])) {
            // if more than 1 item is in cart, splitting the discount proportionally between items after Magento's model
            // for more details check ST-1229
            if ($this->_rewardsRulesItemTotals[$rule->getId()]['items_count'] <= 1) {
                $quoteAmount = $quote->getStore()->convertPrice($cartRules[$rule->getId()]);
                $baseDiscountAmount = min($item_base_row_total, $cartRules[$rule->getId()]);
            } else {
                $discountRate = $item_base_row_total / ($this->_rewardsRulesItemTotals[$rule->getId()]['base_items_price'] - $quote->getRewardsBaseDiscountAmount());
                $maximumItemDiscount = $rule->getPointsDiscountAmount() * $multiplier * $discountRate;
                $quoteAmount = $quote->getStore()->convertPrice($maximumItemDiscount);

                $baseDiscountAmount = min($item_base_row_total, $maximumItemDiscount);
                $this->_rewardsRulesItemTotals[$rule->getId()]['items_count']--;
            }
        }

        $discountAmount = min($item_row_total , $quoteAmount);

        $discountAmount = $quote->getStore()->roundPrice($discountAmount);
        $baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);

        return array($discountAmount, $baseDiscountAmount);
    }


}
