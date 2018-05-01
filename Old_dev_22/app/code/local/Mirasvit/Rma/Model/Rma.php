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


class Mirasvit_Rma_Model_Rma extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('rma/rma');
    }

    public function toOptionArray($emptyOption = false)
    {
    	return $this->getCollection()->toOptionArray($emptyOption);
    }

	protected $_itemCollection;
	public function getItemCollection()
	{
		if (!$this->_itemCollection) {
			$this->_itemCollection = Mage::getModel('rma/item')->getCollection()
				->addFieldToFilter('rma_id', $this->getRmaId());
		}
		return $this->_itemCollection;
	}

	protected $_commentCollection;
	public function getCommentCollection()
	{
		if (!$this->_commentCollection) {
			$this->_commentCollection = Mage::getModel('rma/comment')->getCollection()
				->addFieldToFilter('rma_id', $this->getRmaId());
		}
		return $this->_commentCollection;
	}

    protected $_order = null;
    public function getOrder()
    {
        if (!$this->getOrderId()) {
            return false;
        }
    	if ($this->_order === null) {
            $this->_order = Mage::getModel('sales/order')->load($this->getOrderId());
    	}
    	return $this->_order;
    }

    protected $_store = null;
    public function getStore()
    {
        if (!$this->getStoreId()) {
            return false;
        }
    	if ($this->_store === null) {
            $this->_store = Mage::getModel('core/store')->load($this->getStoreId());
    	}
    	return $this->_store;
    }

    protected $_customer = null;
    public function getCustomer()
    {
    	if ($this->_customer === null) {
            if ($this->getCustomerId()) {
                $this->_customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
            } elseif ($this->getFirstname()) {
                $this->_customer = new Varien_Object(array(
                    'firstname' => $this->getFirstname(),
                    'lastname' => $this->getLastname(),
                    'name' => $this->getFirstname().' '.$this->getLastname(),
                    'email' => $this->getEmail(),
                ));
            } else {
                $this->_customer = false;
            }
    	}
    	return $this->_customer;
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


	/************************/

    public function getUrl()
    {
        return Mage::getUrl('rma/rma/view', array('id' => $this->getId()));
    }

    public function getGuestUrl() {
        $url = Mage::getUrl('rma/guest/view', array('id' => $this->getGuestId(), '_store' =>$this->getStoreId()));
        return $url;
    }

    public function getPrintUrl() {
        $url = Mage::getUrl('rma/rma/print', array('id' => $this->getGuestId(), '_store' =>$this->getStoreId()));
        return $url;
    }

    public function getGuestPrintUrl() {
        $url = Mage::getUrl('rma/guest/print', array('id' => $this->getGuestId(), '_store' =>$this->getStoreId()));
        return $url;
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->getGuestId()) {
            $this->setGuestId(md5($this->getId().Mage::helper('rma/string')->generateRandString(10)));
        }

        $config = Mage::getSingleton('rma/config');
        if (!$this->getStatusId()) {
            $this->setStatusId($config->getGeneralDefaultStatus());
        }

        if (!$this->getIsResolved()) {
            $status = $this->getStatus();
            if ($status->getIsRmaResolved()) {
                $this->setIsResolved(true);
            }
        }
    }

    protected function _afterSaveCommit()
    {
        parent::_afterSaveCommit();
        if (!$this->getIncrementId()) {
            $this->setIncrementId(Mage::helper('rma')->generateIncrementId($this));
            $this->save();
        }
    }

    public function getShippingAddressHtml()
    {
        $items = array();
        $items[] = $this->getFirstname().' '.$this->getLastname();
        if ($this->getEmail()) {
            $items[] = $this->getEmail();
        }
        if ($this->getTelephone()) {
            $items[] = $this->getTelephone();
        }
        if ($this->getCompany()) {
            $items[] = $this->getCompany();
        }
        return implode('<br>', $items);
    }

    public function getReturnAddress()
    {
        return Mage::getSingleton('rma/config')->getGeneralReturnAddress($this->getStoreId());
    }

    public function getReturnAddressHtml()
    {
        return Mage::helper('rma')->convertToHtml($this->getReturnAddress());
    }

    public function addComment($text, $isHtml, $customer, $user, $isNotify, $isVisible, $isNotifyAdmin = true)
    {
        if (trim($text) == '' && !Mage::helper('mstcore/attachment')->hasAttachments()) {
            throw new Mage_Core_Exception(Mage::helper('rma')->__('Please, post not empty message'));
        }
        $comment = Mage::getModel('rma/comment')
            ->setRmaId($this->getId());

        if ($isHtml) {
            $comment->setTextHtml($text);
        } else {
            $text = strip_tags($text);
            $comment->setText($text);
        }

        if ($customer) {
            $comment->setCustomerId($customer->getId())
                    ->setCustomerName($customer->getName());
            $message = Mage::helper('rma')->__('Customer has added a comment: <br> <p>%s</p>', Mage::helper('rma')->convertToHtml($text));
            if ($isNotifyAdmin) {
                Mage::helper('rma/mail')->sendNotificationAdminEmail($this, $message);
            }
        } elseif ($user) {
            $comment->setUserId($user->getId());
            if ($isNotify) {
                Mage::helper('rma/mail')->sendNotificationCustomerEmail($this, "<p>{$text}</p>");
            }
        }
        $comment->setIsVisibleInFrontend($isVisible)
            ->setIsCustomerNotified($isNotify);
        $comment->save();
        if ($customer) {
            Mage::helper('mstcore/attachment')->saveAttachments('COMMENT', $comment->getId());
        }
        $this->save();
        return $comment;
    }

    public function initFromOrder($orderId)
    {
        $this->setOrderId($orderId);
        $order = $this->getOrder();

        $this->setCustomerId($order->getCustomerId());
        if ($customer = $this->getCustomer()) {
            $data = $customer->getData();
            unset($data['increment_id']);
            $this->addData($data);
        }

        $address = $order->getShippingAddress();
        $data = $address->getData();
        unset($data['increment_id']);
        $this->addData($data);
        return $this;
    }

    public function getName()
    {
        return $this->getFirstname().' '.$this->getLastname();
    }

    public function getReturnLabel()
    {
        return Mage::helper('mstcore/attachment')->getAttachment('rma_return_label', $this->getId());
    }
}