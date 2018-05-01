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


class Mirasvit_RMA_Adminhtml_RMAController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction ()
    {
        $this->loadLayout()->_setActiveMenu('rma');

        return $this;
    }

    public function indexAction ()
    {
        $this->_title($this->__('RMA'));
        $this->_initAction();
        $this->_addContent($this->getLayout()
            ->createBlock('rma/adminhtml_rma'));
        $this->renderLayout();
    }

    public function addAction ()
    {
        $this->_title($this->__('New RMA'));

        $rma = $this->_initRma();
        $orderId = $this->getRequest()->getParam('order_id');

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $rma->setData($data);
        }

        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('RMA  Manager'),
                Mage::helper('adminhtml')->__('RMA Manager'), $this->getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add RMA '), Mage::helper('adminhtml')->__('Add RMA'));

        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true);

        if ($orderId) {
            $rma->initFromOrder($orderId);
            $this->_addContent($this->getLayout()->createBlock('rma/adminhtml_rma_edit'));
        } else {
            $this->_addContent($this->getLayout()->getBlock('rma_adminhtml_rma_create'));
        }
        $this->renderLayout();
    }

    public function editAction ()
    {
        $rma = $this->_initRma();

        if ($rma->getId()) {
            $this->_title($this->__("RMA #%s", $rma->getIncrementId()));
            $this->_initAction();
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('RMA'),
                    Mage::helper('adminhtml')->__('RMA'), $this->getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit RMA '),
                    Mage::helper('adminhtml')->__('Edit RMA '));

            $this->getLayout()
                ->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('rma/adminhtml_rma_edit'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The rma does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction ()
    {
        if ($data = $this->getRequest()->getPost()) {
            $items = $data['items'];
            unset($data['items']);
            unset($data['comment']);
            try {
                $isEmpty = true;
                foreach ($items as $item) {
                    if ((int)$item['qty_requested'] > 0) {
                        $isEmpty = false;
                        break;
                    }
                }
                if ($isEmpty) {
                    throw new Mage_Core_Exception("Please, add order items to the RMA (set 'Qty to Return')");
                }

                $rma = Mage::helper('rma/process')->createOrUpdateRmaFromPost($data, $items);

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('RMA was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $rma->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                if ($this->getRequest()->getParam('id')) {
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                } else {
                    $this->_redirect('*/*/add', array('order_id' => $this->getRequest()->getParam('order_id')));
                }
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find rma to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction ()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $rma = Mage::getModel('rma/rma');

                $rma->setId($this->getRequest()
                    ->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('RMA was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()
                    ->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('rma_id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select rma(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $rma = Mage::getModel('rma/rma')
                        ->setIsMassDelete(true)
                        ->load($id);
                    $rma->delete();
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

    public function _initRma()
    {
        $rma = Mage::getModel('rma/rma');
        if ($this->getRequest()->getParam('id')) {
            $rma->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_rma', $rma);

        return $rma;
    }

    /************************/


    public function addCommentAction()
    {
        try {
            $this->_initRma();

            $data = $this->getRequest()->getPost('comment');
            $isNotify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
            $isVisible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

            $rma = Mage::registry('current_rma');
            if (!$rma) {
                Mage::throwException(Mage::helper('rma')->__('Invalid RMA.'));
            }

            $comment = trim($data['comment']);
            if (!$comment) {
                Mage::throwException(Mage::helper('rma')->__('Enter valid message.'));
            }
            $user = Mage::getSingleton('admin/session')->getUser();
            $rma->addComment($comment, false, false, $user, $isNotify, $isVisible);

            $historyBlock = $this->getLayout()->createBlock('rma/adminhtml_rma_edit_form_history', 'rma_history');
            $response = $historyBlock->toHtml();
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        }
        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

}