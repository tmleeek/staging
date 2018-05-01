<?php

class TBT_Rewardssocial_Block_Referral_Share_Button extends TBT_Rewardssocial_Block_Widget_Abstract
{
    protected $_predictedPoints = null;
    protected $_customer = null;

    public function _prepareLayout()
    {
        if (!Mage::helper('rewardssocial/referral_config')->isShareButtonEnabled()) {
            $this->setIsHidden(true);
            return parent::_prepareLayout();
        }

        $modalBlock = $this->getLayout()->createBlock('core/template')
            ->setTemplate('rewardssocial/referral/share/modal.phtml')
            ->setNotLoggedInMessage($this->getTextWithLoginLinks($this->__("[login_link]Login or create an account[/login_link] to be rewarded for sharing your referral link!")));

        $referralShareBlock = $this->getLayout()->createBlock('rewardsref/customer_referral_abstract')
            ->setTemplate('rewardsref/customer/referral/affiliate.phtml')
            ->setIsInModal(true);

        $referralWidgetsBlock = $referralShareBlock->getLayout()->getBlock('referral.share.widgets');

        $referralShareBlock->setChild('referral.share.widgets', $referralWidgetsBlock);
        $modalBlock->setChild('referral_share', $referralShareBlock);
        $this->setChild('modal', $modalBlock);


        $this->setFrameTags("div class='rewardssocial-widget rewardssocial-referral-{$this->getWidgetKey()} {$this->getCustomCss()}'", "/div");

        return parent::_prepareLayout();;
    }

    public function getNotificationBlock()
    {
        return $this->getLayout()->createBlock('core/template')
            ->setTemplate('rewardssocial/referral/share/points.phtml')
            ->setPredictedPointsString($this->getPredictedPointsString());
    }

    /**
     * Get predicted points for sharing a customer sharing his referral link,
     * formatted as a string.
     *
     * @return string
     */
    public function getPredictedPointsString()
    {
        $predictedPoints = $this->getPredictedPoints();
        return (string) Mage::getModel('rewards/points')->set($predictedPoints);
    }

    public function getPredictedPoints()
    {
        if ($this->_predictedPoints == null) {
            $this->_predictedPoints = $this->_getValidator()->getPredictedPoints();
        }

        return $this->_predictedPoints;
    }

    public function getHasPredictedPoints()
    {
        $predictedPoints = $this->getPredictedPoints();
        return !empty($predictedPoints);
    }

    /**
     * Retrieve Referral Share Validator model
     *
     * @return TBT_Rewardssocial_Model_Referral_Share_Validator
     */
    protected function _getValidator()
    {
        return Mage::getSingleton('rewardssocial/referral_share_validator');
    }

    /**
     * No counter implemented for now.
     * @return boolean false
     */
    public function isCounterEnabled()
    {
        return 'false';
    }
}
