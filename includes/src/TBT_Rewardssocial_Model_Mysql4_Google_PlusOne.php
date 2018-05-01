<?php

class TBT_Rewardssocial_Model_Mysql4_Google_PlusOne extends TBT_Rewards_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('rewardssocial/google_plusone', 'google_plusone_id');
        return $this;
    }

    public function loadByCustomerAndUrl(Mage_Core_Model_Abstract $object, $customerId, $url)
    {
        return $this->load($object, array($customerId, $url), array('customer_id', 'url'));
    }
}
