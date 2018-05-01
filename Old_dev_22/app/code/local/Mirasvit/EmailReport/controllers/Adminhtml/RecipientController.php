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


class Mirasvit_EmailReport_Adminhtml_RecipientController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('email')
            ->_title(Mage::helper('email')->__('Trigger Email Suite'), Mage::helper('email')->__('Trigger Email Suite'))
            ->_title(Mage::helper('email')->__('Statistics'), Mage::helper('email')->__('Statistics'));

        return $this;
    }

    public function singleAction()
    {
        $this->_initAction();
        $this->_title($this->__('Single Recipient'));

        $this->_addContent($this->getLayout()->createBlock('emailreport/adminhtml_recipient_single'));
        $this->renderLayout();
    }

    public function trendAction()
    {
        $this->_initAction();
        $this->_title($this->__('Recipient Trend'));

        $this->_addContent($this->getLayout()->createBlock('emailreport/adminhtml_recipient_trend'));
        $this->renderLayout();
    }
}