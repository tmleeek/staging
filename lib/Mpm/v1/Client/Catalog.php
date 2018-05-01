<?php
namespace Mpm\GatewayClient\Client;

use GuzzleHttp\Post\PostFile;
use Mpm\GatewayClient\Client,
    Mpm\GatewayClient\Exceptions\FileNotFoundException;

class Catalog extends Client
{
    public static function getInstance()
    {
        if (self::$_instance === null || false === (self::$_instance instanceof Catalog)) {
            self::$_instance = new Catalog();
        }
        return self::$_instance;
    }

    public function upload($filename)
    {
        $params = array('type' => 'xml');
        $url = '/catalog';
        $attachments = array('catalog' => '@'.$filename);
        $this->post($url, $params,  array("catalog" => "@".$filename), array('Content-Type' => 'multipart/form-data'), $attachments);
    }

    public function updateProduct(array $product, $sku)
    {
        $parameters = array('product' => $product);

        return $this->put('/product/'.$sku, array(), $parameters);
    }

    public function getFields()
    {
        return $this->get('/catalog/attributes');
    }

    public function getCarlFields()
    {
        return $this->get('/catalog/carl-attributes');
    }
}