<?php

class TBT_Rewardssocial_Block_Pinterest_Pin_Modal extends Mage_Core_Block_Template
{
    protected $_customer = null;

    public function getCustomerPinterestUsername()
    {
        return $this->getCustomer()->getPinterestUsername();
    }

    public function getCustomer()
    {
        if ($this->_customer === null) {
            $customerId = $this->_getSession()->getCustomer()->getId();
            $this->_customer = Mage::getModel('rewardssocial/customer')->load($customerId);
            $this->_customer->setId($customerId);
        }

        return $this->_customer;
    }

    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    protected function _toHtml()
    {
        // if rule to reward doesn't exist, no need to load the modal
        if (Mage::helper('rewardssocial/pinterest_pin')->getRuleExists()) {
            return parent::_toHtml();
        }

        return '';
    }
}