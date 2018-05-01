<?php

class TBT_RewardsReferral_Model_Observer_Block_Output extends Varien_Object
{
    /**
     * Executed from the core_block_abstract_to_html_after event
     * @param Varien_Event $observer
     */
    public function afterOutput($observer)
    {
        $block     = $observer->getEvent ()->getBlock ();
        $transport = $observer->getEvent ()->getTransport ();

        // Magento 1.4.0.1 and lower dont have this transport, so we can't do autointegration : (
        if (empty($transport)) {
            return $this;
        }

        if (Mage::getStoreConfigFlag('advanced/modules_disable_output/TBT_RewardsReferral')) {
            return $this;
        }

        $this->_injectReferralEarnedToOrderCancelPopup($block, $transport);
        $this->_appendPointsAdjustmentToCreditmemo($block, $transport);

        return $this;
    }

    /**
     * This will append a field to adjust the points earned by an affiliate on this order,
     * if any, to Rewards popup on admin cancel order operation before canceling the order
     * @param  Mage_Adminhtml_Block_Sales_Order_View $block
     * @param  Varien_Object $transport
     * @return $this
     */
    protected function _injectReferralEarnedToOrderCancelPopup($block, $transport)
    {
        if (!($block instanceof Mage_Adminhtml_Block_Sales_Order_View)) {
            return $this;
        }

        $html = $transport->getHtml();

        $popup = $block->getLayout()->createBlock('rewardsref/adminhtml_sales_order_creditmemo_points');
        $popup->setOrder($block->getOrder());
        $html .= $popup->toHtml();

        $transport->setHtml($html);

        return $this;
    }

    /**
     * Appends a field to the creditmemo form, to adjust points earned by the referral on the order.
     * @param Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals $block
     * @param Varien_Object $transport
     * @return $this
     */
    protected function _appendPointsAdjustmentToCreditmemo($block, $transport)
    {
        if (!($block instanceof Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals)) {
            return $this;
        }

        $html = $transport->getHtml();

        $stBlock = $block->getLayout()->createBlock('rewardsref/adminhtml_sales_order_creditmemo_points');
        $stBlock->setOrder($block->getOrder());
        $stHtml = $stBlock->toHtml();

        $html .= "<div class='divider'></div>";
        $html .= $stHtml;

        $transport->setHtml($html);

        return $this;
    }

    /*
     * Adds "My Referrals" link in Customer Dashboard Navigation section
     * @param Varien_Event_Observer $observer
     */
    public function addCustomerDashboardLink($observer)
    {
        $block = $observer->getEvent()->getBlock();

        if (!($block instanceof Mage_Customer_Block_Account_Navigation)) {
            return $this;
        }

        // if TBT_RewardsReferral module output is disabled don't add the link
        if (Mage::getStoreConfigFlag('advanced/modules_disable_output/TBT_RewardsReferral')) {
            return $this;
        }

        //  if Referral Link Configuration is set to Hide then don't add the link
        if (!Mage::getStoreConfigFlag('rewards/referral/referral_show')) {
            return $this;
        }

        $label = Mage::helper('rewardsref')->__("My Referrals");
        $block->addLink("rewardsref", "rewardsref/customer/index/", $label);

        return $this;
    }
}