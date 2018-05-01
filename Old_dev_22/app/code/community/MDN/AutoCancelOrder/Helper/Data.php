<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : ALLAIRE Benjamin
 * @mail : benjamin@boostmyshop.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_AutoCancelOrder_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Apply checking for cancel orders via button
     */
    public function apply() {

        $orders = $this->getOrdersToCancel(); // ok
        
        foreach ($orders as $order) {

            try {
                $status = $order->getStatus();

                //get payment method
                $paymentMethod = $order->getPayment()->getMethod();
                $nbHour = $this->getDelayForPaymentMethod($paymentMethod);

                //find the age for the payment method
                $created = $order->getCreatedAt();
                $currentDate = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
                $nbHourOrder = round((strtotime($currentDate) - strtotime($created)) / (60 * 60)); // OK

                //check that number of day between order creation date and now > delay
                if ($nbHourOrder >= $nbHour) {

                    // check if the config allow to unhold orders
                    if (Mage::getStoreConfig('autocancelorder/general/unhold')) {
    
                        // check if order is on hold 
                        if ( $status == Mage_Sales_Model_Order::STATE_HOLDED && $order->canUnHold() ) {
                            $this->unHoldOrder($order);
                        }

                    }

                    // cancel the order and write a comment
                    if ($order->canCancel()) {
                        $this->cancelOrder($order);
                    } else {
                        $message = $this->__("Order #%s can not be canceled, check config 'unhold before cancel' or hold it", $order->getIncrementId());
                        $this->addLog($message);
                    }
                } // end if delay > config
            } catch (Exception $ex) {
                $message = $this->__("An error occurred : %s ", $ex->getTraceAsString());
                $this->addLog($message);
            }
        }

    }

    /**
     * return an array of orders witch are configured to be canceled (via backoffice)
     */
    public function getOrdersToCancel() {

        // orders type with selected status
        $statusOrders = Mage::getStoreConfig('autocancelorder/general/apply_on_orders'); // string(21) "pending,closed,holded" 
        $statusOrders = explode(",", $statusOrders);

        // limit of date for considerate orders
        $dateOrderAfter = Mage::getStoreConfig('autocancelorder/general/consider_order_after'); // string(10) "2013-07-23"
        $dateOrderAfter .= " 00:00:00"; // 2013-07-23 00:00:00

        // get orders with previous filters
        $orders = Mage::getModel('sales/order')->getCollection()
                        ->addFieldToFilter('status', $statusOrders)
                        ->addFieldToFilter('created_at', array('gt' => $dateOrderAfter));

        return $orders;
    }

    /**
     * add a log info in table 'auto_cancel_order_log' for historic
     */
    public function addLog($message) {
        $aco = Mage::getModel('AutoCancelOrder/Log');
        $aco->setaco_date(date("Y-m-d H:i:s"));
        $aco->setaco_message($message);
        $aco->save();
    }

    /**
     * get the number of hours allowed to keep an order health
     * 
     * @param <type> $paymentMethod 
     * @return <type> $nbDay
     */
    public function getDelayForPaymentMethod($paymentMethod) {
        $path = 'autocancelorder/delay_cancelation/' . $paymentMethod;
        $nbHour = Mage::getStoreConfig($path); // from config
        
        // apply default config
        if (empty($nbHour)) {
            $path = 'autocancelorder/delay_cancelation/default';
            $nbHour = Mage::getStoreConfig($path);
        }
        return $nbHour;
    }

    /**
     * Unhold order
     * @param <type> $order 
     */
    public function unholdOrder($order) {
        $order->unHold();
        $order->addStatusHistoryComment("Unhold by auto cancel order extension.");
        $order->save();

        $message = $this->__("Order #%s successfully unhold", $order->getIncrementId());

        // notify for historic
        $this->addLog($message);
    }

    /**
     * Cancel order
     * @param <type> $order
     */
    public function cancelOrder($order) {
        $order->cancel();
        $order->addStatusHistoryComment("Canceled by auto cancel order extension.");
        $order->save();

        $message = $this->__("Order #%s was canceled successfully", $order->getIncrementId());

        $this->addLog($message);
    }


    
    /**
     * get the current order on the log page, when clicking
     * on a log -> get the order.
     *  
     * @param $aocId : the id of autocancelorder entry
     * @return : id of current order
     */
    public function getOrderIdToView($aocId){
        
        // get the message of log
        $log = Mage::getModel('AutoCancelOrder/Log')->load($aocId);
        
        // get message of log that contain order incrrement id
        $logMessage = $log->getaco_message();
        
        // extract increment id
        $incrementId = preg_match("/[0-9]{9,15}/", $logMessage, $matches);
        
        if($incrementId){
            // load order   
            $order = Mage::getModel('sales/order')->loadbyIncrementId($matches[0]);
            $orderId = $order->getId();
        }
        else {
             throw new Exception('error order increment id can not be found in log message');
        }
        
        return $orderId;
    }
    
    
}