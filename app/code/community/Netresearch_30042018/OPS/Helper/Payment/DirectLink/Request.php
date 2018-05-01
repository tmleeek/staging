<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de> 
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */



abstract class Netresearch_OPS_Helper_Payment_DirectLink_Request
{

    protected $dataHelper = null;

    protected $quoteHelper = null;

    protected $orderHelper = null;

    protected $customerHelper = null;

    protected $validator = null;

    protected $requestHelper = null;

    protected $config = null;

    /**
     * @param null $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return null
     */
    public function getConfig()
    {
        if (null === $this->config) {
            $this->config = Mage::getModel('ops/config');
        }
        return $this->config;
    }

    public function setRequestHelper(Netresearch_OPS_Helper_Payment_Request $requestHelper)
    {
        $this->requestHelper = $requestHelper;
    }

    /**
     * @return Netresearch_OPS_Helper_Payment_Request
     */
    public function getRequestHelper()
    {
        if (null === $this->requestHelper) {
            $this->requestHelper = Mage::helper('ops/payment_request');
        }

        return $this->requestHelper;
    }

    /**
     * sets the data helper
     *
     * @param Netresearch_OPS_Helper_Data $dataHelper
     */
    public function setDataHelper(Netresearch_OPS_Helper_Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * gets the data helper
     *
     * @return Mage_Core_Helper_Abstract
     */
    public function getDataHelper()
    {
        if (null === $this->dataHelper) {
            $this->dataHelper = Mage::helper('ops/data');
        }

        return $this->dataHelper;
    }

    /**
     * sets the quote helper
     *
     * @param Netresearch_OPS_Helper_Quote $quoteHelper
     */
    public function setQuoteHelper(Netresearch_OPS_Helper_Quote $quoteHelper)
    {
        $this->quoteHelper = $quoteHelper;
    }

    /**
     * gets the quote helper
     *
     * @return Mage_Core_Helper_Abstract
     */
    public function getQuoteHelper()
    {
        if (null === $this->quoteHelper) {
            $this->quoteHelper = Mage::helper('ops/quote');
        }

        return $this->quoteHelper;
    }

    /**
     * sets the order helper
     *
     * @param Netresearch_OPS_Helper_Order $orderHelper
     */
    public function setOrderHelper(Netresearch_OPS_Helper_Order $orderHelper)
    {
        $this->orderHelper = $orderHelper;
    }

    /**
     * gets the order helper
     *
     * @return Mage_Core_Helper_Abstract
     */
    public function getOrderHelper()
    {
        if (null === $this->orderHelper) {
            $this->orderHelper = Mage::helper('ops/order');
        }

        return $this->orderHelper;
    }

    /**
     * sets the customer helper
     *
     * @param Mage_Core_Helper_Abstract $customerHelper
     */
    public function setCustomerHelper(Mage_Core_Helper_Abstract $customerHelper)
    {
        $this->customerHelper = $customerHelper;
    }

    /**
     * gets the customer helper
     *
     * @return Mage_Core_Helper_Abstract
     */
    public function getCustomerHelper()
    {
        if (null === $this->customerHelper) {
            $this->customerHelper = Mage::helper('customer/data');
        }

        return $this->customerHelper;
    }


    /**
     * extracts the aparameter for the direct link request from the quote, order and, optionally from existing request params
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param Mage_Sales_Model_Order $order
     * @param array $requestParams
     *
     * @return array - the parameters for the direct link request
     */
    public function getDirectLinkRequestParams(Mage_Sales_Model_Quote $quote, Mage_Sales_Model_Order $order, $requestParams = array())
    {
        $billingAddress  = $order->getBillingAddress();
        $shippingAddress = $this->getShippingAddress($order, $billingAddress);
        $requestParams = $this->getBaseRequestParams($quote, $order, $billingAddress);
        $requestParams = array_merge($requestParams, $this->getPaymentSpecificParams($quote));
        $shipToParams = $this->getRequestHelper()->extractShipToParameters($shippingAddress);
        $shipToParams = $this->decodeParamsForDirectLinkCall($shipToParams);
        $requestParams = array_merge($requestParams, $shipToParams);
        $requestParams = $this->addCustomerSpecificParams($requestParams);
        $requestParams = $this->addParamsForAdminPayments($requestParams);

        return $requestParams;
    }

    /**
     * specail handling like validation and so on for admin payments
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param                        $requestParams
     *
     * @return mixed
     */
    abstract public function handleAdminPayment(Mage_Sales_Model_Quote $quote, $requestParams);

    /**
     * extracts payment specific payment parameters
     *
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return array
     */
    abstract protected function getPaymentSpecificParams(Mage_Sales_Model_Quote $quote);

    /**
     * gets the shipping address if there is one, otherwise the billing address is used as shipping address
     *
     * @param $order
     * @param $billingAddress
     *
     * @return mixed
     */
    protected function getShippingAddress(Mage_Sales_Model_Order $order, $billingAddress)
    {
        $shippingAddress = $order->getShippingAddress();
        if (null === $shippingAddress || false === $shippingAddress) {
            $shippingAddress = $billingAddress;
        }
        return $shippingAddress;
    }

    /**
     * utf8 decode for direct link calls
     *
     * @param array $requestParams
     *
     * @return array - the decoded array
     */
    protected function decodeParamsForDirectLinkCall(array $requestParams)
    {
        foreach ($requestParams as $key => $value) {
            $requestParams[$key] = utf8_decode($value);
        }
        return $requestParams;
    }

    /**
     * @param $requestParams
     *
     * @return mixed
     */
    protected function addCustomerSpecificParams($requestParams)
    {
        if ($this->getCustomerHelper()->isLoggedIn()) {
            $requestParams['CUID'] = $this->getCustomerHelper()->getCustomer()->getId();
        }
        return $requestParams;
    }

    /**
     * @param $requestParams
     *
     * @return mixed
     */
    protected function addParamsForAdminPayments($requestParams)
    {
        if ($this->getDataHelper()->isAdminSession()) {
            $requestParams['ECI'] = Netresearch_OPS_Model_Eci_Values::MANUALLY_KEYED_FROM_MOTO;
        }
        return $requestParams;
    }

    /**
     * @param $quote
     * @param $order
     * @param $billingAddress
     *
     * @return array
     */
    protected function getBaseRequestParams($quote, $order, $billingAddress)
    {
        $requestParams = array(
            'AMOUNT'                        => $this->getDataHelper()->getAmount($quote->getBaseGrandTotal()),
            'CURRENCY'                      => $this->getQuoteHelper()->getQuoteCurrency($quote),
            'OPERATION'                     => $this->getQuoteHelper()->getPaymentAction($quote),
            'ORDERID'                       => $this->getConfig()->getConfigData('devprefix') . $quote->getId(),
            'OWNERADDRESS'                  => utf8_decode($billingAddress->getStreet(-1)),
            'OWNERTOWN'                     => utf8_decode($billingAddress->getCity()),
            'OWNERZIP'                      => $billingAddress->getPostcode(),
            'OWNERTELNO'                    => $billingAddress->getTelephone(),
            'OWNERCTY'                      => $billingAddress->getCountry(),
            'ADDMATCH'                      => $this->getOrderHelper()->checkIfAddressesAreSame($order),
            'ECOM_BILLTO_POSTAL_POSTALCODE' => $billingAddress->getPostcode(),
            'ORIG'                          => $this->getDataHelper()->getModuleVersionString(),
            'EMAIL'                         => $order->getCustomerEmail(),
        );

        return $requestParams;
    }
} 