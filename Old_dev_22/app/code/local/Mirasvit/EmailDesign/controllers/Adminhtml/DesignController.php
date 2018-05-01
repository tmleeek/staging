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


class Mirasvit_EmailDesign_Adminhtml_DesignController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('email')
            ->_title(__('Trigger Email Suite'), __('Trigger Email Suite'))
            ->_title(__('Manage Designs'), __('Manage Designs'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();

        $this->_addContent($this->getLayout()->createBlock('emaildesign/adminhtml_design'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $model = $this->getModel();

        $this->_initAction();
        $this->_title($this->__('New Design'));


        $this->_addContent($this->getLayout()->createBlock('emaildesign/adminhtml_design_edit'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $model = $this->getModel();

        if ($model->getId()) {
            $this->_initAction();

            $this->_title($model->getTitle());

            $this->_addContent($this->getLayout()->createBlock('emaildesign/adminhtml_design_edit'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('emaildesign')->__('The design does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = $this->getModel();
            $model->addData($data);

            try {
                $model->save();

                if ($this->getRequest()->getParam('isAjax')) {
                    $this->getResponse()->setHeader('Content-type', 'application/json');
                    $jsonData = Mage::helper('core')->jsonEncode(array(
                        'success' => true,
                        'message' => Mage::helper('emaildesign')->__('Design was successfully saved'))
                    );
                    $this->getResponse()->setBody($jsonData);
                    return;
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('emaildesign')->__('Design was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

                return;
            }
        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('emaildesign')->__('Unable to find design to save'));
        $this->_redirect('*/*/');
    }

    public function importMailchimpAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('emaildesign/adminhtml_design_mailchimp_import'));
        $this->renderLayout();
    }

    public function doimportMailchimpAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            foreach ($data['design'] as $design) {
                $model = Mage::getModel('emaildesign/design')->importMailchimp($design);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('emaildesign')->__('Design %s imported', $model->getTitle()));
            }
        }

        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        try {
            $model = $this->getModel();
            $model->delete();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

            return;
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('emaildesign')->__('Design was successfully deleted'));
        $this->_redirect('*/*/');
    }

    public function exportAction()
    {
        try {
            $path = $this->getModel()->export();

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('emaildesign')->__('Design exported to %s', $path));
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/');
        }
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

        $this->getResponse()->setBody($model->getPreviewContent());
    }

    public function getModel()
    {
        $model = Mage::getModel('emaildesign/design');

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_model', $model);

        return $model;
    }
}