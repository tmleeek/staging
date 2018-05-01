<?php

class SweetToothCurrency
{
    private $prefix = '/currency';
    private $client;

    public function __construct($client) 
    {
        $this->client = $client;
    }

    public function get($id = null) 
    {
        if (!is_null($id)) {
            $result = $this->_mockCurrency($id);
        } else {
            $result = $this->_mockCurrency();
        }

        return $this->client->prepareResponse($result);
    }

    public function __destruct()
    {
        unset($this->prefix);
        unset($this->array);
    }
    
    protected function _mockCurrency($id = null) 
    {
        $currency = array (
            'currency'   => array (
                'currency_id'        => 1,
                'code'               => 'whitelabel',
                'caption_singular'   => 'Gold Point',
                'caption_plural'     => 'Gold Points',
                'caption_none'        => 'No Points'
            )
        ); 
        
        
        $currencies = array (
            'currencies' => array ( 
                $currency['currency']                                      
            )
        );
        
        
        if ($id) {            
            return $currency;
        } else {
            return $currencies;            
        }
    }       

}
