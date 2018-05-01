<?php

class TBT_RewardsReferral_Block_Field_Abstract extends Mage_Core_Block_Template
{

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function showReferralCode()
    {
        return Mage::getStoreConfigFlag('rewards/referral/show_referral_code');
    }

    public function showReferralCodeShort()
    {
        return Mage::getStoreConfigFlag('rewards/referral/show_referral_code');
    }

    public function showReferralEmail()
    {
        return Mage::getStoreConfigFlag('rewards/referral/show_referral_email');
    }

    //@nelkaake Added on Saturday June 26, 2010:
    public function getCustomer()
    {
        return Mage::getSingleton('rewards/session')->getCustomer();
    }

    //@nelkaake Added on Saturday June 26, 2010:
    public function isCustomerLoggedIn()
    {
        return Mage::getSingleton('rewards/session')->isCustomerLoggedIn();
    }

    /**
     * Returns the current affiliate's code
     * @return string short or long code (depending on store configuration) for the affiliate currently in the session.
     */
    public function getCurrentAffiliate()
    {
        $affiliate_customer = Mage::helper('rewardsref/code')->getReferringCustomer();

        if (!$affiliate_customer) {
            return "";
        }

        $code = Mage::helper('rewardsref/code')->getCode($affiliate_customer->getEmail());

        if (Mage::getStoreConfigFlag('rewards/referral/show_referral_short_code')) {
            $code = Mage::helper('rewardsref/shortcode')->getCode($affiliate_customer->getId());
        }

        if (empty($code)) {
            return "";
        }

        return $code;
    }

    /**
     * Checks if there are any rules that applies to Guests (rule of type 'Referral Makes Any Order' and action
     * 'by_fixed').
     * @return  boolean True if any rules that apply for Guests exist, false otherwise.
     */
    public function getApplicableToGuestReferralOrderRules()
    {
        $applicable_rules = Mage::getSingleton('rewardsref/validator')
                            ->getApplicableRules(TBT_RewardsReferral_Model_Special_Order::ACTION_REFERRAL_ORDER);

        foreach ($applicable_rules as $arr) {
            // To check rule action guest customers will only earn points if action is Fixed amount
            if (($arr->getPointsAmount() > 0) && ($arr->getSimpleAction() == "by_fixed")) {
                return true;
            }

            if (($arr->getPointsAmount() > 0) && ($arr->getSimpleAction() == "by_percent")) {
                return false;
            }
        }

        return false;
    }

    /**
     * Checks if there are any rules of type 'Referral Makes Any Order' applicable.
     * @return  boolean True if any rules of this type exist, false otherwise.
     */
    public function getApplicableReferralOrderRules()
    {
        $applicable_rules = Mage::getSingleton('rewardsref/validator')
                            ->getApplicableRules(TBT_RewardsReferral_Model_Special_Order::ACTION_REFERRAL_ORDER);

        foreach ($applicable_rules as $arr) {

            if ($arr->getPointsAmount() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if there are any rules of type 'Referral Makes First Order'
     * @return  boolean True if any rules of this type exist, false otherwise.
     */
    public function getApplicableReferralFirstOrderRules()
    {
        $applicable_rules = Mage::getSingleton('rewardsref/validator')
                            ->getApplicableRules(TBT_RewardsReferral_Model_Special_Firstorder::ACTION_REFERRAL_FIRST_ORDER);

        foreach ($applicable_rules as $arr) {
            //  Check points amount if rule is active/inactive
            if ($arr->getPointsAmount() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if there are any rules of type 'Referral Signs Up To Website'
     * @return  boolean True if any rules of this type exist, false otherwise.
     */
    public function getApplicableReferralSignupRules()
    {
        $applicable_rules = Mage::getSingleton('rewardsref/validator')
                            ->getApplicableRules(TBT_RewardsReferral_Model_Special_Signup::ACTION_REFERRAL_SIGNUP);

        foreach ($applicable_rules as $arr) {
            //  Check points amount if rule is active/inactive
            if ($arr->getPointsAmount() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks when to display Referral Block on Checkout.
     * @return boolean True only if all rules are active / configuration is set to yes /  customer is not logged
     */
    public function getShowReferralBlock()
    {
        if (!Mage::getStoreConfigFlag('rewards/referral/show_in_onepage_checkout')) {
            return false;
        }
        if (!$this->showReferralEmail() && !$this->showReferralCode() && !$this->showReferralCode()) {
            return false;
        }
        if ($this->isCustomerLoggedIn()) {
            return false;
        }

       return true;
    }

    /**
     * Checks if Guest Checkouts should earn points for affiliates. If yes, display referral box.
     * @return  boolean
     */
    public function getShowGuestReferralField()
    {
        if ($this->getApplicableToGuestReferralOrderRules()) {
            // if Only Referral Makes any order with action Fixed amount is enabled display referral block.
            return true;
        }

        return false;
    }

}