<?php

class MDN_Mpm_Block_Adminhtml_Catalog_Product_Edit_Tab_Carl extends Mage_Adminhtml_Block_Widget implements
    Mage_Adminhtml_Block_Widget_Tab_Interface{

    protected $_productOffers = null;

    protected $_productSetting = array();
    protected $channelsInformations = null;
    protected $_error = false;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Mpm/Catalog/Product/View/Tab/Carl.phtml');

        return Mage::register('mpm_product', $this->getProduct());
    }

    public function getProduct()
    {
        return Mage::registry('product');
    }


    public function getProductOffers()
    {
        if ($this->_productOffers === null)
        {
            $this->_productOffers = Mage::helper('Mpm/Product')->getOffers($this->getProduct());
        }
        return $this->_productOffers;
    }

    public function getBmPerformanceUrl()
    {
        return 'http://bms-performance.com/dashboard#/page/product/view/'.$this->getProduct()->getSku();
    }

    public function getRefreshUrl()
    {
        return $this->getUrl('adminhtml/Mpm_Carl/UpdateOffers', array('product_id' => $this->getProduct()->getId()));
    }

    public function getAllOffersGrid()
    {
        $url = $this->getUrl('Mpm/Carl/loadCarlTabAllOffers', array('product_id' => $this->getProduct()->getId()));

        $loaderJs =  $this->getJsUrl() . "mage/adminhtml/loader.js";

        $loaderGifUrl = $this->getSkinUrl('images/ajax-loader-tr.gif');

        $loadOffersBlockAsyncScript = <<<HTML
        <script>new Ajax.Updater("carl_offers_grid","$url", { evalScripts: true });</script>
                <script src="$loaderJs" type="text/javascript"></script>
        <div id="loadingmask" align="center">
<div class="loader" id="loading-mask-loader"><img src="$loaderGifUrl" alt="Loading"/></div>
<div id="loading-mask"></div>
</div>
HTML;
        return $loadOffersBlockAsyncScript;

    }

    public function getRepricingHistoryGrid()
    {
        $block = $this->getLayout()->createBlock('Mpm/Adminhtml_Catalog_Product_Edit_Tab_RepricingHistory');
        $block->setProduct($this->getProduct());
        return $block->toHtml();
    }


    public function getBestOffer($channel)
    {
        return Mage::helper('Mpm/Product')->getBestOffer($this->getProductOffers(), $channel);
    }

    public function getColorForRepricingStatus($status)
    {
        return Mage::helper('Mpm/Pricing')->getColorForRepricingStatus($status);
    }

    /**
     * @return mixed
     */
    public function getMyPrice()
    {
        return $this->getProduct()->getPrice();
    }

    public function getApplyPriceUrl($channel)
    {
        return $this->getUrl('adminhtml/Mpm_Pricer/apply', array('product_id' => $this->getProduct()->getId(), 'channel' => ($channel ? $channel->channelCode : '')));
    }


    public static function sortOffers($a, $b)
    {
        $al = strtolower($a['total']);
        $bl = strtolower($b['total']);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }

    public function getChannels()
    {
        if($this->channelsInformations === null){
            $this->channelsInformations = Mage::helper('Mpm/Carl')->getChannelsSubscribed();
        }
        return  $this->channelsInformations;
    }

    public function getOffersSummary()
    {
        $url = $this->getUrl('Mpm/Carl/loadCarlTabOffersSummary', array('product_id' => $this->getProduct()->getId()));
        $loaderJs =  $this->getJsUrl() . "mage/adminhtml/loader.js";
        $loaderGifUrl = $this->getSkinUrl('images/ajax-loader-tr.gif');

        $mpm_url_update = $this->getUrl('adminhtml/Mpm_Products/changeSetting', array('product_id' => '#product_id#',
        'channel' => '#channel#', 'field' => '#field#', 'value' => '#value#'));

        $loadOffersBlockAsyncScript = <<<HTML
        <script>new Ajax.Updater("carl_offers_summary","$url",{ evalScripts: true }); var MPM_URL_UPDATE_PRODUCT =
         '$mpm_url_update';</script>
        <script src="$loaderJs" type="text/javascript"></script>
        <div id="loadingmask" align="center">
<div class="loader" id="loading-mask-loader"><img src="$loaderGifUrl" alt="Loading"/></div>
<div id="loading-mask"></div>
</div>
HTML;
        return $loadOffersBlockAsyncScript;
    }


    public function getTabLabel()
    {
        return Mage::helper('Mpm')->__('SmartPrice');
    }

    public function getTabTitle()
    {
        return Mage::helper('Mpm')->__('SmartPrice');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }


}
