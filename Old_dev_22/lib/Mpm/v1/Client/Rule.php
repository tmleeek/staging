<?php
namespace Mpm\GatewayClient\Client;

use Mpm\GatewayClient\Client;

class Rule extends Client
{
    public static function getInstance()
    {
        if (self::$_instance === null || false === (self::$_instance instanceof Rule)) {
            self::$_instance = new Rule();
        }
        return self::$_instance;
    }

    public function postSave($code, $source, $withoutUserId = false)
    {
        $parameters = array(
            'code'   => $code,
            'source' => $source
        );

        if($withoutUserId) {
            $parameters['without_user_id'] = 'true';
        }

        return $this->post('/rule/save', array(), $parameters);
    }

    public function getMapping($codes)
    {
        $query = array(
            'codes' => $codes
        );

        return $this->get('/rule/play-many', $query);
    }

    public function getPlay($code)
    {
        $query = array(
            'code' => $code
        );

        return json_decode($this->get('/rule/play', $query))->body->rule_value;
    }

}