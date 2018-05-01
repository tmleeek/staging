<?php

class TBT_Rewardssocial_Block_Facebook_Like_Button extends Evolved_Like_Block_Like
{
    protected function _prepareLayout()
    {
        if (!Mage::helper('rewardssocial/facebook_config')->isLikingEnabled()
            || !$this->hasFacebookLike())
        {
            $this->setIsHidden(true);
        }

        // TODO: from TBT_Rewardssocial_Block_Widget_Abstract
        $this->setFrameTags("div class='rewardssocial-widget rewardssocial-{$this->getWidgetKey()}'", "/div");

        return parent::_prepareLayout();
    }

    /**
     * TODO: from TBT_Rewardssocial_Block_Widget_Abstract
     */
    public function getWidgetKey()
    {
        return str_replace('.', '-', $this->getNameInLayout());
    }

    public function getHasLiked()
    {
        $customer = $this->_getRS()->getSessionCustomer();
        if (!$customer->getId()) {
            return false;
        }

        $url = Mage::helper('core/url')->getCurrentUrl();
        return $this->_getValidator()->hasLikedPage($customer, $url);
    }

    public function getNotificationBlock()
    {
        return $this->getLayout()->createBlock('core/template')
            ->setTemplate('rewardssocial/facebook/like/points.phtml')
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
        return !empty($predictedPoints) && !$this->getHasLiked();
    }

    public function getPredictedPoints()
    {
        if ($this->_predictedPoints === null ) {
            $this->_predictedPoints = $this->_getValidator()->getPredictedFacebookLikePoints();
        }

        return $this->_predictedPoints;
    }

    /**
     * TODO: from TBT_Rewardssocial_Block_Widget_Abstract
     */
    protected function _toHtml()
    {
        $widgetName = $this->getParentBlock()->getWidgetName();
        $html = parent::_toHtml();
        if ($html != '') {
            $html .= "
                <script type='text/javascript'>
                    Event.observe(document, 'dom:loaded', function() {
                        " . $widgetName . ".addWidget('{$this->getWidgetKey()}');
                    });
                </script>
            ";
        }

        return $html;
    }

    protected function _getValidator()
    {
        return Mage::getSingleton('rewardssocial/facebook_like_validator');
    }

    protected function _getRS()
    {
        return Mage::getSingleton('rewards/session');
    }

    /**
     * Checks if Facebook Like button is present, currently if Evolved Like module
     * installed and enabled.
     * @return boolean true if Evolved Like module is enabled, false otherwise
     */
    public function hasFacebookLike()
    {
        return Mage::helper('rewardssocial')->isModuleEnabled('Evolved_Like');
    }
}
