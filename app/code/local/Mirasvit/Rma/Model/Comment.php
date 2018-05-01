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


class Mirasvit_Rma_Model_Comment extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('rma/comment');
    }

    public function toOptionArray($emptyOption = false)
    {
    	return $this->getCollection()->toOptionArray($emptyOption);
    }

    protected $_status = null;
    public function getStatus()
    {
        if (!$this->getStatusId()) {
            return false;
        }
    	if ($this->_status === null) {
            $this->_status = Mage::getModel('rma/status')->load($this->getStatusId());
    	}
    	return $this->_status;
    }

    protected $_user = null;
    public function getUser()
    {
        if (!$this->getUserId()) {
            return false;
        }
    	if ($this->_user === null) {
            $this->_user = Mage::getModel('admin/user')->load($this->getUserId());
    	}
    	return $this->_user;
    }

    protected $_customer = null;
    public function getCustomer()
    {
        if (!$this->getCustomerId()) {
            return false;
        }
    	if ($this->_customer === null) {
            $this->_customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
    	}
    	return $this->_customer;
    }


	/************************/

    public function setTextHtml($html)
    {
        $this->setIsHtml(true);
        $this->setText($html);
        return $this;
    }

    public function getTextHtml()
    {
        if ($this->getIsHtml()) {
            return $this->getText();
        } else {
            return Mage::helper('rma')->convertToHtml($this->getText());
        }
    }

    public function getAttachments() {
        return Mage::helper('mstcore/attachment')->getAttachments('COMMENT', $this->getId());
    }
}