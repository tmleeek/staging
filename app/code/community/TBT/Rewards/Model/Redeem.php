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
 * Redeem
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Model_Redeem extends Mage_Core_Model_Abstract
{
    const POINTS_RULE_ID = TBT_Rewards_Model_Catalogrule_Rule::POINTS_RULE_ID;
    const POINTS_APPLICABLE_QTY = TBT_Rewards_Model_Catalogrule_Rule::POINTS_APPLICABLE_QTY;
    const POINTS_EFFECT = TBT_Rewards_Model_Catalogrule_Rule::POINTS_EFFECT;
    const POINTS_USES = TBT_Rewards_Model_Catalogrule_Rule::POINTS_USES;
    const SALES_FLAT_QUOTE_ITEM = "sales_flat_quote_item";

    /**
     * Tax calculation model
     *
     * @var Mage_Tax_Model_Calculation
     */
    protected $_taxCalculator = null;

    /**
     * Tax helper
     * @var Mage_Tax_Helper_Data
     */
    protected $_taxHelper = null;

    /**
     * Tax config model
     * @var Mage_Tax_Model_Config
     */
    protected $_taxConfig = null;

    protected function _construct()
    {
        $this->_taxCalculator = Mage::getSingleton('tax/calculation');
        $this->_taxHelper     = Mage::helper('tax');
        $this->_taxConfig     = Mage::getSingleton('tax/config');

        return $this;
    }

    /**
     * @deprecated Use refactorRedemptions($items, $doSave) instead
     */
    public function addCatalogRedemptionsToItem($item, $rule_id_list, $customer) {
        return false;
    }

    /**
     * Removes all applicable rules to the item's rule hash.
     * Returns false if no changes were made.
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @param array $rule_id_list
     * @param integer $inst_id redemption instance id (this comes out of the item redemptions hash)
     * @return boolean
     */
    public function removeCatalogRedemptionsFromItem(&$item, $rule_id_list, $inst_id = 0) {
        //Check to make sure we can load the redeem points hash alright
        if (! $item->getRedeemedPointsHash ()) {
            throw new Exception ( $this->__ ( "Unable to load the redeem points hash" ) );
        }
        $catalog_redemptions = Mage::helper ( 'rewards' )->unhashIt ( $item->getRedeemedPointsHash () );
        foreach ( $catalog_redemptions as $key => $redemption ) {
            $catalog_redemptions [$key] = ( array ) $redemption;
        }

        $doSave = false;

        foreach ( $rule_id_list as $rule_id ) {
            $rule = Mage::getModel ( 'rewards/catalogrule_rule' )->load ( $rule_id );
            $foundRuleIdIndex = false;
            foreach ( $catalog_redemptions as $index => $redemption ) {
                $rule_id_is_same = ($redemption [TBT_Rewards_Model_Catalogrule_Rule::POINTS_RULE_ID] == $rule_id);
                $inst_id_is_same = (($inst_id == 0) ? true : ($redemption [TBT_Rewards_Model_Catalogrule_Rule::POINTS_INST_ID] == $inst_id));
                if ($rule_id_is_same && $inst_id_is_same) {
                    $foundRuleIdIndex = $index;
                }
            }

            if ($foundRuleIdIndex === false) {
                throw new Exception ( "The rule entitled '" . $rule->getName () . "' is not applied to this product." );
            } else {
                unset ( $catalog_redemptions [$foundRuleIdIndex] );
                $item->setRedeemedPointsHash ( Mage::helper ( 'rewards' )->hashIt ( $catalog_redemptions ) );
                $doSave = true;
            }
        }

        if ($doSave) {
            $item->save ();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retenders the items listed in the item list
     *
     * @param array(Mage_Sales_Model_Quote_Item) $items
     */
    public function refactorRedemptions($items, $doSave = true) {
        if (! is_array ( $items )) {
            $items = array ($items );
        }

        foreach ( $items as $item ) {
            $this->refactorRedemption ( $item, $doSave );
            $this->refactorItemTax ( $item );
        }
        $this->refactorGrandTotal ( $items );
    }

    /**
     * If we need to, recalculates the tax for an item model.
     *
     * @param Mage_Sales_Model_Quote_Item $item
     */
    public function refactorItemTax(&$item) {
        //@nelkaake -a 13/01/11: Fixes a bug that occurs in Magento 1.3.1 where the cart is calculating
        // tax based on the amount before discounts instead of after discounts as configured.
        if (Mage::helper ( 'rewards/version' )->isMage ( '1.3.1.0' )) {
            if (Mage::helper ( 'tax' )->priceIncludesTax ()) {
                if (Mage::helper ( 'tax' )->applyTaxAfterDiscount ()) {
                    return $this;
                }
            }
        }

        /*if($item->getQuote())
            $item->calcTaxAmount();*/
        return $this;
    }

    /**
     * Retenders the item's redemption rules and final row total
     * @nelkaake Friday March 26, 2010 12:36:50 PM : Changed to protected vs private
     * @param Mage_Sales_Model_Quote_Item $persistentItem
     */
    public function refactorRedemption(&$persistentItem, $doSave = true) {
        // clone the item so any changes we make don't persist unless we want them to (ie: doSave)
        $item = $this->_cloneQuoteItem($persistentItem);

        // Write to the database the new item row information
        $r = $this->getUpdatedRedemptionData ( $item );
        $row_total = $r ['row_total'];
        $row_total_incl_tax = $r ['row_total_incl_tax'];
        //$row_total = $r['row_total_incl_tax'];
        $redems = $r ['redemptions_data'];

        //@nelkaake -a 3/03/11: Failsafe to make sure the total never drops below zero
        if ($row_total < 0)
            $row_total = 0;
        if ($row_total_incl_tax < 0)
            $row_total_incl_tax = 0;

        $this->resetItemDiscounts ( $item );

        $item->setRowTotal ( $row_total );
        $item->setRowTotalInclTax ( $row_total_incl_tax );
        if (!Mage::app()->getStore()->isAdmin()) {
            $item->setBaseRowTotal ( Mage::helper ( 'rewards/price' )->getReversedCurrencyPrice ( $row_total ) );
            $item->setBaseRowTotalInclTax ( Mage::helper ( 'rewards/price' )->getReversedCurrencyPrice ( $row_total_incl_tax ) );
        }

        $regular_discount = $item->getBaseDiscountAmount ();
        if (empty ( $regular_discount )) {
            $item->setRowTotalWithDiscount ( $item->getRowTotal () );
            $item->setBaseRowTotalWithDiscount ( $item->getBaseRowTotal () );
        }

        //@nelkaake -a 16/11/10:
        $this->_calcTaxAmounts ( $item );
        if ($doSave) {
            $persistentItem->setData($item->getData());
            $persistentItem->save();
        }
    }

    /**
     * Clones a sales/quote_item and then refills the fields that are automatically cleared
     * when that model gets cloned.  Performs some weird cloning logic on the item's parent as well,
     * because if we don't clone the parent, it will end up with double the children.
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return Mage_Sales_Model_Quote_Item_Abstract
     */
    protected function _cloneQuoteItem($item)
    {
        $clone = clone $item;

        if ($clone instanceof Mage_Sales_Model_Quote_Item_Abstract) {
            // annoyingly, a bunch of values are unset after the item gets cloned.  let's revert that.
            $clone->setId($item->getId())
                ->setQuote($item->getQuote());

            // if the original item had a parent, we need it too
            if ($item->getParentItem()) {
                // we need to clone it though, otherwise we'll double-up its children when we call setParentItem()
                // because that implicitly calls setChild() on it.  what a pain.
                $newParent = clone $item->getParentItem();
                $siblings = $item->getParentItem()->getChildren();
                if (is_array($siblings)) {
                    // just in case any of our code wants the parent to have all its children, let's add them
                    // (cloning the parent item clears its children)
                    // let's also try to keep the same order of children, just in case
                    foreach ($siblings as $sibling) {
                        // checks to see if this sibling is actually the current item
                        if ($sibling->compare($item)) {
                            $clone->setParentItem($newParent);
                        } else {
                            $newParent->addChild($sibling);
                        }
                    }
                }
            }

            // grab all the children from the original item and add them back
            $children = $item->getChildren();
            if (is_array($children)) {
                foreach ($children as $child) {
                    $clone->addChild($child);
                }
            }

            // grab all the "messages" from the original item and add them back
            $messages = $item->getMessage(false);
            if (is_array($messages)) {
                foreach ($messages as $message) {
                    $clone->addMessage($message);
                }
            }
        }

        return $clone;
    }

    /**
     * Calculates tax amounts for the row item using $this->_taxCalculator
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return $this
     */
    protected function _calcTaxAmounts(&$item)
    {
        //@nelkaake -a 16/11/10: Calculator only works in magento 1.4 and up.
        if (! Mage::helper ( 'rewards/version' )->isMageVersionAtLeast ( '1.4.2.0' )) {
            return $this;
        }

        list($rowTax, $baseRowTax) = $this->_calcItemTax($item);

        // if this is a bundle product, our tax calculation needs to include its children
        if ($item->getHasChildren() && $item->isChildrenCalculated()) {
            $accumulatedTax     = 0.0;
            $accumulatedBaseTax = 0.0;
            foreach ($item->getChildren() as $child) {
                list($childTax, $childBaseTax) = $this->_calcItemTax($child);

                $accumulatedTax     += $childTax;
                $accumulatedBaseTax += $childBaseTax;
            }

            $rowTax     += $accumulatedTax;
            $baseRowTax += $accumulatedBaseTax;
        }

        $item->setTaxAmount(max(0, $rowTax));
        $item->setBaseTaxAmount(max(0, $baseRowTax));

        // this messes up order row item tax, not sure if it's still needed
        // $item->setTaxableAmount($item->getRowTotal());
        // $item->setBaseTaxableAmount($item->getBaseRowTotal());

        return $this;
    }

    /**
     * Calculates row item tax taking into account Tax settings: Apply Customer Tax, Apply Discount On Prices.
     * @param  Mage_Sales_Model_Quote_Item  $item
     * @return array Item tax & base tax amounts
     */
    protected function _calcItemTax($item)
    {
        $store          = $item->getStoreId();
        $inclTax        = $item->getIsPriceInclTax();
        $subtotal       = $item->getTaxableAmount() + $item->getExtraRowTaxableAmount();
        $baseSubtotal   = $item->getBaseTaxableAmount() + $item->getBaseExtraRowTaxableAmount();
        $rate           = $item->getTaxPercent();

        switch ($this->_taxHelper->getCalculationSequence($store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                // nothing to do here
                break;
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                $discountAmount     = $item->getDiscountAmount();
                $baseDiscountAmount = $item->getBaseDiscountAmount();
                $subtotal           = max($subtotal - $discountAmount, 0);
                $baseSubtotal       = max($baseSubtotal - $baseDiscountAmount, 0);
        }

        $itemTax     = $this->_taxCalculator->calcTaxAmount($subtotal, $rate, $inclTax);
        $baseItemTax = $this->_taxCalculator->calcTaxAmount($baseSubtotal, $rate, $inclTax);

        $item->setTaxAmount(max(0, $itemTax));
        $item->setBaseTaxAmount(max(0, $baseItemTax));

        return array($itemTax, $baseItemTax);
    }

    /**
     * Returns the item's updated row total after redemptions
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return float
     */
    public function getRowTotalAfterRedemptions($item) {
        $new_red_data = $this->getUpdatedRedemptionData ( $item );
        $row_total = $new_red_data ['row_total'];
        return $row_total;
    }
    /**
     * Returns the item's updated row total after redemptions
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return float
     */
    public function getRowTotalAfterRedemptionsInclTax($item) {
        $new_red_data = $this->getUpdatedRedemptionData ( $item, true );
        $row_total = $new_red_data ['row_total_incl_tax'];
        return $row_total;
    }

    /**
     * Returns the item's updated redemption data as a hash
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return string a hash of the new item redemptions
     */
    public function getUpdatedRedemptionsHash($item) {
        $new_red_data = $this->getUpdatedRedemptions ( $item );
        $redemptions_data = Mage::helper ( 'rewards' )->hashIt ( $new_red_data );
        return $redemptions_data;
    }

    /**
     * Returns the item's updated redemption data
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array a map of the new item redemptions
     */
    public function getUpdatedRedemptions($item) {
        $new_red_data = $this->getUpdatedRedemptionData ( $item );
        $redemptions_data = $new_red_data ['redemptions_data'];
        return $redemptions_data;
    }

    /**
     * Renders the item's redemption rules and final row total and returns it.
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array a map of the new item redemption data:
     * array('redemptions_data'=>{...}, 'row_total'=>float)
     */
    protected function getUpdatedRedemptionData($item, $do_incl_tax = true) {
        // Step 1: Create a map of usability for all applied redemptions
        //echo "$item->getRedeemedPointsHash()";
        $redeemed_points = Mage::helper ( 'rewards' )->unhashIt ( $item->getRedeemedPointsHash () );

        // Prepare data from item and initalize counters
        if ($item->getQuote ())
            $store_currency = round ( $item->getQuote ()->getStoreToQuoteRate (), 4 );
        if ($item->getOrder ())
            $store_currency = round ( $item->getOrder ()->getStoreToQuoteRate (), 4 );

        if ($item->hasCustomPrice ()) {
            $product_price = ( float ) $item->getCustomPrice () * $store_currency;
        } else {
            //@nelkaake -a 17/02/11: We need to use our own calculation because the one that was set by the
            // rest of the Magento system is rounded.
            if (Mage::helper ( 'tax' )->priceIncludesTax () && $item->getPriceInclTax ()) {
                $product_price = $item->getPriceInclTax () / (1 + $item->getTaxPercent () / 100);
            } else {
                $product_price = ( float ) $item->getPrice () * $store_currency;
            }

        }
        if ($item->getParentItem () || sizeof ( $redeemed_points ) == 0) {
            return array (
                'redemptions_data' => array (),
                'row_total_incl_tax' => $item->getRowTotalInclTax (),
                'row_total' => $item->getRowTotal ()
            );
        }

        $total_qty = $item->getQty ();
        $total_qty_redeemed = 0.0000;
        $row_total = 0.0000;
        $new_redeemed_points = array ();
        $ret = array ();

        // Loop through and apply all our rules.
        foreach ( $redeemed_points as $key => &$redemption_instance ) {
            $redemption_instance = ( array ) $redemption_instance;
            $applic_qty = $redemption_instance [self::POINTS_APPLICABLE_QTY];
            $rule_id = $redemption_instance [self::POINTS_RULE_ID];
            $effect = $redemption_instance [self::POINTS_EFFECT];
            $uses = isset ( $redemption_instance [self::POINTS_USES] ) ? ( int ) $redemption_instance [self::POINTS_USES] : 1;
            $rule = Mage::helper ( 'rewards/rule' )->getCatalogRule ( $rule_id );

            // If a rule was turned off at some point in the back-end it should be removed and not calculated in the cart anymore.
            if (! $rule->getIsActive ()) {
                $this->removeCatalogRedemptionsFromItem ( $item, array ($rule_id ) );
                $effect = "";
            }

            $total_qty_remain = $total_qty - $total_qty_redeemed;
            if ($total_qty_remain > 0) {
                if ($total_qty_remain < $applic_qty) {
                    $applic_qty = $total_qty_remain;
                    $redemption_instance [self::POINTS_APPLICABLE_QTY] = $applic_qty;
                }

                $price_after_redem = $this->getPriceAfterEffect ( $product_price, $effect, $item );

                $row_total += $applic_qty * ( float ) $price_after_redem;
                $total_qty_redeemed += $applic_qty;
                $new_redeemed_points [] = $redemption_instance;
            } else {
                $redemption_instance [self::POINTS_APPLICABLE_QTY] = 0;
                $redemption_instance [self::POINTS_USES] = 1; // used once by default
                unset ( $redeemed_points [$key] );
            }
        }

        $ret ['redemptions_data'] = $new_redeemed_points;

        // Add in the left over products that perhaps weren't affected by qty adjustment.
        $total_qty_remain = ($total_qty - $total_qty_redeemed);
        if ($total_qty_remain < 0) {
            $total_qty_remain = 0;
            $total_qty_redeemed = $total_qty;

        //throw new Exception("Redemption rules may be overlapping.  Please notify the store administrator of this error.");
        }
        $row_total += $total_qty_remain * ( float ) $product_price;

        $ret ['row_total'] = $row_total;
        $ret ['row_total_incl_tax'] = $row_total * (1 + $item->getTaxPercent () / 100);

        return $ret;
    }

    /**
     * Returns a product price after the given effect has occured.
     * @see also TBT_Rewards_Helper_Data::priceAdjuster
     *
     * @param decimal $product_price
     * @param mixed $effect
     * @param Mage_Sales_Model_Quote_Item $item
     * @param boolean  $calc_incl_tax_if_applic if applicable, should I calculate the price including tax amount?
     */
    public function getPriceAfterEffect($product_price, $effect, $item, $calc_incl_tax_if_applic = true) {
        //@nelkaake -a 17/02/11: If it's order mode we don't want to be pulling tax rates from anywhere.
        if (Mage::helper ( 'tax' )->priceIncludesTax () && $calc_incl_tax_if_applic) {
            $product_price = $product_price * (1 + $item->getTaxPercent () / 100);
        }

        $price_after_redem = Mage::helper ( 'rewards' )->priceAdjuster ( $product_price, $effect );

        if (Mage::helper ( 'tax' )->priceIncludesTax () && $calc_incl_tax_if_applic) {
            $price_after_redem = $price_after_redem / (1 + $item->getTaxPercent () / 100);
        }

        return $price_after_redem;
    }

    public function refactorGrandTotal($items) {
        $acc_diff = 0;

        if (! is_array ( $items )) {
            $items = array ($items );
        }

        foreach ( $items as $item ) {
            // Tracking the differences in applying Catalog rules
            $acc_diff += $item->getRowTotalBeforeRedemptions () - $item->getRowTotal ();
        }
    }

    /**
     * Calculates the resulting total discount amount due to Sweet Tooth catalog redemption
     * rule discounts.
     * @param Mage_Sales_Model_Quote_Item $item
     */
    public function getTotalCatalogDiscount($item) {

        $this->resetRowTotals( $item );

        if (Mage::helper ( 'tax' )->priceIncludesTax ()) {
            $row_total = $item->getRowTotalInclTax ();
            $row_total_after = $this->getRowTotalAfterRedemptionsInclTax ( $item );
        } else {
            $row_total = $item->getRowTotal ();
            $row_total_after = $this->getRowTotalAfterRedemptions ( $item );
        }
        $row_total_after = $row_total_after < 0 ? 0 : $row_total_after;

        $catalog_discount = $row_total_after - $row_total;
        return $catalog_discount;
    }

    /**
     * Make sure we're calculating the discount based on the original row_total, not one which we previously modified.
     * For now looks like we only need to do this for OneStepCheckout, so we'll address it there only.
     * @deprecated Too many modules to check for now, so we're just doing it by default in getTotalCatalogDiscount
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return $this;
     */
    protected function _resetTotalsIfOSC($item) {

        // if OneStepCheckout isn't installed or enabled ...
        if ( !Mage::getConfig()->getModuleConfig( 'Idev_OneStepCheckout' ) ||
                !Mage::getConfig()->getModuleConfig( 'Idev_OneStepCheckout' )->is( 'active', 'true' ) ) {

            // ... and GoMage Checkout isn't installed or enabled ...
            if ( !Mage::getConfig()->getModuleConfig( 'GoMage_Checkout' ) ||
                    !Mage::getConfig()->getModuleConfig( 'GoMage_Checkout' )->is( 'active', 'true' ) ||
                    !Mage::helper( 'gomage_checkout' )->getConfigData( 'general/enabled' ) ) {

                // ... and OneClickCartCheckout isn't installed or enabled ...
                if ( !Mage::getConfig()->getModuleConfig( 'GoldenSpiralStudio_OneClickCartCheckout' ) ||
                        !Mage::getConfig()->getModuleConfig( 'GoldenSpiralStudio_OneClickCartCheckout' )->is( 'active', 'true' ) ) {

                    // ... then we don't need to do this, so just return
                    return $this;
                }
            }
        }

        // if any of the above modules are installed & enabled, this is required
        $this->resetRowTotals( $item );

        return $this;
    }

    /**
     *
     * @param Mage_Sales_Model_Quote_Item $item
     */
    public function resetItemDiscounts($item) {
        if (! $item)
            return $this;

        if ($item->getRowTotalBeforeRedemptions () == 0) {
            $item->setRowTotalBeforeRedemptions ( $item->getRowTotal () );
            $item->setRowTotalBeforeRedemptionsInclTax ( $item->getRowTotalInclTax () );
        } elseif ($item->getRowTotalBeforeRedemptions () < $item->getRowTotal ()) {
            $item->setRowTotal ( $item->getRowTotalBeforeRedemptions () );
            $item->setRowTotalInclTax ( $item->getRowTotalBeforeRedemptionsInclTax () );
        }
                else {
            // do nothing
        }

        if (!Mage::helper ( 'rewards' )->isBaseMageVersionAtLeast ( '1.4.0.0' )) {
            // only happens in Magento 1.3
            $rowTotalInclTax = $item->getRowTotalBeforeRedemptions () * (1 + ($item->getTaxPercent () / 100));
            $item->setRowTotalBeforeRedemptionsInclTax ($rowTotalInclTax);
        }

        return $this;
    }

    /**
     * Resets the RowTotalAfterRedemptions value for the item.
     * @param Mage_Sales_Model_Quote_Item $item
     */
    public function resetBeforeDiscount($item) {
        if (! $item)
            return $this;

        $item->setRowTotalBeforeRedemptions ( null );
        $item->setRowTotalBeforeRedemptionsInclTax ( null );

        return $this;
    }

    /**
    * Resets the following item attributes by calling $item->calcRowTotal followed by _calcTaxAmounts():
    *   row_total,
    *   base_row_total,
    *   row_total_incl_tax,
    *   base_row_total_incl_tax,
    *   tax_amount,
    *   base_tax_amount,
    *   taxable_amount,
    *   base_taxable_amount
    * @param Mage_Sales_Model_Quote_Item $item
    */
    public function resetRowTotals($item){
        // no need to do the reset by calling $item->calcRowTotal if Magento prior to 1.4.2.0
        if (!Mage::helper ( 'rewards/version' )->isMageVersionAtLeast ( '1.4.2.0' )) {
            return $this;
        }

        // $item->calcRowTotal();
        $this->_calcTaxAmounts($item);

        return $this;
    }
}
