<?php

class TBT_Rewardssocial_Block_Purchase_Widgets extends TBT_Rewardssocial_Block_Widgets
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('rewardssocial/widgets.phtml');
        $this->setName('rewardssocial.checkout.purchase.widgets');

        return $this;
    }

    protected function _toHtml()
    {
        $html       = parent::_toHtml();
        $widgetName = $this->getWidgetName();
        $orderId    = $this->hasOrderId() ? $this->getOrderId() : null;
        if ($html != '') {
            $html .= "
                <script type='text/javascript'>
                    Event.observe(document, 'dom:loaded', function() {
                        " . $widgetName . ".data = " . $widgetName . ".data || {};
                        " . $widgetName . ".data.orderId = '{$orderId}';
                    });
                </script>
            ";
        }

        return $html;
    }

    /**
     * This styling is applied to the points notification elements (rewardssocial/widgets/points.phtml)
     * @return string
     */
    public function getInlineStyling()
    {
        return 'vertical-align: top; padding-top: 2px;';
    }
}
