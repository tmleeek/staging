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


class Mirasvit_Rma_RmaController extends Mage_Core_Controller_Front_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        if ($action != 'external' && $action != 'postexternal') {
            if (!Mage::getSingleton('customer/session')->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    protected function _initRma()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $customer = $this->_getSession()->getCustomer();
            $rma = Mage::getModel('rma/rma')->load($id);
            if ($rma->getId() > 0 && $rma->getCustomerId() == $customer->getId()) {
                Mage::register('current_rma', $rma);
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

    public function newAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function saveAction()
    {
        $session  = $this->_getSession();
        $customer = $session->getCustomer();
        $data = $this->getRequest()->getParams();

        $items = $data['items'];
        unset($data['items']);

        try {
            $rma = Mage::helper('rma/process')->createRmaFromPost($data, $items, $customer);
            $session->addSuccess($this->__('RMA was successfuly created'));
            $this->_redirect('*/*/view', array('id' => $rma->getId()));
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
            $session->setFormData($data);
            if ($this->getRequest()->getParam('id')) {
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } else {
                $this->_redirect('*/*/add', array('order_id' => $this->getRequest()->getParam('order_id')));
            }
        }
    }

    public function savecommentAction()
    {
        $session  = $this->_getSession();
        $customer = $session->getCustomer();
        $rmaId = $this->getRequest()->getParam('id');
        if (!$rma = $this->_initRma()) {
            $this->_redirect('*/*/index');
            return;
        }
        $comment = $this->getRequest()->getParam('comment');

        try {
            $rma->addComment($comment, false, $customer, false, false, true);
            $session->addSuccess($this->__('Your comment was successfuly added'));
            $this->_redirect('*/*/view', array('id' => $rma->getId()));
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
            $this->_redirectUrl($rma->getUrl());
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