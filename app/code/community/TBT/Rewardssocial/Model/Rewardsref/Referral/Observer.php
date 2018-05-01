<?php

class TBT_Rewardssocial_Model_Rewardsref_Referral_Observer extends Varien_Object
{
    public function subscribe(Varien_Event_Observer $observer)
    {
        try {
            $event = $observer->getEvent();
            if (!$event) {
                return $this;
            }

            $affiliate = $event->getAffiliate();
            if (!$affiliate) {
                return $this;
            }

            $referral = $event->getReferral();
            if (!$referral) {
                return $this;
            }

            $customer = Mage::getModel('rewardssocial/customer')->load($affiliate->getId());
            if (!$customer->getId()) {
                $customer->setData($affiliate->getData())
                    ->setId($affiliate->getId());
            }

            $minimumWait = $customer->getTimeUntilNextReferralShareAllowed();
            if($minimumWait > 0) {
                Mage::getSingleton('core/session')->addError(
                    Mage::helper('rewardssocial')->__('Please wait %s second(s) before sharing your referral link again, if you want to be rewarded.', $minimumWait)
                );
                return $this;
            }

            if ($customer->isMaxDailyReferralShareReached()) {
                $maxShares = $this->_getMaxReferralSharesPerDay($customer);
                Mage::getSingleton('core/session')->addError(
                    Mage::helper('rewardssocial')->__("You've reached referral link sharing rewards limit for today (%s).", $maxShares)
                );
                return $this;
            }

            $referralShare = Mage::getModel('rewardssocial/referral_share')
                ->setCustomerId($customer->getId())
                ->save();

            $validatorModel = Mage::getModel('rewardssocial/referral_share_validator');
            $validatorModel->initReward($customer->getId(), $referralShare->getId());

            $message = Mage::helper('rewardssocial')->__("Thanks for sharing your referral link!");
            $predictedPoints = $validatorModel->getPredictedPoints();
            if (count($predictedPoints) > 0) {
                $pointsString = (string) Mage::getModel('rewards/points')->set($predictedPoints);
                $message = Mage::helper('rewardssocial')->__("You've earned <b>%s</b> for sharing your referral link!", $pointsString);
            }
            $this->_getSession()->addSuccess($message);
        } catch (Exception $ex) {
            Mage::helper('rewards')->logException($ex);
            throw new Exception(
                Mage::helper('rewardssocial')->__("Could not reward you for your sharing your referral link."),
                null, $ex
            );
        }

        return $this;
    }

    protected function _getMaxReferralSharesPerDay($customer)
    {
        return Mage::helper('rewardssocial/referral_config')->getMaxReferralSharesPerDay($customer->getStore());
    }

    /**
     * @return Mage_Core_Model_Session
     */
    protected function _getSession() {
        return Mage::getSingleton('core/session');
    }
}
