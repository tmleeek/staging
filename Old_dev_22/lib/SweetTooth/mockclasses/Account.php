<?php


/**
 *
 */
class SweetToothAccount
{   
    const RESOURCE_ACCOUNT   = '/account';
    
    private $prefix = '/account';
    private $client;

    public function __construct($client) {
        $this->client = $client;
    }
    
    public function create($fields)
    {    
        if ($this->client->getApiKey() || $this->client->getApiSecret()) {
            throw new SweetToothSdkException('You are attempting to create an account '.
                 'but you already have API Credentials configured '.
                 '(rewards/platform/apikey and rewards/platform/apisecret)');
        }
        $result = $this->_createMockAccount();
        return $this->client->prepareResponse($result);
    }

    public function get() 
    {
        $result = $this->_createMockAccount();
        return $this->client->prepareResponse($result);
    }        

    public function __destruct()
    {
        unset($this->prefix);
        unset($this->client);
    }
    
    protected function _createMockAccount() 
    {
        $account = array (
            'channel_user_id'    => 34,
            'firstname'          => 'John',
            'lastname'           => 'Doe',
            'email'              => 'jdoe@example.com'                                                                                        
        );
        
        return $account;                     
    }
}
