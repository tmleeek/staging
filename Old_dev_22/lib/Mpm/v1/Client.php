<?php 
namespace Mpm\GatewayClient;

include_once(dirname(__FILE__).DS.'Interfaces'.DS.'Client.php');
include_once(dirname(__FILE__).DS.'CurlClient.php');
include_once(dirname(__FILE__).DS.'Cache.php');

/**
 * Base class for Gateway client. Provides base method for sending and receiving 
 * requests
 *
 * @author Nicolas Mugnier <nicolas@boostmyshop.com>
 * @package Mpm\GatewayClient
 */
abstract class Client implements Interfaces\ApiClient
{

    /**
     * @constant string API base URL
     */
    // const BASE_URL = 'http://development.nico.carl.com/api';
    const BASE_URL = 'http://gateway.mpm.com/api';

    const API_VERSION = 'v1';

    /**
     * @var Mpm\GatewayClient\Client $_instance Current instance of Gateway Client
     */
    protected static $_instance = null;

    /**
     * @var \GuzzleHttp\Client Handles instance of Guzzle HTTP client
     */
    protected $client = null;

    /**
     * @var string $token Handles Oauth token to send with request
     */
    protected $token = null;

    /**
     * Class constructor. Creates a new instance of Guzzle HTTP client
     */
    protected function __construct()
    {
        $this->client = new CurlClient();
    }

    /**
     * Sets Oauth token to be send with next request
     * @param string $token Oauth token
     * @return void
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Sends a DELETE request to Carl API
     * @param string $route Route to call
     * @param array $params Additional params to add to request
     * @param array $body Additional params to add to request's body
     * @param array $headers Additional params to add to request's headers
     * @return mixed
     */
    protected function delete($route, $params = array(), $body = array(), $headers = array())
    {
        try {

            $params  = array_merge(array('format' => 'json'), $params);
            $headers = array_merge(array('Authorization' => 'Bearer ' . $this->token), $headers);
            $body    = array_merge(array('grant_type' => 'client_credentials', 'format' => 'json'), $body);

            $queryString = array();
            foreach ($params as $key => $value) {
                $queryString[] = $key . '=' . $value;
            }

            $response = $this->client->delete(
                self::BASE_URL . '/' . self::API_VERSION . $route . '?' . implode('&', $queryString),
                array(
                    'headers' => $headers,
                    'body'    => $body
                )
            );

            $this->checkResponse($response);

            return $response;

        } catch (Exception $e) {
            return $e->getResponse();
        }
    }

    /** 
     * Sends a GET request to Carl API
     * @param string $route Route to call
     * @param array $params Additional params to add to request
     * @param array $body Additional params to add to request's body
     * @param array $headers Additional params to add to request's headers
     * @return mixed
     */
    protected function get($route, $params = array(), $body = array(), $headers = array(), $ttl = null)
    {
        if($ttl !== null) {
            $keyCache = md5($route . serialize($params) . serialize($body) . serialize($headers));
            if($response = Cache::get($keyCache, $ttl)) {
                return $response;
            }
            unset($response);
        }

        try {

            $params  = array_merge(array('format' => 'json'), $params);
            $headers = array_merge(array('Authorization' => 'Bearer ' . $this->token), $headers);
            $body    = array_merge(array('grant_type' => 'client_credentials', 'format' => 'json'), $body);

            $response = $this->client->get(
                self::BASE_URL . '/' . self::API_VERSION . $route . '?' . http_build_query($params),
                array(
                    'headers' => $headers,
                    'body'    => $body
                )
            );

            $this->checkResponse($response);

            if($ttl !== null) {
                Cache::set($keyCache, $response);
            }

            return $response;

        } catch (Exception $e) {
            return $e->getResponse();
        }
    }

    /** 
     * Sends a POST request to Carl API
     * @param string $route Route to call
     * @param array $params Additional params to add to request
     * @param array $body Additional params to add to request's body
     * @param array $headers Additional params to add to request's headers
     * @return mixed
     */
    protected function post($route, $params, $body = array(), $headers = array(), $attachments = array())
    {
        try {
            $params  = array_merge(array('format' => 'json'), $params);
            $headers = array_merge(array('Authorization' => 'Bearer ' . $this->token), $headers);
            $body    = array_merge(array('grant_type' => 'client_credentials', 'format' => 'json'), $body);

            $queryString = array();
            foreach ($params as $key => $value) {
                $queryString[] = $key . '=' . urlencode($value);
            }

            $response = $this->client->post(
                self::BASE_URL . '/' . self::API_VERSION . $route . '?' . implode('&', $queryString),
                array(
                    'headers' => $headers,
                    'body'    => $body
                ),
                $attachments
            );

            $this->checkResponse($response);

            return $response;

        } catch (Exception $e) {
            return $e->getResponse();
        }
    }

    /**
     * Sends a DELETE request to Carl API
     * @param string $route Route to call
     * @param array $params Additional params to add to request
     * @param array $body Additional params to add to request's body
     * @param array $headers Additional params to add to request's headers
     * @return mixed
     */
    protected function put($route, $params = array(), $body = array(), $headers = array())
    {
        try {

            $params  = array_merge(array('format' => 'json'), $params);
            $headers = array_merge(array('Authorization' => 'Bearer ' . $this->token), $headers);
            $body    = array_merge(array('grant_type' => 'client_credentials', 'format' => 'json'), $body);

            $queryString = array();
            foreach ($params as $key => $value) {
                $queryString[] = $key . '=' . urlencode($value);
            }

            $response = $this->client->put(
                self::BASE_URL . '/' . self::API_VERSION . $route . '?' . implode('&', $queryString),
                array(
                    'headers' => $headers,
                    'body'    => $body
                )
            );

            $this->checkResponse($response);

            return $response;

        } catch (Exception $e) {
            return $e->getResponse();
        }
    }

    public function checkResponse($response)
    {
        $result = json_decode($response);

        if (!empty($result) && $result->header->status != 200)
        {
            if (isset($result->body->message) && (isset($result->header->message)))
                throw new \Exception($result->body->message, $result->header->message);
            elseif(isset($result->body->errors) && is_array($result->body->errors))
                throw new \Exception(implode(', ', $result->body->errors));
            elseif(isset($result->body->message))
                throw new \Exception($result->body->message);
            else
                throw new \Exception($result->header->status, $result->header->status);
        }
    }

    public function setLogPath($logPath)
    {
        $this->client->_logFilePath = $logPath;
        return $this;
    }

}