<?php

class TBT_RewardsApi_Model_Sales_Order_Api
{
    const CONFIG_XPATH_SYNC_ORDERS = 'rewards/platform_sync/enable';

    /**
     * Going with a very simple and fail safe implementation here.  Not extending the
     * Mage_Sales API because the items() method doesn't provide any easy
     * entry points to add the page size to the collection.
     *
     * @param null $filters
     * @param null $limit
     * @return array
     */
    public function items($filters = null, $limit = null)
    {
        if (!Mage::getStoreConfigFlag(self::CONFIG_XPATH_SYNC_ORDERS)) {
            return array();
        }

        /** @var $orderCollection Mage_Sales_Model_Mysql4_Order_Collection */
        $orderCollection = Mage::getModel("sales/order")->getCollection();
        $orderCollection->addAttributeToSelect('*');

        if ($limit) {
            $orderCollection->setPageSize($limit);
        }

        try {
            foreach ($filters as $field => $value) {
                $orderCollection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            Mage::helper('rewardsapi')->logException($e);
            throw new Mage_Api_Exception("There was a problem applying filters.  Exception has been logged.");
        }

        $orders = array();
        foreach ($orderCollection as $order) {
            $orders[] = $this->_formatAttributeArray($order);
        }

        return $orders;
    }

    /**
     * The reason I'm not leveraging the _getAttributes() method is for maximal
     * backwards compatibility with older versions of Mage.  I wanted to go with
     * as simple and fail safe an implementation as possible.
     *
     * @param Mage_Sales_Model_Order $order
     * @return float|mixed
     */
    protected function _formatAttributeArray(Mage_Sales_Model_Order $order)
    {
        $result = $order->getData();
        if (isset($result['entity_id'])) {
            $result['order_id'] = $result['entity_id'];
            unset($result['entity_id']);
        }

        return $result;
    }
}