<?php

include_once(dirname(__FILE__). DS .'..'. DS .'etc'. DS .'SocketClient.php');

class SweetToothTransfer
{
    // CURLOPT_TIMEOUT_MS may not be recognized by older versions of PHP
    const ST_CURLOPT_TIMEOUT_MS = 156;

    private $prefix = "/transfer";
    private $client;

    public function __construct($client) 
    {
        $this->client = $client;
    }

    /**
     * Creates a transfer. Allows the user to specify a timeout for the request. After the request is completed
     * timeout is reset to its inital value so it won't affect other operations.
     * 
     * @param  array             $fields Contains transfer data
     * @return array/json/object         Response body, defaults to array
     */
    public function create($fields)
    {
        $initialClientType = $this->client->getRestClientType();
        $this->client->setRestClientType('SweetToothSocketClient');

        $result = $this->client->post($this->prefix, $fields);

        $this->client->setRestClientType($initialClientType);

        return $result;
        //return $this->client->prepareResponse($result);
    }

    /**
    * Modifies a previously created transfer.
    * 
    * @param  string $id         Stores transfer ID.
    * @param  array $fields      Contains data that the transfer should be modfied with
    * @return array/json/object  Response body, defaults to array
    */
    public function modify($id, $fields)
    {
       
    }

    /**
     * Cleans up memory used when working with Transfer objects.
     */
    public function __destruct()
    {
        unset($this->prefix);
        unset($this->array);
    }
}
