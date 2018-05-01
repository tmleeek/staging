<?php

class MDN_ProductReturn_GuestController extends Mage_Core_Controller_Front_Action {

    /**
     * Display form to create the customer account
     */
    public function FormAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Create customer account
     */
    public function SubmitAction() {
        $orderId = $this->getRequest()->getParam('order_id');
        try {
            //create and log the customer
            $customer = Mage::getModel('customer/customer')->setId(null);
            $post = $this->getRequest()->getPost();
            foreach ($post as $k => $v)
                $customer->setData($k, $v);
            $customer->getGroupId();
            $customer->save();
            Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);

            //assign order to customer
            $order = Mage::getModel('sales/order')->load($orderId);
            Mage::getSingleton('ProductReturn/Guest')->associateOrderToCustomer($customer, $order);

            //redirect in new RMA request form
            Mage::getSingleton('customer/session')->addSuccess($this->__('Your account has been created'));
            $this->_redirect('ProductReturn/Front/NewRequest', array('order_id' => $orderId));
            
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError($this->__($ex->getMessage()));
            $this->_redirect('ProductReturn/Guest/Form', array('order_id' => $orderId));
        }
        
    }

}
