<?php

class TBT_Rewardssocial_Model_Mysql4_Pinterest_Pin_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('rewardssocial/pinterest_pin');
    }

    public function filterAllSinceMinTime($customer)
    {
        $minimumWait = Mage::helper('rewardssocial/pinterest_config')->getMinSecondsBetweenPins($customer->getStore());
        $now = time();
        $oldestRequiredTime = $now - $minimumWait;

        $this->addFilter('customer_id', $customer->getId())
            ->addFieldToFilter('UNIX_TIMESTAMP(`created_time`)', array('gteq' => $oldestRequiredTime));

        return $this;
    }
}
