<?php

class TBT_RewardsReferral_Model_Observer_Onestepcheckout extends Varien_Object
{
    /**
     * Observes 'customer_save_before'. Called when an account is being created through the checkout, when
     * Idev Onestepcheckout is used.
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function beforeOnestepcheckout($observer)
    {
        $event = $observer->getEvent();
        if (!$event) {
            return $this;
        }

        $data = Mage::app()->getRequest()->getParams();
        if (!isset($data['billing'])) {
            return $this;
        }

        Mage::getModel('rewardsref/observer_createaccount')->attemptReferralCheck($observer, 'billing', $data);

        return $this;
    }
}
