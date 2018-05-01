<?php

abstract class TBT_Rewardssocial_Block_Widget_Abstract extends TBT_Rewardssocial_Block_Abstract
{
    protected function _prepareLayout()
    {
        $this->setFrameTags("div class='rewardssocial-widget rewardssocial-{$this->getWidgetKey()} {$this->getCustomCss()}'", "/div");
        return parent::_prepareLayout();
    }

    abstract public function getNotificationBlock();

    abstract public function getHasPredictedPoints();

    public function getWidgetKey()
    {
        return str_replace('.', '-', $this->getNameInLayout());
    }

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

    /**
     * Get predicted points for social action formatted as a string.
     * @return string
     */
    public function getPredictedPointsString()
    {
        $predictedPoints = $this->getPredictedPoints();
        return (string) Mage::getModel('rewards/points')->set($predictedPoints);
    }

    /**
     * Appends css class if counter is enabled for social widget
     * @return string
     */
    public function getCustomCss()
    {
        if ($this->isCounterEnabled()) {
            return "rewardssocial-{$this->getWidgetKey()}-counter";
        }

        return '';
    }
}
