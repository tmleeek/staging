<?php
namespace Mpm\GatewayClient\Client;

use Mpm\GatewayClient\Client;

class Seller extends Client
{
    public static function getInstance()
    {
        if (self::$_instance === null || false === (self::$_instance instanceof Seller)) {
            self::$_instance = new Seller();
        }
        return self::$_instance;
    }

    public function jsonListSellers()
    {
        return json_decode($this->get('/client-data/competitor/json-list'))->body;
    }

    public function listSellers($query)
    {
        return json_decode($this->get('/seller', $query))->body;
    }

    public function getWebserviceCredentials($channel)
    {
        $query = array(
            'channel' => $channel
        );

        $result = $this->get('/seller-webservice/credentials', $query);

        $result = json_decode($result);
        $credentials = (isset($result->body->credentials)) ? $result->body->credentials : '';
        return json_decode($credentials);
    }

    public function setWebserviceCredentials($channel, $data)
    {
        $query = array(
            'channel' => $channel,
            'credentials' => json_encode($data)
        );

        $this->post('/seller-webservice/credentials', $query);

        return true;
    }

}