<?php

class TBT_Rewardssocial_Model_Facebook_Like extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('rewardssocial/facebook_like');
    }

    /**
     * create FB waiting time string
     * @param int $waitTime seconds
     * @return FB waiting time string
     */
    public function getFBWaitingTimeString($waitTime)
    {
        $rSocialHelper = Mage::helper("rewardssocial");
        $waitTimeSepArr = Mage::helper("rewards/datetime")->secondsToDayFormat($waitTime);
        $waitTimeString = "";

        if ($waitTimeSepArr['d'] > 0) {
            $waitTimeString = $waitTimeSepArr['d'] . " " . $rSocialHelper->__("day(s)");
        } else if ($waitTimeSepArr['h'] >= 23 && $waitTimeSepArr['m'] > 0) {
            $waitTimeString = $rSocialHelper->__("1 day");
        } else if ($waitTimeSepArr['h'] > 0) {
            $waitTimeString = $waitTimeSepArr['h'] . " " . $rSocialHelper->__("hour(s)");
        } else {
            $waitTimeString = $waitTimeSepArr['m'] . " " . $rSocialHelper->__("minute(s)");
        }

        return $waitTimeString;
    }
}