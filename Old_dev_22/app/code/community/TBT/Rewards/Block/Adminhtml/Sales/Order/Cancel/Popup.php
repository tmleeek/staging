<?php

class TBT_Rewards_Block_Adminhtml_Sales_Order_Cancel_Popup extends TBT_Rewards_Block_Manage_Widget_Popup
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        $this->setTitle($this->__("Adjust Points"));
        
        $submitJs = "
            var pointsEarned = $('points_earned');
            var pointsSpent = $('points_spent');
            rewardsOrderCancelUrl += '?' + pointsEarned.name + '=' + pointsEarned.value;
            rewardsOrderCancelUrl += '&' + pointsSpent.name + '=' + pointsSpent.value;
            setLocation(rewardsOrderCancelUrl);
        ";
        
        $this->addButton($this->getButtonHtml(
            $this->__("Back"),
            'closeRewardsOrderCancelPopup();',
            'back',
            'rewards_order_cancel_back_button'
        ));
        $this->addButton($this->getButtonHtml(
            $this->__("Reset"),
            'resetPointsAdjustment();',
            '',
            'rewards_order_cancel_reset_button'
        ));
        $this->addButton($this->getButtonHtml(
            $this->__("Cancel Order"),
            $submitJs,
            'save',
            'rewards_order_cancel_submit_button'
        ));
        
        $deleteConfirmRewrite = <<<FEED
            var rewardsOrderCancelUrl = '#';
            var oldDeleteConfirm = deleteConfirm;
            deleteConfirm = function(message, url) {
                if (message == '{$this->__("Are you sure you want to cancel this order?")}') {
                    rewardsOrderCancelUrl = url;
                    openRewardsOrderCancelPopup();
                } else {
                    oldDeleteConfirm(message, url);
                }
            }
FEED;
        $this->addPostJavaScript($deleteConfirmRewrite);
        $this->addPostJavaScript("enablePointsFields();");
        
        return $this;
    }
    
    protected function _toHtml()
    {
        // we're setting this in _toHtml so it runs late enough for getOrder to have a value in getFieldHtml below
        $msg = $this->__("This customer has earned or spent points on this order. Please adjust points spent and/or earned:");
        $msg .="<a id='cancel_order_21067993_wikiHint' class='wikiHint' href='https://support.sweettoothrewards.com/entries/21067993-what-happens-when-i-cancel-an-order' title='Adjust Points' target='_blank'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>";
        $msg .= $this->_getFieldHtml();
        $this->setPopupContent($msg);
        
        return parent::_toHtml();
    }
    
    protected function _getFieldHtml()
    {
        $adjustmentBlock = $this->getLayout()->createBlock('rewards/adminhtml_sales_order_creditmemo_points');
        $adjustmentBlock->setOrder($this->getOrder())
            ->setFieldWrapper('rewards')
            ->setStyle("border: none; background: none; margin-left: auto; margin-right: auto; text-align: right;");
        return $adjustmentBlock->toHtml();
    }
}
