<?php

class TBT_Rewardssocial_Model_Mysql4_Customer extends TBT_Rewards_Model_Mysql4_Abstract
{
    protected $_isPkAutoIncrement = false;

    protected function _construct()
    {
        $this->_init('rewardssocial/customer', 'customer_id');
        return $this;
    }
}
