<?php

class TBT_RewardsReferral_Model_Transfer_Reason_Guestorder extends TBT_Rewards_Model_Transfer_Reason_Abstract
{
     const REASON_TYPE_ID = 23;

     public function getAvailReasons($current_reason, &$availR)
     {
          return $availR;
     }

     public function getOtherReasons()
     {
          return array();
     }

     public function getManualReasons()
     {
          return array();
     }

     public function getDistributionReasons()
     {
          return array(self::REASON_TYPE_ID => Mage::helper('rewardsref')->__('Referral Guest Order'));
     }

     public function getRedemptionReasons()
     {
          return array();
     }

     public function getAllReasons()
     {
          return array(self::REASON_TYPE_ID => Mage::helper('rewardsref')->__('Referral Guest Order'));
     }

}
