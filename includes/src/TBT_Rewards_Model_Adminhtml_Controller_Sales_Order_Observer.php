<?php

class TBT_Rewards_Model_Adminhtml_Controller_Sales_Order_Observer extends Varien_Object
{
    /**
     * Observes the controller_action_predispatch_adminhtml_sales_order_cancel event.
     * Ensures the points adjuster can initialize successfully (which includes checking the
     * adjusted redemptions against balance) to ensure the customer can afford them before
     * allowing the cancellation to continue. (It stops this process by placing an error into
     * the rewards session, then rewards/sales_order_observer::cancel() pulls
     * the error back out again and halts the cancellation.)
     * @param Varien_Event_Observer $observer
     * @return self
     */
    public function cancelPreDispatch($observer)
    {
        // if not at least Magento 1.4.1.1, we can't inject the points adjuster popup so we are falling back
        // on automatic cancelling the points, if option enabled in admin
        if (! Mage::helper('rewards/version')->isBaseMageVersionAtLeast('1.4.1.1')) {
            return $this;
        }

        $event = $observer->getEvent();
        if (!$event) {
            return $this;
        }

        $action = $event->getControllerAction();
        if (!$action) {
            return $this;
        }

        $params = $action->getRequest()->getParams();
        $order = Mage::getModel('sales/order')->load($params['order_id']);

        // get the adjusted points values from the merchant, if any
        $adjustedSpent = $this->_getFromParams($params, 'adjustment_points_spent');
        $adjustedEarned = $this->_getFromParams($params, 'adjustment_points_earned');

        $this->_getAdjuster()
            ->setOrder($order)
            ->setAdjustedSpent($adjustedSpent)
            ->setAdjustedEarned($adjustedEarned);

        // dispatching this event so we can hook aditional logic in Referral module
        Mage::dispatchEvent('rewards_adjust_points_init_before',
            array('adjuster' => $this->_getAdjuster()));

        try {
            $this->_getAdjuster()->init();

            // setting this for TBT_Rewards_Model_Sales_Order_Payment_Observer::automaticCancel()
            Mage::register('sweet_tooth_single_admin_order_cancel', 1);

        } catch (Exception $ex) {
            $this->_getRewardsSession()->addError($ex->getMessage());
            $this->_getRewardsSession()
                ->getMessages()
                ->getLastAddedMessage()
                ->setIdentifier('rewards_order_cancel_failed');
        }

        // dispatching this event so we can hook aditional logic in Referral module
        Mage::dispatchEvent('rewards_adjust_points_init_after',
            array('adjuster' => $this->_getAdjuster()));

        return $this;
    }

    /**
     * Observes the controller_action_postdispatch_adminhtml_sales_order_cancel event.
     * Finishes order cancellation adjustment through the points adjuster.
     * @param Varien_Event_Observer $observer
     * @return self
     */
    public function cancelPostDispatch($observer)
    {
        // we don't want to complete this task if the cancellation returned an error
        if (count($this->_getSession()->getMessages()->getErrors()) > 0) {
            return $this;
        }

        if (! Mage::helper('rewards/version')->isBaseMageVersionAtLeast('1.4.1.1')) {
            return $this;
        }

        if (Mage::registry('sweet_tooth_single_admin_order_cancel')) {
            // we remove this from registry
            Mage::unregister('sweet_tooth_single_admin_order_cancel');
        }

        try {
            $this->_getAdjuster()
                ->setTransferComments($this->__("Points adjustment made during the process of cancelling an order."))
                ->execute();

        } catch (Exception $ex) {
            $this->_getSession()->addError($ex->getMessage());
        }

        return $this;
    }

    /**
     * Performs a bunch of checks when getting our values out of the controller params,
     * so it doesn't muck up the primary code up above.
     * @param array $params
     * @param string $key
     * @return mixed
     */
    protected function _getFromParams($params, $key)
    {
        if (!isset($params['rewards'])) {
            return null;
        }

        if (!is_array($params['rewards'])) {
            return null;
        }

        if (!array_key_exists($key, $params['rewards'])) {
            return null;
        }

        return $params['rewards'][$key];
    }

    /**
     * @return TBT_Rewards_Model_Sales_Order_Transfer_Adjuster
     */
    protected function _getAdjuster()
    {
        return Mage::getSingleton('rewards/sales_order_transfer_adjuster');
    }

    /**
     * Retrieve adminhtml session model object
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * @return TBT_Rewards_Model_Session
     */
    protected function _getRewardsSession()
    {
        return Mage::getSingleton('rewards/session');
    }

    protected function __()
    {
        $helper = Mage::helper('rewards');
        $args = func_get_args();

        return call_user_func_array(array($helper, '__'), $args);
    }
}
