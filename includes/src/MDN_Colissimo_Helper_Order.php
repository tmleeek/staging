<?php
/**
 * Description
 * @package MDN\Colissimo\Helper
 */
class MDN_Colissimo_Helper_Order extends Mage_Core_Helper_Abstract
{
    /**
     * Checks if an order is valid to be used by Colissimo module
     * @param Mage_Sales_Model_Order $order Order to check
     * @return bool Whether or not an order can be used with Colissimo shipment module
     */
    public function isEligibleForColissimoShipment($order)
    {
        if (is_object($order)) {
            $shippingMethod = $order->getshipping_method();

            $forcedMethods = explode(',', Mage::getStoreConfig('colissimo/account_shipment/force_colissimo_on_methods'));
            
            if (preg_match('/colissimo/', $order->getshipping_method()) || in_array($order->getshipping_method(), $forcedMethods)) {
                return true;
            }

            return false;
        } else {
            throw new Exception('This is not a valid order object !');
        }
    }
}