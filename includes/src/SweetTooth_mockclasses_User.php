<?php
  
  
class SweetToothUser
{
    private $prefix = '/user';
    private $client;
  
    public function __construct($client) 
    {
        $this->client = $client;
    }
  
    public function get($id) 
    {
        if (!is_null($id)) {
            $result = $this->client->get($this->prefix . '/' . $id);
            return $this->client->prepareResponse($result);
        } else {
            return $this->search();
        }
    }
  
    public function search($filters = null) 
    {
        $result = $this->client->get($this->prefix . '/' . 'search', $filters);
        return $this->client->prepareResponse($result);
    }
  
    public function searchOne($filters) 
    {
        $result = $this->getMockSingleUser($filters);
        return $this->client->prepareResponse($result);
    }
  
    public function create($fields)
    {
        $result = $this->getMockSingleUser($fields);
        return $this->client->prepareResponse($result);
    }
  
    public function modify($id, $fields)
    {
        $fields = array('product' => $fields);
        return sendToAPI($this->prefix . 'products/' . $id, 'PUT', $fields);
    }
  
    public function remove($id)
    {
        return sendToAPI($this->prefix . 'products/'. $id, 'DELETE');
  
    }
  
    public function __destruct()
    {
        unset($this->prefix);
        unset($this->array);
    }
     
    private function getMockSingleUser($filters)
    {
       $points = array (
           array (
               'currency_id'       => 1,
               'available'         => 100,
               'earned'            => 50,
               'pending_earned'    => 25,
               'pending_spent'     => 5000,
               'spent'             => 34   
           ), 
           array (
               'currency_id'       => 2,
               'available'         => 200,
               'earned'            => 250,
               'pending_earned'    => 225,
               'pending_spent'     => 25000,
               'spent'             => 234
           )                          
       );
         
       $user = array (
           'user_id'           => 34,
           'channel_user_id'   => 525,
           'channel'           => 98,
           'email'             => 'fakecustomer@example.com',
           'firstname'         => 'fake',
           'lastname'          => 'customer',
           'points'            =>  $points,
           'created_at'        => '2011-11-15 23:19:55',
           'updated_at'        => '2011-11-15 23:19:55'              
       );
          
       return $user;
    }
     
}
