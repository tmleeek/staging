<?php

class TBT_Rewardssocial_Model_Mysql4_Pinterest_Pin extends TBT_Rewards_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('rewardssocial/pinterest_pin', 'pinterest_pin_id');
        return $this;
    }

    public function loadByCustomerAndUrl(Mage_Core_Model_Abstract $object, $customerId, $pinnedUrl)
    {
        return $this->load($object, array($customerId, $pinnedUrl), array('customer_id', 'pinned_url'));
    }
}
