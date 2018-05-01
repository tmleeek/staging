<?php

/**
 *
 *
 */
class TBT_Rewardssocial_Block_Facebook_Like_Notificationblock extends TBT_Rewardssocial_Block_Abstract {
    protected $_timeUntilNextLike = null;

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }


    /**
     *
     */
    public function getPredictedLikePointsString() {
        $str = (string) Mage::getModel('rewards/points')->set(  $this->getPredictedLikePoints()  );

        return $str;
    }

    /**
     *
     */
    public function getTimeUntilNextLike() {
        $customer = $this->_getRS()->getSessionCustomer();

        if($this->_timeUntilNextLike == null) {
            $this->_timeUntilNextLike = Mage::getModel('rewardssocial/facebook_like')->getCollection()->getTimeUntilNextLikeAllowed($customer);
        }

        return $this->_timeUntilNextLike;
    }

    /**
     *
     * @return boolean
     */
    public function getCanEarnFacebookPoints() {
        if ( ! $this->getHasPredictedLikePoints() ) {
            return false;
        }

        if ( ! $this->getHasLikedPage() ) {
            return false;
        }

        return true;

    }


    /**
     * 
     * @return boolean
     */
    public function getHasPredictedLikePoints() {
        $predicted = $this->getPredictedLikePoints();
        if ( empty($predicted) ) {
            return false;
        }
        
        return true;
    }
    

    
    /**
     * 
     */
    public function getPredictedLikePoints() {
        if($this->_predictedPoints == null) {
            $this->_predictedPoints = $this->_getFacebookLikeValidator()->getPredictedFacebookLikePoints();
        }
        return $this->_predictedPoints;
    }


    /**
     * 
     * @return boolean
     */
    public function getHasLikedPage() {
        $customer = $this->_getRS()->getSessionCustomer();
        $liked_url = $this->getCurrentPageURI();
        $hasLiked = $this->_getFacebookLikeValidator()->hasLikedPage($customer, $liked_url);

        return $hasLiked;
    }



    /**
     * If the is_hidden attribute is set, dont output anything.
     *
     * (overrides parent method)
     */
    protected function _toHtml() {
		if ( ! Mage::helper('rewardssocial/facebook_evlike')->isEvlikeEnabled() ) {
		    return "";
		}
		if ( ! Mage::helper('rewardssocial/facebook_evlike')->isEvlikeValidRewardsConfig() ) {
		    return "";
		}
        return parent::_toHtml();
    }


    /**
     * @return TBT_Rewardssocial_Model_Facebook_Like_Validator
     */
    protected function _getFacebookLikeValidator() {
        return Mage::getSingleton('rewardssocial/facebook_like_validator');
    }

}
