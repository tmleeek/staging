<?php
namespace Mpm\GatewayClient\Client;

include_once(dirname(__FILE__).DS.'..'.DS.'Client.php');

use Mpm\GatewayClient\Client;

class Auth extends Client
{
    public static function getInstance()
    {
        if (self::$_instance === null || false === (self::$_instance instanceof Auth)) {
            self::$_instance = new Auth();
        }
        return self::$_instance;
    }

    public function authenticate($login, $pass)
    {
        try {
            $credentialsResponse = $this->client->post(
                self::BASE_URL . '/' . self::API_VERSION .'/user/credentials',
                array(
                    'body' => array(
                        'email'    => $login,
                        'password' => $pass
                    )
                )
            );

            if ($data = json_decode((string)$credentialsResponse)) {
                $tokenResponse = $this->client->post(
                    self::BASE_URL . '/' . self::API_VERSION .'/oauth/authentication',
                    array(
                        'headers' => array('Authorization' => 'Basic ' . $data->credentials),
                        'body' => array('grant_type' => 'client_credentials')
                    )
                );

                $response = json_decode((string)$tokenResponse);

                return $response->body;
            } else {
                throw new \Exception('Bad response from API');
            }
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if ($e->getCode() == 401) {
                throw new Exception('Wrong credentials used');
            } else {
                throw new Exception('Bad response from API');
            }
        }
    }

    public function check()
    {
        $response = $this->get('/oauth');

        $response = json_decode((string) $response->getBody());

        if ($response->header->status != 200) {
            return false;
        }

        return true;
    }
}