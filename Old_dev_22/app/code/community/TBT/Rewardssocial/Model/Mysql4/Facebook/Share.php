<?php
/**
 * Facebook Share Resource model
 */
class TBT_Rewardssocial_Model_Mysql4_Facebook_Share extends TBT_Rewards_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('rewardssocial/facebook_share', 'facebook_share_id');
    }

    /**
     * Load a Facebook share by customer and product ID
     * @param  Mage_Core_Model_Abstract $object
     * @param  int                   $customerId Customer ID
     * @param  int                   $productId  Product ID
     * @return TBT_Rewardssocial_Model_Facebook_Share
     */
    public function loadByCustomerAndProductId(Mage_Core_Model_Abstract $object, $customerId, $productId)
    {
        return $this->load($object, array($customerId, $productId), array('customer_id', 'product_id'));
    }
}