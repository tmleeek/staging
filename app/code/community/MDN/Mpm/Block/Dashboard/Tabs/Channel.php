<?php

class MDN_Mpm_Block_Dashboard_Tabs_Channel extends Mage_Adminhtml_Block_Widget {

    private static $offers = array();
    protected $_channelStats = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('Mpm/Dashboard/Tabs/Channel.phtml');
    }

    public function getChannel()
    {
        return Mage::registry('mpm_channel');
    }

    public function getOffersCount()
    {
        return $this->getOffers('channel_offers');
    }

    public function getYourOffersCount()
    {
        return $this->getOffers('seller_offers');
    }

    public function getBbwCount()
    {
        return $this->getOffers('seller_bbw_offers');
    }

    public function getSellersCount()
    {
        $channel = $this->getChannel();
        $channelParts = explode('_', $channel);

        $query = array(
            'rows'   => 0,
            'filter' => 'channel_name,'.$channelParts[0].'_'.$channelParts[1],
        );

        return Mage::helper('Mpm/Carl')->listSellers($query)->max;
    }

    public function getOffersWithCompetitorCount($from, $to)
    {
        $totalCompetitors = 0;
        if(0 === $competitors = $this->getOffers('channel_competitors')) {
            return 0;
        }

        foreach($competitors as $countCompetitors => $nbCompetitors) {
            if($countCompetitors >= $from && $countCompetitors <= $to) {
                $totalCompetitors+= $nbCompetitors;
            }
        }

        return $totalCompetitors;
    }

    public function getMainCompetitors()
    {
        $channel = $this->getChannel();
        $bestCompetitors = Mage::helper('Mpm/Carl')->getBestCompetitors($channel, 10);

        return $bestCompetitors;
    }

    private function getOffers($field)
    {
        if(empty(self::$offers)) {
            self::$offers = Mage::helper('Mpm/Carl')->getStatisticsOffers();
        }

        $channel = $this->getChannel();
        return empty(self::$offers->$field->$channel) ? 0 : self::$offers->$field->$channel;
    }

}