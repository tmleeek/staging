<?php
namespace Mpm\GatewayClient\Client;

use Mpm\GatewayClient\Client;

class Dashboard extends Client
{
    public static function getInstance()
    {
        if (self::$_instance === null || false === (self::$_instance instanceof Dashboard)) {
            self::$_instance = new Dashboard();
        }
        return self::$_instance;
    }

    public function getBestProducts()
    {
        return $this->get('/dashboard/best-products');
    }

    public function getBestCompetitors($channel, $limit)
    {
        $parameters = array(
            'channel' => $channel,
            'limit'   => $limit
        );

        $response = json_decode($this->get('/dashboard/best-competitors', $parameters));

        return (is_object($response)) ? $response->body : null;
    }

    public function getBestSellers()
    {
        return $this->get('/dashboard/best-sellers');
    }

    public function getSuggestedPriceAlertCompilation()
    {
        return $this->get('/dashboard/suggested-retail-price-alert-compilation');
    }
}