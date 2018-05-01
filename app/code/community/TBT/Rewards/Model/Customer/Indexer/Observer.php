<?php

class TBT_Rewards_Model_Customer_Indexer_Observer extends Varien_Object
{
    const REWARDS_TRANSFER_ENTITY = TBT_Rewards_Model_Customer_Indexer_Points::REWARDS_TRANSFER_ENTITY;
    const REWARDS_CUSTOMER_ENTITY = TBT_Rewards_Model_Customer_Indexer_Points::REWARDS_CUSTOMER_ENTITY;

    protected $_oldTransfer = null;

    /**
     * Update points via observer method (updateUsablePointsBalance)
     * @param  Varien_Event_Observer $observer
     * @return TBT_Rewards_Model_Customer_Indexer_Points
     */
    public function updateUsablePointsBalance($observer)
    {
        try {
            if(!Mage::helper('rewards/customer_points_index')->canIndex()) {
                //shouldn't be using the index
                return $this;
            }

            $transfer = Mage::helper('rewards/dispatch')->getEventObject($observer);

            if ($this->_getShouldSkipIndex($transfer)) {
                return $this;
            }

            $this->_oldTransfer = $transfer;

            Mage::getSingleton ( 'index/indexer' )->processEntityAction ( $transfer, self::REWARDS_TRANSFER_ENTITY, Mage_Index_Model_Event::TYPE_SAVE );

        } catch(Exception $e) {
            Mage::helper('rewards/debug')->logException($e);
        }
        return $this;
    }

    /**
     * Observes 'rewards_order_points_transfer_after_create' which gets triggered when point transfers are created for
     * an order. Check TBT_Rewards_Model_Observer_Sales_Order_Save_After_Create::createPointsTransfers()
     *
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function updateIndexAfterOrderPointsCreated($observer)
    {
        $this->_updateIndexAfterOrderAction($observer);
        return $this;
    }

    /**
     * Observes 'rewards_order_points_transfer_after_approved' which gets triggered when point transfers are approved for
     * an order. Check TBT_Rewards_Model_Observer_Sales_Order_Save_After_Approve::approveAssociatedPendingTransfersOnShipment()
     * and TBT_Rewards_Model_Observer_Sales_Order_Invoice_Pay::approveAssociatedPendingTransfersOnInvoice()
     *
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function updateIndexAfterOrderPointsApproved($observer)
    {
        $this->_updateIndexAfterOrderAction($observer);
        return $this;
    }

    /**
     * Observes 'rewards_sales_order_transfer_ajuster_done' which gets triggered when an admin operation is performed
     * on an order which leads to some points adjustments (canceling order).
     *
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function updateIndexAfterOrderCanceled($observer)
    {
        $this->_updateIndexAfterOrderAction($observer);
        return $this;
    }

    /**
     * Observes 'rewards_sales_order_payment_automatic_cancel_done' which gets triggered when an admin operation is
     * performed on an order which leads to some points adjustments (if it a mass admin cancel operation, a payment
     * failure at checkout (paypal, authorize.net), or if Magento prior to 1.4.1.1 and it's a single admin order cancel).
     *
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function updateIndexAfterPaymentCanceled($observer)
    {
        $this->_updateIndexAfterOrderAction($observer);
        return $this;
    }

    /**
     * Triggers our Customer Points Balance Indexer to updates customer points balance after an order operation.
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    protected function _updateIndexAfterOrderAction($observer)
    {
       try {
            if (!Mage::helper('rewards/customer_points_index')->canIndex()) {
                //shouldn't be using the index
                return $this;
            }

            $event = $observer->getEvent();
            if (!$event) {
                return $this;
            }

            $order            = $event->getOrder();
            $session_customer = $this->_getRewardsCustomer($order);

            if (!$session_customer || !$session_customer->getId()) {
                // Only if a customer model exists and that customer has been already created.
                Mage::helper('rewards/debug')->warn("No customer was found for this order (#{$order->getIncrementId()}), so their points index could not be updated.");

                return $this;
            }

            Mage::getSingleton('index/indexer')->processEntityAction($session_customer, self::REWARDS_CUSTOMER_ENTITY, Mage_Index_Model_Event::TYPE_SAVE);

        } catch(Exception $e) {
            Mage::helper('rewards/debug')->logException($e);
        }

        return $this;
    }

    /**
     * Update points via observer method (updateIndexBeforeOrderSave)
     * @param  Varien_Event_Observer $observer
     * @return TBT_Rewards_Model_Customer_Indexer_Points
     */
    public function updateIndexBeforeOrderSave($observer)
    {
        try {
            if(!Mage::helper('rewards/customer_points_index')->canIndex()) {
                //shouldn't be using the index
                return $this;
            }

            $event = $observer->getEvent();
            if (!$event) {
                return $this;
            }

            $order = $event->getOrder();
            if (!$order) {
                return $this;
            }

            $session_customer = $this->_getRewardsCustomer($order);

            if(!$session_customer || !$session_customer->getId()) {
                // no logging required as we'll check again in self::_updateIndexAfterOrderAction()
                return $this;
            }

            Mage::getSingleton('index/indexer')->processEntityAction($session_customer, self::REWARDS_CUSTOMER_ENTITY, Mage_Index_Model_Event::TYPE_SAVE);

        } catch(Exception $e) {
            Mage::helper('rewards/debug')->logException($e);
        }

        return $this;
    }

    /**
     * Update points via observer method (updateIndexOnNewCustomer)
     * @param  Varien_Event_Observer $observer
     * @return TBT_Rewards_Model_Customer_Indexer_Points
     */
    public function updateIndexOnNewCustomer($observer)
    {
        try {
            if(!Mage::helper('rewards/customer_points_index')->canIndex()) {
                //shouldn't be using the index
                return $this;
            }

            $customer = $observer->getEvent()->getCustomer();

            if(!$customer || !$customer->getId()) {
                // Only if a customer model exists and that customer has been already created.
                Mage::helper('rewards/customer_points_index')->error();
                Mage::helper('rewards/debug')->error("Customer model does not exist in observer or that customer has not been saved yet.  This caused the points index to be to become out of sync and disabled.");

                return $this;
            }

            $customer = Mage::getModel('rewards/customer')->load($customer->getId());
            Mage::getSingleton('index/indexer')->processEntityAction($customer, self::REWARDS_CUSTOMER_ENTITY, Mage_Index_Model_Event::TYPE_SAVE);

        } catch(Exception $e) {
            Mage::helper('rewards/debug')->logException($e);
        }

        return $this;
    }

    /**
     * Fetches the customer model from either an order/quote or the session, depending on what's available.
     * @param Mage_Sales_Model_Order $order or quote
     * @return TBT_Rewards_Model_Customer
     */
    protected function _getRewardsCustomer($order=null)
    {
        // If the customer exists in the order, use that. If not, use the session customer from the rewards model.
        if ($order) {
            if( $order ->getCustomer() ) {
                // The index session dispatch requires a rewards model, so we should load that.
                $session_customer = $order->getCustomer();
                if (! ($session_customer instanceof TBT_Rewards_Model_Customer)) {
                    $session_customer = Mage::getModel('rewards/customer')->getRewardsCustomer( $session_customer );
                }
            } else {
                $session_customer = Mage::getModel('rewards/customer')->load( $order->getCustomerId() );
            }
        } else {
            $session_customer = $this->_getRewardsSess()->getSessionCustomer();
        }

        return $session_customer;
    }

    protected function _getShouldSkipIndex($transfer)
    {
        $isOrderTransfer = $transfer->getReferenceType() == TBT_Rewards_Model_Transfer_Reference::REFERENCE_ORDER;
        $skip = $this->_alreadyIndexed($transfer) || $isOrderTransfer;

        return $skip;
    }

    /**
     * Fetches the customer rewards session.
     *
     * @return TBT_Rewards_Model_Session
     */
    protected function _getRewardsSess()
    {
        return Mage::getSingleton ( 'rewards/session' );
    }

    /**
     * Fetches the checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton ( 'checkout/session' );
    }

    /**
     * This will check if this transfer was already indexed. Needed for
     * new transfers which are saved then saved again to set the 'source_reference_id'
     * field. (see TBT_Rewards_Model_Transfer::_afterSave())
     *
     * @param  TBT_Rewards_Model_Transfer $transfer
     * @return bool
     */
    protected function _alreadyIndexed($transfer)
    {
        if (!$this->_oldTransfer) {
            return false;
        }

        if ($this->_oldTransfer->getId() != $transfer->getId()) {
            return false;
        }

        if ($this->_oldTransfer->getStatus() != $transfer->getStatus()) {
            return false;
        }

        return true;
    }
}