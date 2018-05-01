<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Buy_Connector_Orders_Update_ShippingRequester
    extends Ess_M2ePro_Model_Buy_Connector_Command_Pending_Requester
{
    // ########################################

    protected function getProcessingRunnerModelName()
    {
        return 'Buy_Connector_Orders_Update_ProcessingRunner';
    }

    protected function getProcessingParams()
    {
        return array_merge(
            parent::getProcessingParams(),
            array(
                'request_data' => $this->getRequestData()
            )
        );
    }

    // ########################################

    public function getCommand()
    {
        return array('orders','update','confirmation');
    }

    // ########################################

    protected function getResponserParams()
    {
        $params = array();

        foreach ($this->params['items'] as $updateData) {
            $params[$updateData['buy_order_item_id']] = array(
                'order_id'        => $updateData['order_id'],
                'order_item_id'   => $updateData['buy_order_item_id'],
                'tracking_type'   => $updateData['tracking_type'],
                'tracking_number' => $updateData['tracking_number']
            );
        }

        return $params;
    }

    // ########################################

    public function eventBeforeExecuting()
    {
        parent::eventBeforeExecuting();

        // collect ids of processed order changes
        //------------------------------
        $changeIds = array();

        foreach ($this->params['items'] as $updateData) {
            if (!is_array($updateData)) {
                continue;
            }

            $changeIds[] = $updateData['change_id'];
        }
        //------------------------------

        Mage::getResourceModel('M2ePro/Order_Change')->deleteByIds($changeIds);
    }

    // ########################################

    protected function getRequestData()
    {
        $items = array();

        foreach ($this->params['items'] as $updateData) {
            $items[] = array(
                'order_id'        => $updateData['buy_order_id'],
                'order_item_id'   => $updateData['buy_order_item_id'],
                'qty'             => $updateData['qty'],
                'tracking_type'   => $updateData['tracking_type'],
                'tracking_number' => $updateData['tracking_number'],
                'ship_date'       => $updateData['ship_date'],
            );
        }

        return array('items' => $items);
    }

    // ########################################
}