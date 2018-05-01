<?php

class TBT_Rewardssocial_Model_Mysql4_Referral_Share_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('rewardssocial/referral_share');

        return $this;
    }

    public function filterAllSinceMinTime($customer)
    {
        $minimumWait = Mage::helper('rewardssocial/referral_config')->getMinSecondsBetweenShares($customer->getStore());
        $now = time();
        $oldestRequiredTime = $now - $minimumWait;

        $this->addFilter('customer_id', $customer->getId())
            ->addFieldToFilter('UNIX_TIMESTAMP(`created_time`)', array('gteq' => $oldestRequiredTime));

        return $this;
    }
}
