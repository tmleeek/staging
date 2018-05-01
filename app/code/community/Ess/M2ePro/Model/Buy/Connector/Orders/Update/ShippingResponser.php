<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Buy_Connector_Orders_Update_ShippingResponser
    extends Ess_M2ePro_Model_Buy_Connector_Command_Pending_Responser
{
    // M2ePro_TRANSLATIONS
    // Rakuten.com Order status was not updated. Reason: %msg%
    // M2ePro_TRANSLATIONS
    // Rakuten.com Order status was updated to Shipped.
    // M2ePro_TRANSLATIONS
    // Tracking number "%num%" for "%code%" has been sent to Rakuten.com.

    /** @var Ess_M2ePro_Model_Order[] $orders */
    private $orders = NULL;

    // ########################################

    public function __construct(array $params, Ess_M2ePro_Model_Connector_Connection_Response $response)
    {
        parent::__construct($params, $response);

        $ordersIds = array();

        foreach ($this->params as $update) {
            if (!isset($update['order_id'])) {
                throw new Ess_M2ePro_Model_Exception_Logic('Order ID is not defined.');
            }

            $ordersIds[] = (int)$update['order_id'];
        }

        $this->orders = Mage::getModel('M2ePro/Order')
            ->getCollection()
            ->addFieldToFilter('component_mode', Ess_M2ePro_Helper_Component_Buy::NICK)
            ->addFieldToFilter('id', array('in' => $ordersIds))
            ->getItems();
    }

    // ########################################

    public function failDetected($messageText)
    {
        parent::failDetected($messageText);

        foreach ($this->orders as $order) {
            $order->getLog()->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_EXTENSION);
            $order->addErrorLog(
                'Rakuten.com Order status was not updated. Reason: %msg%', array('msg' => $messageText)
            );
        }
    }

    // ########################################

    protected function validateResponse()
    {
        return true;
    }

    protected function processResponseData()
    {
        $responseData = $this->getResponse()->getData();

        // Check global messages
        //----------------------
        $globalMessages = $this->getResponse()->getMessages()->getEntities();
        if (isset($responseData['messages']['0-id']) && is_array($responseData['messages']['0-id'])) {

            foreach ($responseData['messages']['0-id'] as $messageData) {
                $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
                $message->initFromResponseData($messageData);

                $globalMessages[] = $message;
            }
        }

        if (count($globalMessages) > 0) {
            foreach ($this->orders as $order) {
                foreach ($globalMessages as $message) {
                    $order->getLog()->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_EXTENSION);
                    $order->addErrorLog($message->getText());
                }
            }

            return;
        }
        //----------------------

        // Check separate messages
        //----------------------
        $failedOrdersIds = array();

        foreach ($responseData['messages'] as $orderItemId => $messages) {
            $orderItemId = (int)$orderItemId;

            if ($orderItemId <= 0) {
                continue;
            }

            $orderId = $this->getOrderIdByOrderItemId($orderItemId);

            if (!is_numeric($orderId)) {
                continue;
            }

            /** @var Ess_M2ePro_Model_Connector_Connection_Response_Message_Set $messagesSet */
            $messagesSet = Mage::getModel('M2ePro/Connector_Connection_Response_Message_Set');
            $messagesSet->init($messages);

            $failedOrdersIds[] = $orderId;

            foreach ($messagesSet->getEntities() as $message) {
                $this->orders[$orderId]->getLog()->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_EXTENSION);
                $this->orders[$orderId]->addErrorLog($message->getText());
            }
        }
        //----------------------

        //----------------------
        foreach ($this->orders as $order) {

            if (in_array($order->getId(), $failedOrdersIds)) {
                continue;
            }

            $order->getLog()->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_EXTENSION);
            $order->addSuccessLog('Rakuten.com Order status was updated to Shipped.');

            $order->addSuccessLog('Tracking number has been sent to Rakuten.com.',
                array(
                    '!num' => $this->params[$order->getId()]['tracking_number'],
                    'code' => $this->params[$order->getId()]['tracking_type']
                )
            );
        }
        //----------------------
    }

    // ########################################

    private function getOrderIdByOrderItemId($orderItemId)
    {
        foreach ($this->params as $requestOrderItemId => $requestData) {
            if ($orderItemId == $requestOrderItemId && isset($requestData['order_id'])) {
                return $requestData['order_id'];
            }
        }

        return null;
    }

    // ########################################
}