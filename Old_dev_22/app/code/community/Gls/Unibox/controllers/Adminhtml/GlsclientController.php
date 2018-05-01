<?php
/**
 * Gls_Unibox extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Gls
 * @package    Gls_Unibox
 * @copyright  Copyright (c) 2013 webvisum GmbH
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Webvisum
 * @package    Gls_Unibox
 */
class Gls_Unibox_Adminhtml_GlsclientController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('gls');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('GLS'), Mage::helper('adminhtml')->__('Mandanten'));
        $this->_addContent($this->getLayout()->createBlock('glsbox/adminhtml_client'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('gls');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('GLS'), Mage::helper('adminhtml')->__('Mandanten bearbeiten'));
        $this->_addContent($this->getLayout()->createBlock('glsbox/adminhtml_client_edit'))->_addLeft($this->getLayout()->createBlock('glsbox/adminhtml_client_edit_tabs'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->editAction();
    }

    public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            try {
                $model = Mage::getModel('glsbox/client')
                    //->addData($this->getRequest()->getParams())
                    ->setId($this->getRequest()->getParam('id'))
					->setKundennummer($this->getRequest()->getParam('kundennummer'))
					->setCustomerid($this->getRequest()->getParam('customerid'))
					->setContactid($this->getRequest()->getParam('contactid'))
					->setDepotcode($this->getRequest()->getParam('depotcode'))
					->setDepotnummer($this->getRequest()->getParam('depotnummer'))  
                    ->setDisplayName($this->getRequest()->getParam('display_name'))
                    ->setNotes($this->getRequest()->getParam('notes'))
                    ->setStatus($this->getRequest()->getParam('status'))
                    ->setNumcircleStandardStart($this->getRequest()->getParam('numcircle_standard_start'))
                    ->setNumcircleStandardEnd($this->getRequest()->getParam('numcircle_standard_end'))
                    ->setNumcircleExpressStart($this->getRequest()->getParam('numcircle_express_start'))
                	->setNumcircleExpressEnd($this->getRequest()->getParam('numcircle_express_end'));
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Mandant wurde gespeichert'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('glsbox/client');
                /* @var $model Mage_Rating_Model_Rating */
                $model->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Mandant wurde gelöscht'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('glsbox/client');
    }

    protected function _validateSecretKey()
    {
        return parent::_validateSecretKey();
    }
}