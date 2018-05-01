<?php
  
/**
 *
 */
class SweetToothCart
{
   private $prefix = '/cart';
   private $client;
  
   public function __construct($client) 
   {
       $this->client = $client;
   }
  
   public function create($fields)
   { 
       $result = $this->client->post($this->prefix, $fields);
       return $this->client->prepareResponse($result['cart']);
   }
  
   public function __destruct()
   {
       unset($this->prefix);
       unset($this->array);
   }   
}

