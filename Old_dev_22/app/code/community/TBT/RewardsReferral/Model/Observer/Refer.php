<?php

class TBT_RewardsReferral_Model_Observer_Refer extends Varien_Object
{

    public function recordPointsUponRegistration($observer)
    {
        try {
            $model = Mage::getModel('rewardsref/referral_signup');
            $newCustomer = $observer->getEvent()->getCustomer();
            $model->triggerEvent($newCustomer);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * Observes 'sales_order_save_after' event.
     * Will create transfers for affiliate points related to the order, if it's the case.
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function recordPointsForOrderEvent($observer)
    {

        // 'sales_order_save_after' event is fired twice, so make sure we only process it once.
        if (Mage::registry('rewards_referral_record_points_for_order')) {
            return $this;
        }

        Mage::register('rewards_referral_record_points_for_order', 1);

        $orderObj = $observer->getEvent()->getOrder();
        $orderId = $orderObj->getId();
        $order = Mage::getModel('rewards/sales_order')->load($orderId);

        // if user doesn't have an affiliate don't process further
        $referralModel = Mage::getModel('rewardsref/referral');
        if (!$referralModel->referralExists($order->getCustomerEmail())) {
            return $this;
        }

        $customerId = $order->getCustomerId();
        if (!$customerId) {
            // link to referral any order rule only
            $this->recordPointsGuestOrder($order);
            return $this;
        }

        $this->recordPointsUponFirstOrder($order);
        $this->recordPointsOrder($order);

        return $this;
    }

    /**
     *
     *
     * @param Mage_Sales_Model_Order $order
     * @return TBT_RewardsReferral_Model_Observer_Refer
     */
    public function recordPointsUponFirstOrder($order)
    {
        try {
            $model = Mage::getModel('rewardsref/referral_firstorder');
            $model->setOrder($order);
            if ($model->isSubscribed($order->getCustomerEmail()) && false == $model->isConfirmed($order->getCustomerEmail())) {
                $customer = Mage::getModel('rewards/customer')->load($order->getCustomerId());
                $model->triggerEvent($customer, $order->getId());
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    /**
     *
     * @param TBT_Rewards_Model_Sales_Order $order
     * @return TBT_RewardsReferral_Model_Observer_Refer
     */
    public function recordPointsOrder($order)
    {
        try {
            $model = Mage::getModel('rewardsref/referral_order');
            $model->setOrder($order);
            $child = Mage::getModel('rewards/customer')->load($order->getCustomerId());
            $affiliate = Mage::getModel('rewards/customer')->load($model->getReferralParentId());
            $model->triggerEvent($child, $order->getId());
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * Apply only for referral "Any order" rule only
     *
     * @param TBT_Rewards_Model_Sales_Order $order
     * @return TBT_RewardsReferral_Model_Observer_Refer
     */
    public function recordPointsGuestOrder($order)
    {
        try {
            $model = Mage::getModel('rewardsref/referral_guestorder');
            $model->setOrder($order);
            $emptyCustomerObj = Mage::getModel("customer/customer");
            $model->triggerEvent($emptyCustomerObj, $order->getId());
        } catch (Exception $e) {
            Mage::helper("rewards")->logException($e);
        }

        return $this;
    }

}
