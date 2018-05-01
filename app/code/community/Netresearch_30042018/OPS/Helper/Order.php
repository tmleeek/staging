<?php
/**
 * Netresearch_OPS_Helper_Order
 *
 * @package
 * @copyright 2013 Netresearch
 * @author    Michael Lühr <michael.luehr@netresearch.de>
 * @license   OSL 3.0
 */
class Netresearch_OPS_Helper_Order extends Mage_Core_Helper_Abstract
{

    /** @var $config Netresearch_OPS_Model_Config */
    private $config = null;

    const DELIMITER = '#';


    /**
     * return the config model
     *
     * @return Netresearch_OPS_Model_Config
     */
    protected function getConfig()
    {
        if (is_null($this->config)) {
            $this->config = Mage::getModel('ops/config');
        }
        return $this->config;
    }

    /**
     * generates the OPS order id in dependency to the config
     *
     * @param Mage_Sales_Order $order
     * @param $useOrderIdIfPossible if false forces the usage of quoteid (for Kwixo pm etc.)
     * @return string
     */
    public function getOpsOrderId($order, $useOrderIdIfPossible = true)
    {
        $config = $this->getConfig();
        $devPrefix = $config->getConfigData('devprefix');
        $orderRef = $order->getQuoteId();
        if ($config->getOrderReference($order->getStoreId())
            == Netresearch_OPS_Model_Payment_Abstract::REFERENCE_ORDER_ID
        && $useOrderIdIfPossible === true) {
            $orderRef = self::DELIMITER . $order->getIncrementId();
        }
        return $devPrefix . $orderRef;
    }

    /**
     * getting the order from opsOrderId which can either the quote id or the order increment id
     * in both cases the dev prefix is stripped, if neccessary
     *
     * @param $opsOrderId
     * @return Mage_Sales_Model_Order
     */
    public function getOrder($opsOrderId)
    {
        $order = null;
        $fieldToFilter = 'quote_id';
        $devPrefix = $this->getConfig()->getConfigData('devprefix');
        if ($devPrefix == substr($opsOrderId, 0, strlen($devPrefix))) {
            $opsOrderId = substr($opsOrderId, strlen($devPrefix));
        }
        // opsOrderId was created from order increment id, use increment id for filtering
        if (0 === strpos($opsOrderId, self::DELIMITER)) {
            $opsOrderId = substr($opsOrderId, strlen(self::DELIMITER));
            $fieldToFilter = 'increment_id';
        }
        $order = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter($fieldToFilter, $opsOrderId)
            ->getFirstItem();
        return $order;
    }

    /**
     * load and return the quote via the quoteId
     *
     * @param string $quoteId
     * @return Mage_Model_Sales_Quote
     */
    public function getQuote($quoteId)
    {
        return Mage::getModel('sales/quote')->load($quoteId);
    }

    /**
     * check if billing is same as shipping address
     *
     * @param Mage_Model_Sales_Order $order
     * @return int
     */
    public function checkIfAddressesAreSame(Mage_Sales_Model_Order $order)
    {
        $addMatch = 0;
        $billingAddressHash = null;
        $shippingAddressHash = null;
        if ($order->getBillingAddress() instanceof Mage_Customer_Model_Address_Abstract) {
            $billingAddressHash  = Mage::helper('ops/alias')->generateAddressHash(
                $order->getBillingAddress()
            );
        }
        if ($order->getShippingAddress() instanceof Mage_Customer_Model_Address_Abstract) {
            $shippingAddressHash  = Mage::helper('ops/alias')->generateAddressHash(
                $order->getShippingAddress()
            );
        }

        if ($billingAddressHash === $shippingAddressHash) {
         $addMatch = 1;
        }
        return $addMatch;
    }

}
