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


class Mirasvit_Rma_Adminhtml_StatusController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction ()
    {
        $this->loadLayout()->_setActiveMenu('rma');

        return $this;
    }

    public function indexAction ()
    {
        $this->_title($this->__('Statuses'));
        $this->_initAction();
        $this->_addContent($this->getLayout()
            ->createBlock('rma/adminhtml_status'));
        $this->renderLayout();
    }

    public function addAction ()
    {
        $this->_title($this->__('New Status'));

        $this->_initStatus();

        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Status  Manager'),
                Mage::helper('adminhtml')->__('Status Manager'), $this->getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Status '), Mage::helper('adminhtml')->__('Add Status'));

        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('rma/adminhtml_status_edit'));
        $this->renderLayout();
    }

    public function editAction ()
    {
        $status = $this->_initStatus();

        if ($status->getId()) {
            $this->_title($this->__("Edit Status '%s'", $status->getName()));
            $this->_initAction();
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Statuses'),
                    Mage::helper('adminhtml')->__('Statuses'), $this->getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Status '),
                    Mage::helper('adminhtml')->__('Edit Status '));

            $this->getLayout()
                ->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('rma/adminhtml_status_edit'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The Status does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction ()
    {
        if ($data = $this->getRequest()->getPost()) {

            $status = $this->_initStatus();
            $status->addData($data);
            //format date to standart
            // $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            // Mage::helper('mstcore/date')->formatDateForSave($status, 'active_from', $format);
            // Mage::helper('mstcore/date')->formatDateForSave($status, 'active_to', $format);

            try {
                $status->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Status was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $status->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find Status to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction ()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $status = Mage::getModel('rma/status');

                $status->setId($this->getRequest()
                    ->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Status was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()
                    ->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massChangeAction()
    {
        $ids = $this->getRequest()->getParam('status_id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Status(s)'));
        } else {
            try {
                $isActive = $this->getRequest()->getParam('is_active');
                foreach ($ids as $id) {
                    $status = Mage::getModel('rma/status')->load($id);
                    $status->setIsActive($isActive);
                    $status->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully updated', count($ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('status_id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Status(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $status = Mage::getModel('rma/status')
                        ->setIsMassDelete(true)
                        ->load($id);
                    $status->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ids)
                    )
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function _initStatus()
    {
        $status = Mage::getModel('rma/status');
        if ($this->getRequest()->getParam('id')) {
            $status->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_status', $status);

        return $status;
    }

    /************************/

}