<?php
/**
 * MagestyApps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to https://www.magestyapps.com for more information or
 * send an email to office@magestyapps.com .
 *
 * @category    MagestyApps
 * @package     MagestyApps_AdvancedBreadcrumbs
 * @copyright   Copyright (c) 2016 MagestyApps (https://www.magestyapps.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class MagestyApps_AdvancedBreadcrumbs_Helper_Additional extends Mage_Core_Helper_Abstract
{
    protected $_availableCrumbs = array();

    const XML_PATH_ADDITIONAL_PAGES = 'crumbs/additional_pages/';

    const ADDITIONAL_PAGE_CUSTOMER_ACCOUNT = 'customer_account';
    const ADDITIONAL_PAGE_CHECKOUT = 'checkout';
    const ADDITIONAL_PAGE_CONTACTS = 'contacts';

    public function isEnabled($pageType)
    {
        return Mage::helper('crumbs')->isEnabled() && Mage::getStoreConfigFlag(self::XML_PATH_ADDITIONAL_PAGES . $pageType);
    }

    /**
     * Initialize available breadcrumbs for additional pages
     *
     * @return $this
     */
    protected function _initCrumbs()
    {
        $crumbs = array();

        if ($this->isEnabled(self::ADDITIONAL_PAGE_CUSTOMER_ACCOUNT)) {
            $customerAccountCrumb = array(
                'code' => 'customer_account',
                'title' => Mage::helper('crumbs')->__('My Account'),
                'url' => Mage::helper('customer')->getAccountUrl()
            );

            $newCrumbs = array(
                'customer_form_login' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'account_login',
                        'title' => Mage::helper('crumbs')->__('Customer Login')
                    ),
                ),
                'customer_account_dashboard' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'account_dashboard',
                        'title' => Mage::helper('crumbs')->__('Dashboard')
                    ),
                ),
                'customer_edit' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'account_edit',
                        'title' => Mage::helper('crumbs')->__('Account Information'),
                    ),
                ),
                'address_book' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'address_book',
                        'title' => Mage::helper('crumbs')->__('Address Book'),
                    ),
                ),
                'customer_address_edit' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'address_book',
                        'title' => Mage::helper('crumbs')->__('Address Book'),
                        'url' => Mage::getUrl('customer/address'),
                    ),
                    array(
                        'code' => 'edit_address',
                        'title' => Mage::helper('crumbs')->__('Edit Address'),
                    ),
                ),
                'sales.order.history' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'customer_orders',
                        'title' => Mage::helper('crumbs')->__('My Orders'),
                    ),
                ),
                'sales.order.info' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'customer_orders',
                        'title' => Mage::helper('crumbs')->__('My Orders'),
                        'url' => Mage::getUrl('sales/order/history'),
                    ),
                    array(
                        'code' => 'order_view',
                        'title' => Mage::helper('crumbs')->__('Order #%s', $this->getCurrentOrderId()),
                    ),
                ),
                'review_customer_list' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'product_reviews',
                        'title' => Mage::helper('crumbs')->__('My Product Reviews'),
                    ),
                ),
                'customers_review' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'product_reviews',
                        'title' => Mage::helper('crumbs')->__('My Product Reviews'),
                        'url' => Mage::getUrl('review/customer'),
                    ),
                    array(
                        'code' => 'review_details',
                        'title' => Mage::helper('crumbs')->__('Review Details'),
                    ),
                ),
                'downloadable_customer_products_list' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'downloadable_products',
                        'title' => Mage::helper('crumbs')->__('My Downloadable Products'),
                    ),
                ),
                'customer.account.billing.agreement' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'billing_agreements',
                        'title' => Mage::helper('crumbs')->__('Billing Agreements'),
                    ),
                ),
                'sales.recurring.profiles' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'recurring_profiles',
                        'title' => Mage::helper('crumbs')->__('Recurring Profiles'),
                    ),
                ),
                'customer.wishlist' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'wishlist',
                        'title' => Mage::helper('crumbs')->__('Wishlist'),
                    ),
                ),
                'wishlist.sharing' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'wishlist',
                        'title' => Mage::helper('crumbs')->__('Wishlist'),
                        'url' => Mage::getUrl('wishlist'),
                    ),
                    array(
                        'code' => 'share_whishlist',
                        'title' => Mage::helper('crumbs')->__('Share Wishlist'),
                    ),
                ),
                'oauth_customer_token_list' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'applications',
                        'title' => Mage::helper('crumbs')->__('Applications'),
                    ),
                ),
                'customer_newsletter' => array(
                    $customerAccountCrumb,
                    array(
                        'code' => 'customer_newsletter',
                        'title' => Mage::helper('crumbs')->__('Newsletter Subscriptions'),
                    ),
                )
            );

            $crumbs = array_merge($crumbs, $newCrumbs);
        }

        if ($this->isEnabled(self::ADDITIONAL_PAGE_CHECKOUT)) {

            $newCrumbs = array(
                'checkout.cart' => array(
                    array(
                        'code'  => 'checkout_cart',
                        'title' => Mage::helper('crumbs')->__('Shopping Cart'),
                    )
                ),
                'checkout.onepage' => array(
                    array(
                        'code'  => 'checkout_cart',
                        'title' => Mage::helper('crumbs')->__('Shopping Cart'),
                        'url'   => Mage::getUrl('checkout/cart')
                    ),
                    array(
                        'code'  => 'checkout_onepage',
                        'title' => Mage::helper('crumbs')->__('Checkout'),
                    )
                )
            );

            $crumbs = array_merge($crumbs, $newCrumbs);
        }

        if ($this->isEnabled(self::ADDITIONAL_PAGE_CONTACTS)) {
            $crumbs[ 'contactForm'] = array(
                array(
                    'code'  => 'contacts',
                    'title' => Mage::helper('crumbs')->__('Contact Us'),
                )
            );
        }

        $this->_availableCrumbs = $crumbs;

        return $this;
    }

    /**
     * Get crumbs based on specific block in layout
     *
     * @param string $nameInLayout
     * @return bool
     */
    public function getCrumbs($nameInLayout)
    {
        if (!count($this->_availableCrumbs)) {
            $this->_initCrumbs();
        }

        return isset($this->_availableCrumbs[$nameInLayout]) ? $this->_availableCrumbs[$nameInLayout] : false;
    }

    public function getCurrentOrderId()
    {
        $order = Mage::registry('current_order');
        if (!$order || !$order->getIncrementId()) {
            return false;
        }

        return $order->getIncrementId();
    }
}