<?php
class TBT_Rewardssocial_Model_Facebook_Like_Rewards_Observer extends Varien_Object
{
    public function transferVestation($observer)
    {
        try {
            $event = $observer->getEvent();
            if (!$event) {
                return $this;
            }

            $transfer = $event->getTransfer();
            if (!$transfer) {
                return $this;
            }

            $result = $event->getResult();
            if (!$result) {
                return $this;
            }

            // TODO: check Facebook if user is still Liking this thing
            $doesFacebookAccountStillLikeThisThing = true;

            if (!$doesFacebookAccountStillLikeThisThing) {
                $result->setIsSafeToApprove(false);
            }
        } catch (Exception $ex) {
            Mage::helper('rewards')->logException($ex);
        }

        return $this;
    }
}
