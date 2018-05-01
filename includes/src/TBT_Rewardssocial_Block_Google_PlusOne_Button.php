<?php

class TBT_Rewardssocial_Block_Google_PlusOne_Button extends TBT_Rewardssocial_Block_Widget_Abstract
{
    protected $_predictedPoints = null;

    public function _prepareLayout()
    {
        $session = Mage::getSingleton('customer/session');
        $this->customerId = $session->getCustomerId();

        if (!Mage::helper('rewardssocial/google_config')->isGooglePlusEnabled()) {
            $this->setIsHidden(true);
        }

        return parent::_prepareLayout();
    }

    public function getHasPlusOned()
    {
        $customer = $this->_getRS()->getSessionCustomer();
        if (!$customer->getId()) {
            return false;
        }

        $url = Mage::helper('core/url')->getCurrentUrl();
        return $this->_getValidator()->hasPlusOned($customer->getId(), $url);
    }

    public function getNotificationBlock()
    {
        return $this->getLayout()->createBlock('core/template')
            ->setTemplate('rewardssocial/google/plusone/points.phtml')
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
        return !empty($predictedPoints) && !$this->getHasPlusOned();
    }

    public function getPredictedPoints()
    {
        if ($this->_predictedPoints === null ) {
            $this->_predictedPoints = $this->_getValidator()->getPredictedGooglePlusOnePoints();
        }

        return $this->_predictedPoints;
    }

    public function isCounterEnabled()
    {
        return Mage::helper('rewardssocial/google_config')->isGooglePlusCounterEnabled();
    }

    public function getPlusOneProcessingUrl()
    {
        return $this->getUrl('rewardssocial/index/processPlusOne');
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
        return Mage::getSingleton('rewardssocial/google_plusOne_validator');
    }
}
