<?php

class TBT_Rewardssocial_Block_Pinterest_Pin_Rewards extends TBT_Rewardssocial_Block_Abstract
{
    protected $_predictedPoints = null;
    protected $_customer = null;

    public function _toHtml()
    {
        if (Mage::helper('rewardssocial/pinterest_pin')->isPinRewardingEnabled()) {
            return parent::_toHtml();
        }

        return '';
    }

    public function getPinProcessingUri()
    {
        return $this->getUrl('rewardssocial/index/processPin');
    }

    public function getHasPinned()
    {
        $customer = $this->_getRS()->getSessionCustomer();
        if (!$customer->getId()) {
            return false;
        }

        $url = Mage::helper('core/url')->getCurrentUrl();
        return $this->_getValidator()->hasPinned($customer->getId(), $url);
    }

    public function getPredictedPointsString()
    {
        $predictedPoints = $this->getPredictedPoints();
        return (string) Mage::getModel('rewards/points')->set($predictedPoints);
    }

    public function getHasPredictedPoints()
    {
        $predictedPoints = $this->getPredictedPoints();
        return !empty($predictedPoints) && !$this->getHasPinned();
    }

    public function getPredictedPoints()
    {
        if ($this->_predictedPoints === null ) {
            $this->_predictedPoints = $this->_getValidator()->getPredictedPinterestPinPoints();
        }

        return $this->_predictedPoints;
    }

    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    protected function _getValidator()
    {
        return Mage::getSingleton('rewardssocial/pinterest_pin_validator');
    }
}