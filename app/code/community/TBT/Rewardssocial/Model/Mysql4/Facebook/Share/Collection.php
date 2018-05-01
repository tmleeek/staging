<?php

/**
 * Facebook Share Collection model
 */
class TBT_Rewardssocial_Model_Mysql4_Facebook_Share_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('rewardssocial/facebook_share');
    }

    /**
     * This we'll filter the collection for a customer and return all Facebook product shares that the customer did since
     * now and the a config option, called "Minimum Time Between Product Shares", that represents the minimum number of
     * seconds that need to pass for the customer to be rewarded for this new action.
     * @param  Mage_Customer_Model_Customer $customer The customer model
     * @return $this
     */
    public function filterAllSinceMinTime($customer)
    {
        $minimumWait = Mage::helper('rewardssocial/facebook_config')->getMinSecondsBetweenFacebookProductShares($customer->getStore());
        $now = time();
        $oldestRequiredTime = $now - $minimumWait;

        $this->addFieldToFilter('customer_id', array('eq' => (string)$customer->getId()))
            ->addFieldToFilter('UNIX_TIMESTAMP(`created_time`)', array('gteq' => $oldestRequiredTime));

        return $this;
    }
}