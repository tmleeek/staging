<?php

/**
 *
 */
class SweetToothStory
{
    private $prefix = '/story';
    private $client;

    public function __construct($client) 
    {
        $this->client = $client;
    }

    public function get($fields)
    {

        return $this->mockStory();
    }
    
    public function searchOne($filters) 
    {
        return $this->mockStory();
        
    }

    public function __destruct()
    {
        unset($this->prefix);
        unset($this->array);
    }
    
    private function mockStory()
    {
        $story = array (
            'story_id'         => 1,
            'story_type'       => 'cartPurchase',
            'user_id'          => 11,
            'object_id'        => 11,
            'channel'          =>'prestashop',
            'channel_user_id'  => 35,
            'channel_object_id'=> 35,
            'points'           => array (
                array (
                    'currency_id'      => 1,
                    'earned'           => 100,
                    'pending_earned'   => 0,
                    'pending_spent'    => 0,
                    'spent'            => 0
                )
            ),
            'created_at'        => '2011-11-15 23:19:55',
            'updated_at'        => '2011-11-15 23:19:55'        
        );
                  
        $result = $this->client->arrayToObject($story);
        return $result;                
    }
}
