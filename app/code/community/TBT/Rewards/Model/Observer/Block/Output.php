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
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Model_Observer_Block_Output extends Varien_Object
{

    /**
     * Executed from the core_block_abstract_to_html_after event
     * @param Varien_Event $obj
     */
    public function afterOutput($obj)
    {
        $block = $obj->getEvent ()->getBlock ();
        $transport = $obj->getEvent ()->getTransport ();

        // Magento 1.3 and lower dont have this transport, so we can't do autointegration : (
        if(empty($transport)) {
            return $this;
        }

        if (Mage::getStoreConfigFlag('advanced/modules_disable_output/TBT_Rewards')) {
            return $this;
        }

        $this->appendBirthdayPredictPoints ( $block, $transport );
        $this->appendRewardsHeader ( $block, $transport );
        $this->appendCartPointsSpender ( $block, $transport );
        $this->appendPointsSummary ( $block, $transport );
        $this->appendToCatalogListing ( $block, $transport );
        $this->overwriteCheckoutButtons ( $block, $transport );
        $this->appendPointsAdjustmentToCreditmemo($block, $transport);
        $this->appendPointsBalanceToAdminOrderInfo($block, $transport);
        $this->overwriteOrderCancelPopup($block, $transport);
        $this->appendProductViewPoints($block, $transport);
        $this->appendRedemptionInBackend($block, $transport);

        if ($block instanceof Mage_Adminhtml_Block_Dashboard_Sales) {
            $html = $transport->getHtml ();
            $rewardsDashboardHtml = $block->getParentBlock()->getChildHtml('rewards_dashboard');
            if (!empty($rewardsDashboardHtml))
                $html .= $rewardsDashboardHtml;
                $transport->setHtml($html);
        }

        return $this;
    }

    /**
     * This function will append points spending/earning block html to bundle product customize_button child block for
     * Magento Enterprise, because here Magento uses a sliding view to 'Customize and Buy' bundle products, so we
     * display blocks on customize bundle product view.
     *
     */
    public function appendProductViewPoints($block, $transport)
    {
        $versionHelper = Mage::helper("rewards/version");

        if ( !Mage::registry('current_product')
            || Mage::registry('current_product')->getTypeId() != "bundle"
            || !$block instanceof Mage_Catalog_Block_Product_View
            || $block->getBlockAlias() != "product.info.addtocart"
            || !$versionHelper->isMageEnterprise()
        ) {
            return $this;
        }

        $html = $transport->getHtml();
        $earnBlock = $block->getLayout()
                           ->createBlock('rewards/product_view_points_earned', "rewards.product.view.points.earned")
                           ->setTemplate("rewards/product/view/points_earned.phtml");

        $pointBlock = $block->getLayout()
                            ->createBlock('rewards/integrated_product_view_points', "rewards.integrated.product.view.points")
                            ->setTemplate("rewards/product/view/points.phtml");

        $pointsRedeemedBlock = $block->getLayout()
            ->createBlock('rewards/product_view_points_redeemed', "rewards.product.view.points.redeemed")
            ->setTemplate("rewards/product/view/points_redeemed.phtml");

        $pointsSliderBlock = $block->getLayout()
            ->createBlock('rewards/points_slider', "rewards.product.view.points.slider")
            ->setTemplate("rewards/product/view/slider.phtml");

        $pointsSliderJsBlock = $block->getLayout()
            ->createBlock('rewards/points_slider', "rewards.product.view.points.slider.js")
            ->setTemplate("rewards/product/view/slider_js.phtml");

        $pointsRedeemedBlock->setChild("points_slider", $pointsSliderBlock);
        $pointsRedeemedBlock->setChild("points_slider_js", $pointsSliderJsBlock);

        $pointBlock->setChild("points_earned", $earnBlock);
        $pointBlock->setChild("points_redeemed", $pointsRedeemedBlock);

        $pointHtml = $pointBlock->toHtml();
        $html = $pointHtml . $html;
        $transport->setHtml($html);

        return $this;
    }

    public function appendBirthdayPredictPoints($block, $transport)
    {

        if (!Mage::getStoreConfigFlag('rewards/autointegration/predict_birthday_points')) {
            return $this;
        }

        // Check if Block is Dob Block
        if (!( $block instanceof Mage_Customer_Block_Widget_Dob )) {
            return $this;
        }
        if (Mage::getSingleton('rewards/session')->isCustomerLoggedIn()) {
            return $this;
        }

        $html = $transport->getHtml ();
        $st_html = $block->getLayout()->createBlock('rewards/special_birthday')->toHtml();

        // Check that content is not already integrated.
        if ( $st_html != "" && strpos($html, $st_html) === false ) {
            $html .= $st_html;
        }

        $transport->setHtml($html);

        return $this;

    }

    /**
     * Appends the points balance in the header somewhere
     * @param unknown_type $block
     * @param unknown_type $transport
     */
    public function appendRewardsHeader($block, $transport)
    {

        if (!Mage::getStoreConfigFlag('rewards/autointegration/header_points_balance')) {
            return $this;
        }

        if ($block->getBlockAlias () == 'topLinks') {
            $html = $transport->getHtml ();
            $st_html = $block->getChildHtml ( 'rewards_points_balance' );
            $st_html .= $block->getChildHtml ( 'cart_points_js' );

            // Check that content is not already integrated.
            if($st_html != "" && strpos($html, $st_html) === false) {
                $html = $st_html . $html;
            }

            $transport->setHtml ( $html );
        }

        return $this;
    }

    /**
     * Append the shopping cart points spender box in the shopping box
     * @param unknown_type $block
     * @param unknown_type $transport
     */
    public function appendCartPointsSpender($block, $transport)
    {

        if (!Mage::getStoreConfigFlag('rewards/autointegration/shopping_cart_under_coupon')) {
            return "";
        }

        if ($block->getBlockAlias () == 'coupon' && $block->getChild ( 'rewards_cartpoints_spender' )) {
            $html = $transport->getHtml ();
            $st_html = $block->getChildHtml ( 'rewards_cartpoints_spender_js' );
            $st_html = $block->getChildHtml ( 'rewards_cartpoints_spender' );

            // Check that content is not already integrated.
            if(strpos($html, $st_html) === false) {
                $html = $st_html . $html;
            }

            $transport->setHtml ( $html );
        }

        return $this;
    }

    /**
     * Append the points summary message in the dashboard.
     * @param unknown_type $block
     * @param unknown_type $transport
     */
    public function appendPointsSummary($block, $transport)
    {
        if (!Mage::getStoreConfigFlag('rewards/autointegration/customer_dashboard_summary')) {
            return $this;
        }

        if ($block->getBlockAlias () == 'top' && $block->getChild ( 'rewards_points_summary' )) {
            $html = $transport->getHtml ();
            $st_html = $block->getChildHtml ( 'rewards_points_summary' );

            // Check that content is not already integrated.
            if (strpos($html, $st_html) === false) {
                $html = $st_html . $html;
            }

            $transport->setHtml ( $html );
        }

        return $this;
    }

    /**
     *
     * Appends the points balance in the header somewhere
     * @param Mage_Catalog_Block_Product_List $block
     * @param Varien_Object $transport
     */
    public function appendToCatalogListing($block, $transport) {

        // Should we be checking this auto-integration?
        if (!Mage::getStoreConfigFlag('rewards/autointegration/product_listing')) {
            return $this;
        }

        // Block is a price block.
        if (!($block instanceof Mage_Catalog_Block_Product_List)) {
            return $this;
        }


        $all_products = $block->getLoadedProductCollection();

        $html = $transport->getHtml();
        $html = $this->_getNewCatalogListingHtml($html, $all_products, $block);

        $transport->setHtml($html);

        return $this;

    }

    /**
     * Overwrites the various checkout buttons if the customer shouldn't be able to checkout (based on
     * redemptions) with an appropriate message telling the customer what to do.
     * @param Mage_Core_Block_Template $block
     * @param Varien_Object $transport
     */
    public function overwriteCheckoutButtons($block, $transport)
    {
        if (!($block instanceof Mage_Checkout_Block_Onepage_Link) &&
                !($block instanceof Mage_Checkout_Block_Multishipping_Link) &&
                !($block instanceof Mage_Paypal_Block_Express_Shortcut)) {

            return $this;
        }

        if ($this->_getRewardsSession()->canCheckoutWithCurrentRedemptions()) {
            return $this;
        }

        if (!$this->_disableCheckoutsIfNotEnoughPoints()) {
            return $this;
        }

        switch(get_class($block)) {
            case 'Mage_Checkout_Block_Onepage_Link':
                $this->_overwriteMainCheckoutButton($block, $transport);
                break;
            case 'Mage_Checkout_Block_Multishipping_Link':
                $this->_removeMultishippingLink($block, $transport);
                break;
            case 'Mage_Paypal_Block_Express_Shortcut':
                $this->_removePaypalExpressButton($block, $transport);
                break;
        }

        return $this;
    }

    /**
     * Appends some fields to the creditmemo form, to adjust points earned and spent on the order.
     * @param Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals $block
     * @param Varien_Object $transport
     * @return TBT_Rewards_Model_Observer_Block_Output
     */
    public function appendPointsAdjustmentToCreditmemo($block, $transport)
    {
        if (!($block instanceof Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals)) {
            return $this;
        }

        $html = $transport->getHtml();

        $stBlock = $block->getLayout()->createBlock('rewards/adminhtml_sales_order_creditmemo_points');
        $stBlock->setOrder($block->getOrder());
        $stHtml = $stBlock->toHtml();

        $html .= "<div class='divider'></div>";
        $html .= $stHtml;

        $transport->setHtml($html);

        return $this;
    }

    /**
     * Appends the customer's current points balance to the customer Account Information section
     * of the order view page, creditmemo page, etc.
     * @param Mage_Adminhtml_Block_Sales_Order_View_Info $block
     * @param Varien_Object $transport
     * @return TBT_Rewards_Model_Observer_Block_Output
     */
    public function appendPointsBalanceToAdminOrderInfo($block, $transport)
    {
        if (!($block instanceof Mage_Adminhtml_Block_Sales_Order_View_Info)) {
            return $this;
        }

        $html = $transport->getHtml();

        $currencyIds = Mage::getSingleton('rewards/currency')->getAvailCurrencyIds();
        $currencyId = $currencyIds[0];
        $customerId = $block->getOrder()->getCustomerId();
        $customer = Mage::getModel('rewards/customer')->load($customerId);
        $pointsBalance = $customer->getUsablePointsBalance($currencyId);

        $label = Mage::helper('rewards')->__("Customer Points Balance");
        $pointsString = Mage::getModel('rewards/points')->set($currencyId, $pointsBalance);

        $insert = "<td class=\"label rewards-balance-label\"><label>{$label}</label></td>
                <td class=\"value rewards-balance-value\"><strong>{$pointsString}</strong></td>$1";
        $needle = $this->_getEndOfAccountInformationHtml();
        $html = preg_replace($needle, $insert, $html, 1);

        $transport->setHtml($html);

        return $this;
    }

    public function overwriteOrderCancelPopup($block, $transport)
    {
        if (!($block instanceof Mage_Adminhtml_Block_Sales_Order_View)) {
            return $this;
        }

        $html = $transport->getHtml();

        $popup = $block->getLayout()->createBlock('rewards/adminhtml_sales_order_cancel_popup');
        $popup->setOrder($block->getOrder());
        $html .= $popup->toHtml();

        $transport->setHtml($html);

        return $this;
    }

    /**
     * @param Mage_Core_Block_Abstract $block
     * @return self
     */
    public function appendRedemptionInBackend($block, $transport)
    {
        if (!($block instanceof Mage_Adminhtml_Block_Sales_Order_Create_Coupons)) {
            return $this;
        }

        // This was causing a nesting error if it was forced to happen deep inside the block tree, so get it out of the way now.
        $this->_getRewardsSession()->refreshSessionCustomer();

        $html = $transport->getHtml();
       // $html .= $block->getChild('rewards_redemption')->toHtml();
        $transport->setHtml($html);

        return $this;
    }

    /**
     * Overwrites the cart's checkout button with a "not enough points" message if the customer
     * doesn't have enough points to checkout with their specified redemptions, or a "you must login to
     * spend points" message if the customer is trying to spend points as a guest.
     * @param Mage_Checkout_Block_Onepage_Link $mageBlock
     * @param Varien_Object $transport
     */
    protected function _overwriteMainCheckoutButton($mageBlock, $transport)
    {
        if (!($mageBlock instanceof Mage_Checkout_Block_Onepage_Link)) {
            return $this;
        }

        if ($mageBlock instanceof TBT_Rewards_Block_Checkout_Onepage_Link) {
            return $this;
        }

        $rewardsBlock = $mageBlock->getLayout()->createBlock('rewards/checkout_onepage_link');
        $transport->setHtml($rewardsBlock->toHtml());

        return $this;
    }

    /**
     * Removes the cart's multishipping checkout link (assumes the checkout button is being overwritten)
     * @param Mage_Checkout_Block_Multishipping_Link $block
     * @param Varien_Object $transport
     */
    protected function _removeMultishippingLink($block, $transport)
    {
        if (!($block instanceof Mage_Checkout_Block_Multishipping_Link)) {
            return $this;
        }

        $transport->setHtml('');

        return $this;
    }

    /**
     * Removes the cart's PayPal Express Checkout button (assumes the regular checkout button is being overwritten)
     * @param Mage_Paypal_Block_Express_Shortcut $block
     * @param Varien_Object $transport
     */
    protected function _removePaypalExpressButton($block, $transport)
    {
        if (!($block instanceof Mage_Paypal_Block_Express_Shortcut)) {
            return $this;
        }

        $transport->setHtml('');

        return $this;
    }

    /**
     *
     * @param string $html
     * @param Mage_Eav_Model_Entity_Collection_Abstract $all_products
     * @param Mage_Catalog_Block_Product_List $block
     */
    protected function _getNewCatalogListingHtml($html, $all_products, $block) {

        $is_list_mode_display = strpos($html, 'class="products-list" id="products-list">') !== false;

        foreach($all_products as $_product) {
            $product_id = $_product->getId();

            $predict_points_block = $block->getLayout()->createBlock('rewards/product_predictpoints', 'rewards_catalog_product_list_predictpoints');
            $block->insert($predict_points_block);
            $predict_points_block->setProduct($_product);
            $st_html = $predict_points_block->toHtml();
            $isRwdTheme = Mage::helper('rewards/theme')->getPackageName() === "rwd";

            //  If no content, dont integrate
            if(empty($st_html)) {
                continue;
            }

            // Check that content is not already integrated.
            if(strpos($html, $st_html) !== false) {
                continue;
            }


            $replaced_html = null;
            if(Mage::helper('rewards/version')->isMageEnterprise() && !$isRwdTheme){
                $pattern = '/(<button )[^>]*(product\/'.  $product_id  .'\/)(.*)(<\/button>)/isU';
            } else {
                if($is_list_mode_display) {
                    $pattern = '/(<ul class="add-to-links">)((\s)*)(<li>)((\s)*)(<a href=")[^>]*(product\/'.  $product_id  .'\/)(.*)(<\/a>)(.*)(<\/li>)((\s)*)(<\/ul>)/isU';
                } else {
                    $pattern = '/(<div class="actions">)((\s)*)(<button )[^>]*(product\/'.  $product_id  .'\/)(.*)(<\/button>)(.*)(<\/div>)/isU';
                }
            }

            $st_html = preg_replace('#(\\$|\\\\)#', '\\\\$1', $st_html);
            $replacement = $st_html.'${0}';
            $replaced_html = preg_replace($pattern, $replacement, $html);

            //Nothing got replaced , some times if the product id's doesnt include in the url's the url key does , so
            if ( $replaced_html == $html ) {
                $productUrl = preg_quote($_product->getProductUrl()."?"); // Check "?" in product url
                $productUrl = str_replace("/", "\/", $productUrl);
                $pattern = '/(<div class="actions">)((\s)*)(<button )[^>]*('.$productUrl.')(.*)(<\/button>)(.*)(<\/div>)/isU';

                if ( Mage::helper('rewards/version')->isMageEnterprise() && !$isRwdTheme) {
                    $pattern = '/(<button )[^>]*('.  $productUrl  .')(.*)(<\/button>)/isU';
                }

                $replaced_html = preg_replace($pattern, $replacement, $html);
            }

            if (!empty($replaced_html)) {
                $html = $replaced_html;
            }
        }

        return $html;
    }

    /**
     * Returns the HTML used to find the end of the customer Account Information section
     * of the order view page, creditmemo page, etc.  Used to append the customer balance.
     * TODO: this might be easier solved if we add a customer EAV attribute for points balance
     */
    protected function _getEndOfAccountInformationHtml()
    {
        return '/(<\/table>[\s]*<\/div>[\s]*<\/div>[\s]*<\/div>[\s]*<\/div>[\s]*<div class="clear"><\/div>[\s]*[\s]*<div class="box-left">[\s]*<!--Billing Address-->)/';
    }

    /**
     * @deprecated Misspelled method name
     * @see _disableCheckoutsIfNotEnoughPoints
     */
    protected function _disableCheckoutsIfNotEnoughtPoints() {
        return Mage::helper('rewards/config')->disableCheckoutsIfNotEnoughPoints();
    }

    protected function _disableCheckoutsIfNotEnoughPoints()
    {
        return Mage::helper('rewards/config')->disableCheckoutsIfNotEnoughPoints();
    }

    /**
     * @return TBT_Rewards_Model_Session
     */
    protected function _getRewardsSession()
    {
        return Mage::getSingleton('rewards/session');
    }
}
