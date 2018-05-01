<?php

/**
 *
 *
 */
class TBT_Rewardssocial_Block_Twitter_Tweet_Button extends TBT_Rewardssocial_Block_Widget_Abstract
    implements TBT_Rewardssocial_Block_Twitter_Tweet_Button_Interface
{
    protected $_predictedPoints = null;

    public function _prepareLayout()
    {
        $session = Mage::getSingleton('customer/session');
        $this->customerId = $session->getCustomerId();
        $this->storeTwitterUsername = Mage::getStoreConfig('rewards/twitter/storeTwitterUsername');

        if (!Mage::helper('rewardssocial/twitter_config')->isTweetingEnabled()) {
            $this->setIsHidden(true);
        }

        return parent::_prepareLayout();
    }

    public function getHasTweeted()
    {
        $customer = $this->_getRS()->getSessionCustomer();
        if (!$customer->getId()) {
            return false;
        }

        $url = Mage::helper('core/url')->getCurrentUrl();
        return $this->_getValidator()->hasTweeted($customer->getId(), $url);
    }

    public function getNotificationBlock()
    {
        return $this->getLayout()->createBlock('core/template')
            ->setTemplate('rewardssocial/twitter/tweet/points.phtml')
            ->setPredictedPointsString($this->getPredictedPointsString());
    }

    public function getPredictedPointsString()
    {
        $predictedPoints = $this->getPredictedPoints();
        return (string) Mage::getModel('rewards/points')->set($predictedPoints);
    }

    public function getHasPredictedPoints()
    {
        $predictedPoints = $this->getPredictedPoints();
        // TODO: should check other things too, like limits
        return !empty($predictedPoints) && !$this->getHasTweeted();
    }

    public function getPredictedPoints()
    {
        if ($this->_predictedPoints === null ) {
            $this->_predictedPoints = $this->_getValidator()->getPredictedTwitterTweetPoints();
        }

        return $this->_predictedPoints;
    }

    public function getTweetedUrl()
    {
        return null;
    }

    public function isCounterEnabled()
    {
        $countEnabled = Mage::helper('rewardssocial/twitter_config')->isTweetCountEnabled();
        return $countEnabled;
    }

    public function getTweet()
    {
        return Mage::helper('rewardssocial/twitter_config')->getTweetedMessage();
    }

    /**
     * If the is_hidden attribute is set, dont output anything.
     *
     * (overrides parent method)
     */
    protected function _toHtml()
    {
        return parent::_toHtml();
    }

    protected function _getValidator()
    {
        return Mage::getSingleton('rewardssocial/twitter_tweet_validator');
    }
}
