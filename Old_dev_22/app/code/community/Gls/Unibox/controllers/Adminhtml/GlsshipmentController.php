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
class Gls_Unibox_Adminhtml_GlsshipmentController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('gls');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('GLS'), Mage::helper('adminhtml')->__('Sendungen'));
        $this->_addContent($this->getLayout()->createBlock('glsbox/adminhtml_shipment'));
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('glsbox/shipment');
    }

    protected function _validateSecretKey()
    {
        return parent::_validateSecretKey();
    }
}