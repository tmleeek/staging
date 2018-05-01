<?php

/**
 *
 */
class SweetToothOrder
{
    private $prefix = '/story';
    private $client;

    public function __construct($client) {
        $this->client = $client;
    }

    public function create($fields){
        return $this->mockOrder();
    }
    
    public function update($fields){
        return $this->mockOrder();
    }
    
    public function searchOne($filters) {
        return $this->mockOrder();       
    }

    public function __destruct(){
        unset($this->prefix);
        unset($this->array);
    }
    
    private function mockOrder()
    {
        $story = array (
            'user_id'          => 11,
            'order_id'         => 43,
            'channel'          => 'prestashop',
            'channel_user_id'  => 35,
            'channel_order_id' => 53,
            'base_subtotal'    => 343,
            'status'           => 'new',
            'created_at'       => '2011-11-15 23:19:55',
            'updated_at'       => '2011-11-15 23:19:55'
        );
                                                                                           
        $result = $this->client->arrayToObject($story);
        return $result;                
    }
}
