<?php

class TBT_Rewardssocial_Block_Purchase_Share_Abstract extends TBT_Rewardssocial_Block_Widget_Abstract
{
    const POINTS_NOTIFICATION_TEMPLATE = 'rewardssocial/purchase/points.phtml';

    protected $_buttonType = null;
    protected $_predictedPoints = null;

    public function getNotificationBlock()
    {
        $message = "Earn <b>%s</b> for sharing your purchase";
        $message .= ($this->_getButtonType()) ? " on {$this->_getButtonType()}!" : "!";
        $notificationBlock = $this->getLayout()->createBlock('core/template')
            ->setTemplate(self::POINTS_NOTIFICATION_TEMPLATE)
            ->setMessage($message)
            ->setPredictedPointsString($this->getPredictedPointsString());

        return $notificationBlock;
    }

    public function getHasPredictedPoints()
    {
        $predictedPoints = $this->getPredictedPoints();
        return !empty($predictedPoints);
    }

    public function getProductUrl()
    {
        if (!$this->getProduct()) {
            return null;
        }

        return Mage::helper('rewardssocial')->getProductUrl($this->getProduct());
    }

    public function getPredictedPoints()
    {
        if ($this->_predictedPoints === null ) {
            $this->_predictedPoints = $this->_getValidator()->getPredictedPoints();
        }

        return $this->_predictedPoints;
    }

    protected function _getValidator()
    {
        return Mage::helper('rewardssocial/purchase_share')->getValidator($this->_getButtonType());
    }

    /**
     * Overwrite this in child classes. Used for points notifications
     * ex. Earn 10 for sharing your purchase on **Twitter**!
     * @return string
     */
    protected function _getButtonType()
    {
        return $this->_buttonType;
    }

}
