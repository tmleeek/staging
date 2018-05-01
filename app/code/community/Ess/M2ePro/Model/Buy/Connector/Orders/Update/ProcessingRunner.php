<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Buy_Connector_Orders_Update_ProcessingRunner
    extends Ess_M2ePro_Model_Connector_Command_Pending_Processing_Runner_Single
{
    // ########################################

    protected function setLocks()
    {
        parent::setLocks();

        $params = $this->getParams();

        if (!isset($params['request_data']['items']) || !is_array($params['request_data']['items'])) {
            return;
        }

        $ordersIds = array();

        foreach ($params['request_data']['items'] as $update) {
            if (!isset($update['order_id'])) {
                throw new Ess_M2ePro_Model_Exception_Logic('Order ID is not defined.');
            }

            $ordersIds[] = (int)$update['order_id'];
        }

        /** @var Ess_M2ePro_Model_Order[] $orders */
        $orders = Mage::getModel('M2ePro/Order')
            ->getCollection()
            ->addFieldToFilter('id', array('in' => $ordersIds))
            ->getItems();

        foreach ($orders as $order) {
            $order->addProcessingLock('update_shipping_status', $this->getProcessingObject()->getId());
        }
    }

    protected function unsetLocks()
    {
        parent::unsetLocks();

        $params = $this->getParams();

        if (!isset($params['request_data']['items']) || !is_array($params['request_data']['items'])) {
            return;
        }

        $ordersIds = array();

        foreach ($params['request_data']['items'] as $update) {
            if (!isset($update['order_id'])) {
                throw new Ess_M2ePro_Model_Exception_Logic('Order ID is not defined.');
            }

            $ordersIds[] = (int)$update['order_id'];
        }

        /** @var Ess_M2ePro_Model_Order $orders */
        $orders = Mage::getModel('M2ePro/Order')
            ->getCollection()
            ->addFieldToFilter('id', array('in' => $ordersIds))
            ->getItems();

        foreach ($orders as $order) {
            $order->deleteProcessingLocks('update_shipping_status', $this->getProcessingObject()->getId());
        }
    }

    // ########################################
}