<?php

/**
 * @method setHasPredictedPoints
 * @method getHasPredictedPoints
 */
class TBT_Rewardssocial_Block_Widgets_Points extends TBT_Rewardssocial_Block_Abstract
{
    protected $_pointsNotifications = array();

    protected function _construct()
    {
        $this->setTemplate('rewardssocial/widgets/points.phtml');
        return parent::_construct();
    }

    public function isLoggedIn()
    {
        return $this->_getRS()->isCustomerLoggedIn();
    }

    public function addPointsNotification($widgetName, $notificationBlock)
    {
        $widgetName = str_replace('.', '-', $widgetName);
        $this->_pointsNotifications[$widgetName] = $notificationBlock;
        return $this;
    }

    public function getPointsNotifications()
    {
        return $this->_pointsNotifications;
    }

    /**
     * Returns the block used to notify customer of points regarding a certain social widget.
     * May be "You can earn X points," or "You have earned X points," etc.
     * @param string $widgetName Based on the name given to the widget block in the layout XML
     */
    public function getPointsNotification($widgetName = null)
    {
        if ($widgetName === null) {
            // If widgetName is not specified, grab the first one available.  Should only be used when you know only one exists.
            reset($this->_pointsNotifications);
            $widgetName = key($this->_pointsNotifications);
        }

        $widgetName = str_replace('.', '-', $widgetName);
        if (isset($this->_pointsNotifications[$widgetName])) {
            return $this->_pointsNotifications[$widgetName];
        }

        return false;
    }

    /**
     * Number of points notifications, equal to the number of social widgets that are offering points.
     * @return number
     */
    public function getRewardCount()
    {
        return count($this->_pointsNotifications);
    }

    public function getPointsCurrency()
    {
        $currency = Mage::getSingleton('rewards/currency')->getCurrencyCaption(1);
        return $this->__($currency) . " " . $this->__("Points");
    }
}
