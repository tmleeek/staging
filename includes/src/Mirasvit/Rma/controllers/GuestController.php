<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   RMA
 * @version   1.0.1
 * @revision  135
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Rma_GuestController extends Mage_Core_Controller_Front_Action
{

    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function _initOrder()
    {
        if (($orderId = Mage::app()->getRequest()->getParam('order_increment_id')) &&
            ($email = Mage::app()->getRequest()->getParam('email'))) {
        	$orderId = trim($orderId);
        	$orderId = str_replace('#', '', $orderId);
            $collection = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('increment_id', (int)$orderId);
                ;
            if ($collection->count()) {
                $order =  $collection->getFirstItem();
                if ($email != $order->getCustomerEmail()) {
                    return false;
                }

                Mage::register('current_order', $order);
                $this->_getSession()->setRMAGuestOrderId($order->getId());
                return $order;
            }
        }
    }

    public function newAction()
    {
        $session  = $this->_getSession();
    	$customer = Mage::getSingleton('customer/session')->getCustomer();
    	if ($customer->getId()) {
    		$this->_redirect('rma/rma/new');
    		return;
    	}
        try {
	        if (Mage::app()->getRequest()->getParam('order_increment_id') && !$this->_initOrder()) {
	        	throw new Mage_Core_Exception("Wrong Order # or Email");
	        }
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    protected function _initRma()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $rma = Mage::getModel('rma/rma')->getCollection()
              ->addFieldToFilter('main_table.guest_id', $id)
              ->getFirstItem();

            if ($rma->getId() > 0) {
                Mage::register('current_rma', $rma);
                Mage::register('external_rma', true);
                return $rma;
            }
        }
    }

    public function viewAction()
    {
        if ($this->_initRma()) {
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->renderLayout();
        } else {
            $this->_forward('no_rote');
        }
    }

    public function saveAction()
    {
        $session  = $this->_getSession();
        $data = $this->getRequest()->getParams();
        $items = $data['items'];
        unset($data['items']);

        try {
            if ($session->getRMAGuestOrderId() != $data['order_id']) {
                throw new Mage_Core_Exception("Error Processing Request", 1);
            }

            $rma = Mage::helper('rma/process')->createRmaFromPost($data, $items);
            $session->addSuccess($this->__('RMA was successfuly created'));
            $this->_redirectUrl($rma->getGuestUrl());
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
            $session->setFormData($data);
            $this->_redirect('*/*/*');
        }
    }

    public function savecommentAction()
    {
        $session  = $this->_getSession();
        $customer = $session->getCustomer();
        $rmaId = $this->getRequest()->getParam('id');
        if (!$rma = $this->_initRma()) {
            throw new Mage_Core_Exception("Error Processing Request", 1);
        }
        $comment = $this->getRequest()->getParam('comment');

        try {
            $rma->addComment($comment, false, $rma->getCustomer(), false, false, true);
            $session->addSuccess($this->__('Your comment was successfuly added'));
            $this->_redirectUrl($rma->getGuestUrl());
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
            $this->_redirect('*/*/index');
        }
    }

    public function printAction()
    {
        if (!$rma = $this->_initRma()) {
            return;
        }
        if ($label = $rma->getReturnLabel()) {
            $this->_redirectUrl($label->getUrl());
            return;
        }
        $this->loadLayout('print');
        $this->renderLayout();
    }
}