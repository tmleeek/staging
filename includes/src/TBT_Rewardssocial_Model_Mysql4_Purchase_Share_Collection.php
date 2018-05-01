<?php

class TBT_Rewardssocial_Model_Mysql4_Purchase_Share_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('rewardssocial/purchase_share');

        return $this;
    }
}
