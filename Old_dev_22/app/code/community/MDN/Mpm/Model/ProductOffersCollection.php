<?php

class MDN_Mpm_Model_ProductOffersCollection extends MDN_Mpm_Model_CustomCollection
{

    private $size;
    private $productId;
    private $channel;
    private $allChannel = false;
    private $sort = array("field" => "total", "dir" => "asc");

    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->_isCollectionLoaded) {

            $offers = Mage::helper('Mpm/Carl')->getProductOffers($this->productId);
            $channelOffers = array();

            foreach($offers as $offer) {
                if(($offer['channel'] === $this->channel || $this->allChannel === true) && $offer['price'] !== 'NC') {
                    $channelOffers[] = $offer;
                }
            }

            $this->size = count($channelOffers);
            $this->sortOffersByTotal($channelOffers);
            foreach($channelOffers as $offer) {
                if($this->itemFulfilFilters($offer, $this->_filters)){
                    $offerData = new Varien_Object();

                    $offerData->channel = $offer['channel'];
                    $offerData->price = $offer['price'];
                    $offerData->shipping = $offer['shipping'];
                    $offerData->shipping_from = $offer['shipping_from'];
                    $offerData->competitor = $offer['competitor'];
                    $offerData->availability = $offer['availability'];
                    $offerData->state = $offer['state'];
                    $offerData->rank = $offer['rank'];
                    $offerData->competitor_code = $offer['competitor_code'];
                    $offerData->reference = $offer['reference'];
                    $offerData->total = $offer['total'];
                    $offerData->total_converted = $offer['total'];
                    $offerData->updated_at = $offer['updated_at'];
                    $offerData->is_me = $offer['competitor_code'] === Mage::getStoreConfig('mpm/repricing/seller_id_'.$offer['competitor']);

                     $this->addItem($offerData);
                }

            }

            $this->_isCollectionLoaded = true;
        }

        return $this;
    }

    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return boolean
     */
    public function setAllChannel($allChannel)
    {
        $this->allChannel = $allChannel;
    }

    private function sortOffersByTotal(&$channelOffers)
    {
        usort($channelOffers, array($this, 'sortChannelOffers'));
    }

    public  function addAttributeToSort($sortBy, $dir)
    {
        $this->sort = array("field" => $sortBy, "dir" => $dir);
    }

    protected function sortChannelOffers($a, $b){

        $sortBy = $this->sort;
        $column = $sortBy['field'];
        $dir = $sortBy['dir'];
        if ($a[$column] == $b[$column]) {
            return 0;
        }
        if( strcasecmp($dir , 'DESC') === 0)
            $compareResult = ($a[$column] > $b[$column]);
        else
            $compareResult = ($a[$column] < $b[$column]);
        return $compareResult ? -1 : 1;

    }

}
