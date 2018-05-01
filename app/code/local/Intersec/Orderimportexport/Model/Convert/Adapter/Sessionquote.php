<?php
/**
 * Sessionquote.php
 * CommerceExtensions @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commerceextensions.com/LICENSE-M1.txt
 *

 * @category   Orders
 * @package    Sessionquote
 * @copyright  Copyright (c) 2003-2009 CommerceExtensions @ InterSEC Solutions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */ 

class Intersec_Orderimportexport_Model_Convert_Adapter_Sessionquote extends Mage_Core_Model_Session_Abstract
{
    const XML_PATH_DEFAULT_CREATEACCOUNT_GROUP = 'customer/create_account/default_group';

    /**
     * Quote model object
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote   = null;

    /**
     * Customer mofrl object
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer= null;

    /**
     * Store model object
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store   = null;

    /**
     * Order model object
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order   = null;

    public function __construct()
    {
        $this->init('adminhtml_quote');
        if (Mage::app()->isSingleStoreMode()) {
            $this->setStoreId(Mage::app()->getStore(true)->getId());
        }
    }

    /**
     * Retrieve quote model object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (is_null($this->_quote)) {
            $this->_quote = Mage::getModel('sales/quote');
            if ($this->getStoreId() && $this->getQuoteId()) {
                $this->_quote->setStoreId($this->getStoreId())
                    ->load($this->getQuoteId());
            }
            elseif($this->getStoreId() && $this->hasCustomerId()) {
                $this->_quote->setStoreId($this->getStoreId())
                    ->setCustomerGroupId(Mage::getStoreConfig(self::XML_PATH_DEFAULT_CREATEACCOUNT_GROUP))
                    ->assignCustomer($this->getCustomer())
                    ->setIsActive(false)
                    ->save();
                $this->setQuoteId($this->_quote->getId());
            }
            $this->_quote->setIgnoreOldQty(true);
            $this->_quote->setIsSuperMode(true);
        }
        return $this->_quote;
    }

    /**
     * Set customer model object
     * To enable quick switch of preconfigured customer
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        return $this;
    }

    /**
     * Retrieve customer model object
     * @param bool $forceReload
     * @param bool $useSetStore
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer($forceReload=false, $useSetStore=false)
    {
        if (is_null($this->_customer) || $forceReload) {
            $this->_customer = Mage::getModel('customer/customer');
            if ($useSetStore && $this->getStore()->getId()) {
                $this->_customer->setStore($this->getStore());
            }
            if ($customerId = $this->getCustomerId()) {
                $this->_customer->load($customerId);
            }
        }
        return $this->_customer;
    }

    /**
     * Retrieve store model object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = Mage::app()->getStore($this->getStoreId());
            if ($currencyId = $this->getCurrencyId()) {
                $this->_store->setCurrentCurrencyCode($currencyId);
            }
        }
        return $this->_store;
    }

    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (is_null($this->_order)) {
            $this->_order = Mage::getModel('sales/order');
            if ($this->getOrderId()) {
                $this->_order->load($this->getOrderId());
            }
        }
        return $this->_order;
    }
}