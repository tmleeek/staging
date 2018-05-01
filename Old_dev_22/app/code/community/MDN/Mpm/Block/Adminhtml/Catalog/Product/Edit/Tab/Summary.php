<?php

class MDN_Mpm_Block_Adminhtml_Catalog_Product_Edit_Tab_Summary extends Mage_Adminhtml_Block_Widget {


    protected $_productOffers = null;
    protected $channelsInformations = null;


    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('Mpm/Catalog/Product/View/Tab/Summary.phtml');
    }

    public function getProduct()
    {
        return Mage::registry('product');
    }


    /**
     * @return mixed
     */
    public function getBestOfferPerChannel()
    {
        $bestOffers = array();
        foreach($this->getProductOffers() as $offer)
        {


            if ($offer->getrank() == 1)
            {
                $offer->setme(0);
                $bestOffers[] = $offer;
            }
        }
        return $bestOffers;
    }




    public function getPriceComparisonData()
    {
        $data = $this->getBestOfferPerChannel();

        try
        {
            $matchingData = Mage::helper('Mpm/Carl')->getMatchingData($this->getProduct()->getSku());
            $matchingData = (array)$matchingData;

            //append "me"
            $shipping = Mage::helper('Mpm/Shipping')->getRate($this->getProduct(), 'EUR', Mage::getStoreConfig('mpm/product_view/shipping_method_to_use'), 'FR');
            $myName = Mage::getStoreConfig('general/store_information/name');
            if (!$myName)
                $myName = $this->__('Me');
            $data[] = array('channel' => 'magento',
                'price' => $this->getMyPrice(),
                'shipping' => $shipping,
                'me' => 1,
                'total' => $this->getMyPrice() + $shipping,
                'total_converted' => $this->getMyPrice() + $shipping,
                'seller_name' => $myName);

            //sort
            usort($data, array('MDN_Mpm_Block_Adminhtml_Catalog_Product_Edit_Tab_Carl', 'sortOffers'));

            //append reference & url to elements
            for($i=0;$i<count($data);$i++)
            {
                if (isset($matchingData[$data[$i]['channel']]))
                {
                    $data[$i]['reference'] = $matchingData[$data[$i]['channel']]->reference;
                    $data[$i]['url'] = $matchingData[$data[$i]['channel']]->url;
                }
            }
        }
        catch(Exception $ex)
        {
            $this->_error = $ex->getMessage();
        }

        return $data;
    }

    public function getMyPrice()
    {
        return $this->getProduct()->getPrice();
    }

    public function getProductOffers()
    {
        if ($this->_productOffers === null)
        {
            $this->_productOffers = Mage::helper('Mpm/Product')->getOffers($this->getProduct());
        }
        return $this->_productOffers;
    }

    public function getImageChannel($channelName)
    {
        return Mage::helper('Mpm/Carl')->getChannelImageUrl($channelName);
    }

    public function getChannelsCurrency($channel)
    {
        if($channel === 'magento')
            return Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();

        $channelsInformations = $this->getChannels();
        foreach($channelsInformations as $channelInformations){
            if($channel === $channelInformations->channelCode)
                return Mage::app()->getLocale()->currency($channelInformations->currency)->getSymbol();
        }
    }

    public function getChannels()
    {
        if($this->channelsInformations === null){
            $this->channelsInformations = Mage::helper('Mpm/Carl')->getChannelsSubscribed();
        }
        return  $this->channelsInformations;
    }



}
