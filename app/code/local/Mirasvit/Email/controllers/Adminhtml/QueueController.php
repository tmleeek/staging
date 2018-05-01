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
 * @package   Trigger Email Suite
 * @version   1.0.1
 * @revision  168
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Email_Adminhtml_QueueController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('email')
            ->_title(Mage::helper('email')->__('Trigger Email Suite'), Mage::helper('email')->__('Trigger Email Suite'))
            ->_title(Mage::helper('email')->__('Mail Log'), Mage::helper('email')->__('Mail Log'));

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Mail Log'));
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('email/adminhtml_queue'));
        $this->renderLayout();
    }

    public function previewAction()
    {
        $this->loadLayout();

        $model = $this->getModel();

        $this->renderLayout();
    }

    public function dropAction()
    {
        $model = $this->getModel();

        $this->getResponse()->setBody($model->getContent());
    }

    public function sendAction()
    {
        $this->getModel()->send();

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('The mail was sent.'));
        
        $this->_redirect('*/*/');
    }

    public function cancelAction()
    {
        $this->getModel()->cancel();

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('The mail was canceled.'));

        $this->_redirect('*/*/');
    }

    public function resetAction()
    {
        $this->getModel()->reset();

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('The mail was reseted.'));
        
        $this->_redirect('*/*/');
    }

    public function massSendAction()
    {
        if (is_array($this->getRequest()->getParam('queue'))) {
            foreach ($this->getRequest()->getParam('queue') as $queueId) {
                $model = Mage::getModel('email/queue')->load($queueId);
                $model->send();
            }
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('The mails was sent.'));

        $this->_redirect('*/*/');
    }

    public function massCancelAction()
    {
        if (is_array($this->getRequest()->getParam('queue'))) {
            foreach ($this->getRequest()->getParam('queue') as $queueId) {
                $model = Mage::getModel('email/queue')->load($queueId);
                $model->cancel();
            }
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('The mails was canceled.'));

        $this->_redirect('*/*/');
    }

    public function massStatusAction()
    {
        $status = $this->getRequest()->getParam('status');

        if (is_array($this->getRequest()->getParam('queue'))) {
            foreach ($this->getRequest()->getParam('queue') as $queueId) {
                $model = Mage::getModel('email/queue')->load($queueId);
                $model->setStatus($status)
                    ->save();
            }
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('The mails was chaged status.'));

        $this->_redirect('*/*/');
    }

    public function getModel()
    {
        $model = Mage::getModel('email/queue');

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_model', $model);

        return $model;
    }
}