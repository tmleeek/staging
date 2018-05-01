<?php
namespace Mpm\GatewayClient\Client;

use Mpm\GatewayClient\Client;

class Monitoring extends Client
{
    public static function getInstance()
    {
        if (self::$_instance === null || false === (self::$_instance instanceof Monitoring)) {
            self::$_instance = new Monitoring();
        }
        return self::$_instance;
    }

    public function getChannelsSubscribed()
    {
        $result = $this->get('/monitoring/channel/subscribed', array(), array(), array(), 21600);
        $result = json_decode($result);
        $result = $result->body;

        return $result;
    }

    public function getAllChannels(){

        $result = $this->get('/monitoring/channel/all', array(), array(), array(), 21600);
        $result = json_decode($result);
        $result = $result->body;

        return $result;

    }

    public function getOfferInformation($productId, $channelId)
    {
        $result = $this->get('/monitoring/offer/information', array(
            'sku'     => $productId,
            'channel' => $channelId
        ));

        $result = json_decode($result);
        $result = $result->body;

        return $result;
    }

    /**
     * Return every competitors for a SKU
     *
     * @param $sku
     * @return mixed
     */
    public function getAllCompetitors($sku)
    {
        $result = $this->get(
            '/monitoring/offer/competitors',
            array('sku' => $sku),
            array(),
            array(),
            3
        );
        $result = json_decode($result);

        $result = $result->body;

        return $result;
    }


    /**
     * Return every competitors for a SKU
     *
     * @param $sku
     * @return mixed
     */
    public function getCompetitorsHistory($sku, $channelCode, $from = null, $to = null)
    {
        $result = $this->get('/monitoring/offer/history', array(
                'sku'     => $sku,
                'channel'     => $channelCode,
                'from'     => $from,
                'to'     => $to
        ));
        $result = json_decode($result);

        $result = $result->body;

        //echo "<pre>";var_dump($result);die();

        return $result;
    }

    public function getMatchingData($sku)
    {
        $result = $this->get('/monitoring/offer/matching-data', array(
                'sku'     => $sku
        ));
        $result = json_decode($result);
        $result = $result->body;

        return $result;
    }

    public function getMatchingPercent($query)
    {
        return $this->get('/monitoring/match/percent', $query);
    }

    public function getBestOffers($query)
    {
        return $this->get('/monitoring/offer/best', $query);
    }

    public function getChannelStats()
    {
        $value = $this->get('/monitoring/channel/stats');
        $value = json_decode($value);
        $value = $value->body;
        return $value;
    }

    public function getOfferHistory($query) 
    {
        return $this->get('/monitoring/offer/history', $query);
    }

    public function postMatchingByUrls($sku, array $urls)
    {
        $parameters = array(
            'productId' => $sku,
            'channel' => 'custom_nc_default',
            'urls' => implode(',', $urls),
        );

        return $this->post('/monitoring/match/by-urls', array(), $parameters);
    }

    /**
     * @param $sku
     *
     * @return array Returns the urls associated to the product
     */
    public function getMatchingByUrls($sku)
    {
        $parameters = array(
            'productId' => $sku,
            'channel' => 'custom_nc_default',
        );

        $response = $this->get('/monitoring/match/matching-urls', $parameters);

        return json_decode($response)->body;
    }
}
