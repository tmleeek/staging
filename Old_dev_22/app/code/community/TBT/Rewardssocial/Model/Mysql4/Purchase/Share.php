<?php

class TBT_Rewardssocial_Model_Mysql4_Purchase_Share extends TBT_Rewards_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('rewardssocial/purchase_share', 'purchase_share_id');
        return $this;
    }

    public function loadByCustomerOrderAndType(Mage_Core_Model_Abstract $object, $customerId, $orderId, $actionTypeId)
    {
        return $this->load($object, array($customerId, $orderId, $actionTypeId), array('customer_id', 'order_id', 'type_id'));
    }
}
