<?php

class TBT_Rewardssocial_Block_Twitter_Tweet_Notificationblock extends TBT_Rewardssocial_Block_Abstract
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }


    public function getHasTweeted()
    {
        $customer = $this->_getRS()->getSessionCustomer();
        $url = Mage::helper('core/url')->getCurrentUrl();
        $hasTweeted = $this->_getTwitterTweetValidator()->hasTweeted($customer->getId(), $url);

        return $hasTweeted;
    }


    /**
     *
     */
    public function getPredictedTweetPointsString()
    {
        $str = (string) Mage::getModel('rewards/points')->set(  $this->getPredictedTweetPoints()  );

        return $str;
    }

    /**
     *
     * @return boolean
     */
    public function getCanEarnTweetPoints()
    {
        if ( ! $this->getHasPredictedLikePoints() ) {
            return false;
        }

        return true;
    }

    /**
     *
     */
    public function getHasPredictedTweetPoints()
    {
        $predicted = $this->getPredictedTweetPoints();
        if ( empty($predicted) ) {
            return false;
        }

        return true;
    }


    /**
     *
     */
    public function getPredictedTweetPoints()
    {
        if ($this->_predictedPoints == null ) {
            $this->_predictedPoints = $this->_getTwitterTweetValidator()->getPredictedTwitterTweetPoints();
        }
        return $this->_predictedPoints;
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


    /**
     * @return TBT_Rewardssocial_Model_Twitter_Tweet_Validator
     */
    protected function _getTwitterTweetValidator()
    {
        return Mage::getSingleton('rewardssocial/twitter_tweet_validator');
    }
}
