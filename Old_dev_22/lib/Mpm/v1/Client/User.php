<?php
namespace Mpm\GatewayClient\Client;

use Mpm\GatewayClient\Client;

class User extends Client
{
    public static function getInstance()
    {
        if (self::$_instance === null || false === (self::$_instance instanceof User)) {
            self::$_instance = new User();
        }
        return self::$_instance;
    }

    public function getUserInfos()
    {
        return $this->get('/resource/user');
    }

    public function getUserAvatar()
    {
        return $this->get('/resource/user/avatar');
    }

    public function getStatus()
    {
        $value =  $this->get('/status/user-progress-status');
        $result = json_decode($value);
        return $result->body->value;
    }
}
