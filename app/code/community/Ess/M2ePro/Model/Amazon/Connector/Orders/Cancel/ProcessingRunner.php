<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Amazon_Connector_Orders_Cancel_ProcessingRunner
    extends Ess_M2ePro_Model_Connector_Command_Pending_Processing_Runner_Single
{
    // ########################################

    protected function eventBefore()
    {
        $params = $this->getParams();

        /** @var Ess_M2ePro_Model_Amazon_Processing_Action $processingAction */
        $processingAction = Mage::getModel('M2ePro/Amazon_Processing_Action');
        $processingAction->setData(array(
            'account_id'    => $params['account_id'],
            'processing_id' => $this->getProcessingObject()->getId(),
            'related_id'    => $params['change_id'],
            'type'          => Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_ORDER_CANCEL,
            'request_data'  => Mage::helper('M2ePro')->jsonEncode($params['request_data']),
            'start_date'    => $params['start_date'],
        ));
        $processingAction->save();
    }

    protected function setLocks()
    {
        parent::setLocks();

        $params = $this->getParams();

        /** @var Ess_M2ePro_Model_Order $order */
        $order = Mage::helper('M2ePro/Component_Amazon')->getObject('Order', $params['order_id']);
        $order->addProcessingLock('cancel_order', $this->getProcessingObject()->getId());
    }

    protected function unsetLocks()
    {
        parent::unsetLocks();

        $params = $this->getParams();

        /** @var Ess_M2ePro_Model_Order $order */
        $order = Mage::helper('M2ePro/Component_Amazon')->getObject('Order', $params['order_id']);
        $order->deleteProcessingLocks('cancel_order', $this->getProcessingObject()->getId());
    }

    // ########################################
}