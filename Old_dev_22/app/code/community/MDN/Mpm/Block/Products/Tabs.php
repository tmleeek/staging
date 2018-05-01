<?php

class MDN_Mpm_Block_Products_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->setId('products_tab');
        $this->setDestElementId('products_tab');
        $this->setTitle(Mage::helper('Mpm/Carl')->getChannelLabel($this->getChannel()).' - '.$this->getProduct()->getName());
        $this->setTemplate('Mpm/Widget/TabsHoriz.phtml');
    }

    /**
     * Set tabs
     */
    protected function _beforeToHtml() {

        $productId = $this->getRequest()->getParam('product_id');
        $channel = $this->getRequest()->getParam('channel');
        $this->addTabPricing($productId, $channel);
        $this->addTabHistory($productId, $channel);
        $this->addTabPricingHistory($productId, $channel);
        $this->addTabInformation($productId, $channel);
        if($channel === 'custom_nc_default') {
            $this->addTabMatching($productId, $channel);
        }
        $this->addTabDebug($productId, $channel);

        return parent::_beforeToHtml();
    }

    public function getProduct()
    {
        return Mage::registry('mpm_product');
    }

    public function getChannel()
    {
        return Mage::registry('mpm_channel');
    }

    protected function addTabPricing($productId, $channel)
    {
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/Mpm_Products/offersPopupBlock', array('product_id' => urlencode($productId), 'channel' => $channel, 'block_name' => 'Pricing'));
        $this->addTabBlock($url, 'pricing', 'Pricing');
    }

    protected function addTabHistory($productId, $channel)
    {
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/Mpm_Products/offersPopupBlock', array('product_id' => urlencode($productId), 'channel' => $channel, 'block_name' => 'History'));
        $this->addTabBlock($url, 'history', 'History');
    }

    protected function addTabInformation($productId, $channel)
    {
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/Mpm_Products/offersPopupBlock', array('product_id' => urlencode($productId), 'channel' => $channel, 'block_name' => 'Information'));
        $this->addTabBlock($url, 'information', 'Information');
    }
    protected function addTabPricingHistory($productId, $channel)
    {
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/Mpm_Products/offersPopupBlock', array('product_id' => urlencode($productId), 'channel' => $channel, 'block_name' => 'PricingHistory'));
        $this->addTabBlock($url, 'pricing_history', 'Pricing History');
    }

    protected function addTabDebug()
    {
        $block = $this->getLayout()->createBlock('Mpm/Products_Tabs_Debug');
        $this->addTab('Debug', array(
            'label' => 'Debug',
            'content' => $block->toHtml(),
        ));
    }

    protected function addTabMatching($productId, $channel)
    {
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/Mpm_Products/offersPopupBlock', array('product_id' => urlencode($productId), 'channel' => $channel, 'block_name' => 'Matching'));
        $this->addTabBlock($url, 'matching', 'Matching');
    }

    protected function addTabBlock($url, $blockName, $label)
    {

        $this->addTab(
            $blockName,
            array(
                'label'   => Mage::helper('Mpm')->__($label),
                'class' => 'ajax',
                'url' => $url,
            )
        );
    }
}
