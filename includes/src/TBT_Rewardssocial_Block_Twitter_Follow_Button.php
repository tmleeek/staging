<?php

class TBT_Rewardssocial_Block_Twitter_Follow_Button extends TBT_Rewardssocial_Block_Widget_Abstract
{
    protected $_predictedPoints = null;

    public function _prepareLayout()
    {
        $session = Mage::getSingleton('customer/session');
        $this->customerId = $session->getCustomerId();
        $this->storeTwitterUsername = Mage::getStoreConfig('rewards/twitter/storeTwitterUsername');

        if (!Mage::helper('rewardssocial/twitter_follow')->isFollowingEnabled()) {
            $this->setIsHidden(true);
        }

        return parent::_prepareLayout();
    }

    public function getHasFollowed()
    {
        $customer = $this->_getRS()->getSessionCustomer();
        if (!$customer->getId()) {
            return false;
        }

        $url = Mage::helper('core/url')->getCurrentUrl();

        return $this->_getValidator()->hasFollowed($customer->getId());
    }

    public function getNotificationBlock()
    {
        return $this->getLayout()->createBlock('core/template')
            ->setTemplate('rewardssocial/twitter/follow/points.phtml')
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
        return !empty($predictedPoints) && !$this->getHasFollowed();
    }

    public function getPredictedPoints()
    {
        if ($this->_predictedPoints === null ) {
            $this->_predictedPoints = $this->_getValidator()->getPredictedTwitterFollowPoints();
        }

        return $this->_predictedPoints;
    }

    /**
     * Checks wheter config option to show count next to Twitter Follow button is enabled
     *
     * @return boolean true if option 'Followers Count Display' is set to 'Show', false otherwise
     */
    public function isCounterEnabled()
    {
        $countEnabled = Mage::helper('rewardssocial/twitter_follow')->isFollowCountEnabled();
        return $countEnabled;
    }

    public function getCustomCss()
    {
        if ($this->isCounterEnabled() && !$this->isUsernameDisplayEnabled()) {
            return " rewardssocial-{$this->getWidgetKey()}-counter";
        }

        return '';
    }

    /**
     * Checks wheter config option to display username on Twitter Follow button is enabled
     *
     * @return boolean true if option 'Username On Follow Button' is set to 'Show', false otherwise
     */
    public function isUsernameDisplayEnabled()
    {
        $usernameEnabled = Mage::getStoreConfig('rewards/twitter/showUsername');
        return $usernameEnabled;
    }

    public function getFollowProcessingUrl()
    {
        return $this->getUrl('rewardssocial/index/processFollow');
    }

    protected function _getValidator()
    {
        return Mage::getSingleton('rewardssocial/twitter_follow_validator');
    }

    public function isFollowingEnabled()
    {
        return Mage::helper('rewardssocial/twitter_follow')->isFollowingEnabled();
    }

    public function getStoreTwitterUsername()
    {
        return Mage::helper('rewardssocial/twitter_follow')->getStoreTwitterUsername();
    }
}
