<?php


/**
 *
 */
class SweetToothChannel
{
    const RESOURCE_ACCOUNT   = '/channel';
    
    private $prefix = '/channel';
    private $client;

    public function __construct($client) 
    {
        $this->client = $client;
    }
    
    public function create($fields)
    {    
        $result = $this->_create($fields);
        return $this->client->prepareResponse($result);
    }

    public function get() 
    {
        // List
        $result = $this->_get();
        
        $account = $result;

        return $this->client->prepareResponse($account);
    }
    
    

    public function __destruct()
    {
        unset($this->prefix);
        unset($this->client);
    }
    
    private function _create($fields) 
    {
        $createdChannel = array (
            'channel_id'        => 50,
            'channel_type'      => 'prestashop',
            'api_key'           => '93589512ab0501b6009f261280696657',
            'api_secret'        => 'a4712bfe91a9c36a78c0e9f0d3018ff8',
            'frontend_url'      => $fields['frontend_url'],
            'backend_url'       => $fields['backend_url'],
            'platoform_version' => $fields['platform_version'],
            'channel_version'   => $fields['channel_version']                                                    
        );  
          
        return $createdChannel;        
    }
    
    private function _get()
    {
        $channel = array (
            'channel_id'        => 50,
            'channel_type'      => 'prestashop',
            'api_key'           => '93589512ab0501b6009f261280696657',
            'api_secret'        => 'a4712bfe91a9c36a78c0e9f0d3018ff8',
            'url'               => 'www.sweettoothrewards.com'                                                  
        );   
           
        return $channel;
    }
}
