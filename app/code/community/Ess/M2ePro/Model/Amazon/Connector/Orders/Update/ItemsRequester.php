<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Amazon_Connector_Orders_Update_ItemsRequester
    extends Ess_M2ePro_Model_Amazon_Connector_Command_Pending_Requester
{
    // ########################################

    public function getCommand()
    {
        return array('orders','update','entities');
    }

    // ########################################

    public function process()
    {
        $this->eventBeforeExecuting();
        $this->getProcessingRunner()->start();
    }

    // ########################################

    protected function getProcessingRunnerModelName()
    {
        return 'Amazon_Connector_Orders_Update_ProcessingRunner';
    }

    protected function getProcessingParams()
    {
        return array_merge(
            parent::getProcessingParams(),
            array(
                'request_data' => $this->getRequestData(),
                'order_id'     => $this->params['order']['order_id'],
                'change_id'    => $this->params['order']['change_id'],
                'start_date'   => Mage::helper('M2ePro')->getCurrentGmtDate(),
            )
        );
    }

    // ########################################

    protected function getRequestData()
    {
        $fulfillmentDate = new DateTime($this->params['order']['fulfillment_date'], new DateTimeZone('UTC'));

        $order = array(
            'id'               => $this->params['order']['change_id'],
            'order_id'         => $this->params['order']['amazon_order_id'],
            'tracking_number'  => $this->params['order']['tracking_number'],
            'carrier_name'     => $this->params['order']['carrier_name'],
            'fulfillment_date' => $fulfillmentDate->format('c'),
            'shipping_method'  => isset($orderUpdate['shipping_method']) ? $orderUpdate['shipping_method'] : null,
            'items'            => array()
        );

        if (isset($this->params['order']['items']) && is_array($this->params['order']['items'])) {
            foreach ($this->params['order']['items'] as $item) {
                $order['items'][] = array(
                    'item_code' => $item['amazon_order_item_id'],
                    'qty'       => (int)$item['qty']
                );
            }
        }

        return $order;
    }

    // ########################################
}