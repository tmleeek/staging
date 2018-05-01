<?php

class TBT_Rewardssocial_Model_Mysql4_Google_PlusOne_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('rewardssocial/google_plusOne');
    }

    public function filterAllSinceMinTime($customer)
    {
        $minimumWait = Mage::helper('rewardssocial/google_config')->getMinSecondsBetweenPlusOnes($customer->getStore());
        $now = time();
        $oldestRequiredTime = $now - $minimumWait;

        $this->addFilter('customer_id', $customer->getId())
            ->addFieldToFilter('UNIX_TIMESTAMP(`created_time`)', array('gteq' => $oldestRequiredTime));

        return $this;
    }
}
