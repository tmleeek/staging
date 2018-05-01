<?php
namespace Mpm\GatewayClient\Client;

include_once dirname(__FILE__).DS.'..'.DS.'Client.php';

use Mpm\GatewayClient\Client;

class Product extends Client
{
    public static function getInstance()
    {
        if (self::$_instance === null || false === (self::$_instance instanceof Product)) {
            self::$_instance = new Product();
        }
        return self::$_instance;
    }

    public function listProducts($query)
    {
        return $this->get('/product', $query);
    }

    public function getProduct($productId)
    {
        return $this->get('/product/' . $productId);
    }
}