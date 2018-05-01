<?php

/**
 *
 */
class SweetToothAccount
{
    
    const RESOURCE_ACCOUNT   = '/account';
    
    private $prefix = "/account";
    private $client;

    public function __construct($client) {
        $this->client = $client;
    }
    
    /**
     * Creates a SweetTooth account. Checks to see if the instance of SweetTooth already has a key/secret
     * meaning an account has already been created using the SweetTooth instance
     * 
     * @param  array              $fields Contains account creation data
     * @return array/json/object          Response body, defaults to array
     */
    public function create($fields)
    {
        if ($this->client->getApiKey() || $this->client->getApiSecret()) {
            throw new Exception("You are attempting to create an account but you already have API Credentials configured (rewards/platform/apikey and rewards/platform/apisecret)");
        }

        $result = $this->client->post($this->prefix, $fields);
        $account = array_key_exists('account', $result) ? $result['account'] : $result;
        return $this->client->prepareResponse($account);
    }

    /**
     * Retreives merchant account data. Retrieves all account if called with no id.
     * 
     * @param  string $id Contains the ID of the account to be retrieved
     * @return array      Contains the account information stored in an array
     */
    public function get($id = null) 
    {
        $suffix = $id ? ('/' . $id) : '';
        $result = $this->client->get($this->prefix . $suffix);
        $account = array_key_exists('account', $result) ? $result['account'] : $result;
        return $this->client->prepareResponse($account);
    }

    /**
     * Cleans up variables used when working with account objects.
     */
    public function __destruct(){
        unset($this->prefix);
        unset($this->client);
    }
}