<?php

class TBT_RewardsReferral_Model_Referral_Guestorder extends TBT_RewardsReferral_Model_Referral_Abstract
{
    const REFERRAL_STATUS = 1;

    /**
     * @deprecated  user self::REFERRAL_STATUS
     */
    const REFERRAL_STATUS_ID = 4;

    public function getReferralStatusId()
    {
        return self::REFERRAL_STATUS;
    }

    public function getReferralTransferMessage($newCustomer)
    {
        return Mage::getStoreConfig('rewards/transferComments/referralGuestOrder');
    }

    public function getReferralPointsForOrder($ro_rule, $order)
    {
        $simpleAction    = $ro_rule->getSimpleAction();
        $partial_earning = 0;

        if ($simpleAction == 'by_fixed') {
            $partial_earning = Mage::getModel('rewards/points')->set(array(
                $ro_rule->getPointsCurrencyId() => $ro_rule->getPointsAmount()
            ));
        } else {
            // Prior to Sweet Tooth Ref 3.1, there was nothing but by_percent, so default to by_percent if nothing is specified
            $simpleAction    == 'by_percent';
            $percent         = $ro_rule->getPointsAmount();
            $full_earning    = Mage::getModel('rewards/points')->set($order->getTotalEarnedPoints());
            $partial_earning = $full_earning->getPercent($percent);
        }

        return $partial_earning;
     }

    /**
     * requires setOrder($order) to be set!
     *
     * @return type
     */
    public function getTotalReferralPoints()
    {
        $points = Mage::getModel('rewards/points');
        if ($this->hasOrder()) {
            $applicable_rules = $this->_getApplicableReferralOrderRules();
            foreach ($applicable_rules as $arr) {
                $points->add($this->getReferralPointsForOrder($arr, $this->getOrder()));
            }
        }

        return $points;
    }

    public function getTransferReasonId()
    {
        return TBT_RewardsReferral_Model_Transfer_Reason_Guestorder::REASON_TYPE_ID;
    }

    protected function _getApplicableReferralOrderRules()
    {
        $applicable_rules = Mage::getSingleton('rewardsref/validator')
            ->getApplicableRules(TBT_RewardsReferral_Model_Special_Order::ACTION_REFERRAL_ORDER);

        return $applicable_rules;
    }

    public function hasReferralPoints()
    {
        foreach ($this->_getApplicableReferralOrderRules() as $arr) {
            if ($arr->getPointsAmount() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param int $orderId
     * @return TBT_RewardsReferral_Model_Referral_Guestorder
     */
    public function triggerEvent(Mage_Customer_Model_Customer $customer, $orderId = null)
    {
        $affiliate = $this->getReferrerDetails();
        if(!$affiliate) {
            return $this;
        }

        $points = $this->getTotalReferralPoints();
        if ($points->isEmpty()) {
            return $this;
        }

        $order = $this->getOrder();
        if ($order) {
            $order = Mage::getModel('rewards/sales_order')->load($orderId);
        }

        if (!$order->hasData()) {
            return $this;
        }

        $this->loadGuestDetails($order); // Set information for email
        $this->loadByEmail($this->getReferralEmail());

        // update referral
        // if customer is already registered but placing order as guest, make sure we don't overwrite it's ID
        $childId = is_null($this->getReferralChildId()) ? null : $this->getReferralChildId();
        $this->setReferralChildId($childId);
        $this->_updateReferralStatus($this->getReferralStatusId());
        $this->save();

        try {
            foreach ($points->getPoints() as $currencyId => $points_amount) {
                $transfer       = Mage::getModel('rewardsref/transfer');
                $transferStatus = Mage::getStoreConfig('rewards/InitialTransferStatus/ReferralOrder');
                $transfer->create(
                    $points_amount,
                    $currencyId,
                    $affiliate->getId(),
                    -2,
                    $this->getReferralTransferMessage(null),
                    $this->getTransferReasonId(),
                    $transferStatus,
                    $orderId
                );
            }

            if ($affiliate->getRewardsrefNotifyOnReferral()) {
                $msg = $this->getReferralTransferMessage(null);
                $this->sendConfirmation($affiliate, $order->getEmail(), $this->getCustomerText(), $msg, (string) $points);
            }
        } catch (Exception $e) {
            Mage::helper("rewards")->logException($e);
        }

        return $this;
    }

    /**
     * Set Additional informations
     *
     * @param TBT_Rewards_Model_Sales_Order $order
     * @return TBT_RewardsReferral_Model_Referral_Guestorder
     */
    public function loadGuestDetails(TBT_Rewards_Model_Sales_Order $order)
    {
        $guestName  = $order->getBillingAddress()->getName();
        $guestEmail = $order->getCustomerEmail();

        $this->setReferralName($guestName);
        $this->setReferralEmail($guestEmail);

        return $this;
    }

    public function getReferrerDetails()
    {
        // get referrer email from customer session
        $referrerEmail = Mage::getSingleton('core/session')->getReferrerEmail();
        if (!$referrerEmail) {
            return false;
        }

        // get referrer details
        $referrer = Mage::getModel('rewards/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByEmail($referrerEmail);

        if (!$referrer) {
            return false;
        }

        return $referrer;
    }

    public function getCustomerText()
    {
        return  Mage::helper("rewards")->__("Guest");
    }

}
