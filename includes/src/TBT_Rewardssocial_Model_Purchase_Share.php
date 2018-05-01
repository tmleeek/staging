<?php

class TBT_Rewardssocial_Model_Purchase_Share extends TBT_Rewardssocial_Model_Abstract
{
    /**
     * the share action type (what social channel the purchase is shared) is represented in the DB as int.
     * @var array
     **/
    protected $_actionTypeMapping = array(
        'facebook'  => 1,
        'google'    => 2,
        'pinterest' => 3,
        'twitter'   => 4,
    );

    protected function _construct()
    {
        parent::_construct();
        $this->_init('rewardssocial/purchase_share');

        return $this;
    }

    public function loadByCustomerOrderAndType($customerId, $orderId, $actionType)
    {
        $this->getResource()->loadByCustomerOrderAndType($this, $customerId, $orderId, $actionType);
        return $this;
    }

    public function getActionTypeId($actionType)
    {
        if (!$actionType) {
            return 0;
        }

        $actionTypeId = isset($this->_actionTypeMapping[strtolower($actionType)])
            ? $this->_actionTypeMapping[strtolower($actionType)]
            : 0;

        return $actionTypeId;
    }

    public function hasAlreadySharedPurchase($customerId, $productId, $orderId, $actionType)
    {
        $actionTypeId = $this->getActionTypeId($actionType);
        $share = Mage::getModel('rewardssocial/purchase_share')->loadByCustomerOrderAndType($customerId, $orderId, $actionTypeId);

        return (bool) $share->getId();
    }
}
