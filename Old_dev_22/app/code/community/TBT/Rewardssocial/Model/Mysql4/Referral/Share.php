<?php

class TBT_Rewardssocial_Model_Mysql4_Referral_Share extends TBT_Rewards_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('rewardssocial/referral_share', 'referral_share_id');
        return $this;
    }
}
