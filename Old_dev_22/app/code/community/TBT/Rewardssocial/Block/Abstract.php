<?php

class TBT_Rewardssocial_Block_Abstract extends Mage_Core_Block_Template
{

    protected $_predictedPoints = null;

    public function getTextWithLoginLinks($text)
    {
        $login_url = $this->getUrl('customer/account/login') ;
        $text = Mage::helper('rewardssocial')->getTextWithLinks($text, 'login_link', $login_url);

        return $text;
    }

    /**
     * @return boolean
     */
    public function getIsCustomerLoggedIn()
    {
        return $this->_getRS()->isCustomerLoggedIn();
    }

    public function getCurrentPageURI()
    {
        return $this->getRequest()->getRequestUri();
    }

    public function getCustomer()
    {
        return $this->_getRS()->getCustomer();
    }

    /**
     * Encrypts a page that can be 'liked'
     *
     * @return string
     */
    public function getPageKey()
    {
        $page_url = $this->getCurrentPageURI();;
        $page_url_encr = Mage::helper('rewardssocial/crypt')->encrypt($page_url);

        return $page_url_encr;
    }

    /**
     * If the is_hidden attribute is set, dont output anything.
     *
     * (overrides parent method)
     */
    protected function _toHtml()
    {
        // if ($this->getIsHidden()) {
        //     return "";
        // }

        return parent::_toHtml();
    }

    /**
     * @return TBT_Rewards_Model_Session
     */
    protected function _getRS()
    {
        return Mage::getSingleton('rewards/session');
    }
}
