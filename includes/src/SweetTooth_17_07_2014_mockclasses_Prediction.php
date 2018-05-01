<?php

/**
 *
 */
class SweetToothPrediction
{
    private $prefix = '/prediction';
    private $client;

    public function __construct($client) 
    {
        $this->client = $client;
    }

    public function get($type, $fields)
    {
        $result = $this->_mockPrediction();
        return $this->client->prepareResponse($result);
    }

    public function __destruct()
    {
        unset($this->prefix);
        unset($this->array);
    }
    
    protected function _mockPrediction()
    {
        $prediction = array (
           'points'  => array (
               array (
                        'currency_id'            => 1,
                        'earned'                 => 200,
                        'pending_earned'         => 0                                 
                )                                
            )                                
        );
        
        return $prediction;                
    }
    
}
