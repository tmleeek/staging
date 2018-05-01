<?php

class MDN_Orderpreparation_OrderController extends Mage_Adminhtml_Controller_Action {

    public function DispatchAction() {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);

        $result = Mage::helper('Orderpreparation/Dispatcher')->DispatchOrder($order);

        //confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Order dispatched'));
        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
    }

}
