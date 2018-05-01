<?php

class TBT_Rewardssocial_Block_Facebook_Like_Notificationblock_Response extends TBT_Rewardssocial_Block_Abstract {

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    /**
     *
     * @return boolean
     */
    public function getHasPredictedLikePoints()
    {
        $predicted = $this->getPredictedLikePoints();
        if ( empty($predicted) ) {
            return false;
        }

        return true;
    }



    /**
     *
     */
    public function getPredictedLikePoints()
    {
        if($this->_predictedPoints == null) {
            $this->_predictedPoints = $this->_getFacebookLikeValidator()->getPredictedFacebookLikePoints();
        }
        return $this->_predictedPoints;
    }

    /**
     *
     * @return boolean
     */
    public function getHasLikedPage()
    {
        $customer = $this->_getRS()->getSessionCustomer();
        $liked_url = $this->getCurrentPageURI();
        $hasLiked = $this->_getFacebookLikeValidator()->hasLikedPage($customer, $liked_url);

        return $hasLiked;
    }

    protected function _toHtml()
    {
        $response_html = "";

        // If no predicted points exist, don't display anything.
        if(!$this->getHasPredictedLikePoints()) return "&nbsp;";

        $msg = $this->getMsg();

        $text =  $msg->getText();

        $text = $this->getTextWithLoginLinks($text);

        if($msg->getType() == Mage_Core_Model_Message::ERROR) {
            $response_html = "<div class='facebook-like-rewards-notification-msg facebook-like-rewards-notification-error'>" . $text . "</div>";
        } else {
            $response_html = "<div class='facebook-like-rewards-notification-msg'>" . $text . "</div>";
        }

        return $response_html;
    }

    /**
     * @return TBT_Rewardssocial_Model_Facebook_Like_Validator
     */
    protected function _getFacebookLikeValidator()
    {
        return Mage::getSingleton('rewardssocial/facebook_like_validator');
    }
}