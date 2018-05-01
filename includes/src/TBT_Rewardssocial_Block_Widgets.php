<?php

class TBT_Rewardssocial_Block_Widgets extends TBT_Rewardssocial_Block_Abstract
{
    protected $_pointsBlock             = null;
    protected $_product                 = null;
    protected $_widgetName              = 'rewardsSocialWidgetHover';
    protected $_widgetClass             = 'rewardssocial-widgets';
    protected $_widgetNotificationClass = 'rewardssocial-widgets-points-notification';

    public function getPointsNotificationBlock()
    {
        if ($this->_pointsBlock === null) {
            $this->_pointsBlock = $this->_fetchPointsNotificationBlock();
        }

        return $this->_pointsBlock;
    }

    /**
     * Loops through all social widgets and adds points notification block if it's the
     * case and widget is not hidden.
     *
     * @return TBT_Rewardssocial_Block_Widgets_Points
     */
    protected function _fetchPointsNotificationBlock()
    {
        $pointsBlock = $this->getLayout()->createBlock('rewardssocial/widgets_points')
            ->setSocialWidgetName($this->getWidgetName())
            ->setWidgetNotificationClass($this->getWidgetNotificationClass())
            ->setInlineStyling($this->getInlineStyling());

        foreach ($this->getSortedChildren() as $name) {
            $widget = $this->getLayout()->getBlock($name);

            // do not try to add points notification block if button is not enabled
            if ($widget->getIsHidden()) {
                continue;
            }

            if ($widget->getHasPredictedPoints()) {
                $pointsBlock->setHasPredictedPoints(true);
                $pointsBlock->addPointsNotification($name, $widget->getNotificationBlock());
            }
        }

        return $pointsBlock;
    }

    protected function _toHtml()
    {
        $html       = parent::_toHtml();
        $widgetName = $this->getWidgetName();
        $productId  = $this->getProduct() ? $this->getProduct()->getId() : null;

        if ($html != '') {
            $html .= "
                <script type='text/javascript'>
                    Event.observe(document, 'dom:loaded', function() {
                        " . $widgetName . ".data = " . $widgetName . ".data || {};
                        " . $widgetName . ".data.productId = '{$productId}';
                    });
                </script>
            ";
        }

        return $html;
    }

    public function getWidgetClass()
    {
        $widgetClass = $this->_widgetClass;
        if ($this->getProduct()) {
            $widgetClass .= ' ' . $this->getProduct()->getId();
        }

        return $widgetClass;
    }

    public function getWidgetName()
    {
        $widgetName = $this->_widgetName;
        if ($this->getProduct()) {
            $widgetName .= $this->getProduct()->getId();
        }

        return $widgetName;
    }

    public function getWidgetNotificationClass()
    {
        $widgetNotificationClass = $this->_widgetNotificationClass;
        if ($this->getProduct()) {
            $widgetNotificationClass .= $this->getProduct()->getId();
        }

        return $widgetNotificationClass;
    }

    public function getProduct()
    {
        if ($this->_product == null) {
            $this->_product = $this->hasData('product') ? $this->getData('product') : Mage::registry('product');
        }

        return $this->_product;
    }

    /**
     * This styling is applied to the points notification elements (rewardssocial/widgets/points.phtml)
     * @deprecated. Moved to widget.css
     * @return string
     */
    public function getInlineStyling()
    {
        return '';
    }
}
