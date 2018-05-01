<?php

/**
 *
 */
class SweetToothCart
{
   private $prefix = '/st_cart';
   private $client;
 
   public function __construct($client) 
   {
       $this->client = $client;
   }
 
   public function create($fields)
   { 
       $result = $this->mockReturnCartInfo($fields);
       return $this->client->prepareResponse($result);
   }
 
   public function __destruct()
   {
       unset($this->prefix);
       unset($this->array);
   }
   
   public function mockReturnCartInfo($requestCartInfo)
   {
       if (!isset($requestCartInfo)) {
           return null;
       }
       
       
       if ((gettype($requestCartInfo) == 'array') && array_key_exists('cart', $requestCartInfo)) {
           $requestCartInfo = $requestCartInfo['cart'];
       }
       
       $cartInfo = array (
           'user_id' => 11,
           'channel_user_id' => $requestCartInfo['channel_user_id'],
           'base_subtotal' => $requestCartInfo['base_subtotal'],
           'status' => 'new',
           'channel' => 'prestashop',
           'items' => $requestCartInfo['items']
       );
   
       $cartInfo['applicable_rules']= array (
           array (
               'rule_id'            => 10,
               'type'               => 'fixed_discount',
               'uses_per_customer'  => 1,
               'points_currency_id' => 1,
               'points_amount'      => 1,
               'discount_amount'    => 2,
               'max_uses'           => 3
           ),
           array(
               'rule_id'            => 10,
               'type'               => 'variable_discount',
               'uses_per_customer'  => 1,
               'points_currency_id' => 1,
               'points_amount'      => 1,
               'discount_amount'    => 2,
               'max_uses'           => 3

           ),
           array(
               'rule_id'            => 1,
               'type'               => 'variable_discount',
               'uses_per_customer'  => 3,
               'points_currency_id' => 2,
               'points_amount'      => 5,
               'discount_amount'    => 2,
               'max_uses'           => 3
           
           )
       );
   
       $cartInfo['points_earned'] = array (
           array (
               'currency_id'    => 1,
               'earned'         => 10
           ),
           array (
               'currency_id'    => 2,
               'earned'         => 20
           )
       );
   
       $uses = 1;
       if ((gettype($requestCartInfo) == 'array') && array_key_exists('applied_rules', $requestCartInfo) && !is_null($requestCartInfo['applied_rules'])) {           
           $uses = array_pop($requestCartInfo['applied_rules']);
           $uses = $uses['uses'];
       }
       
       $cartInfo['rewards'] = array (
           array (
               'rule_id'        => 10,
               'type'           => 'percent_discount',
               'label'          => '10 points gives 10% off',
               'details'        => array (
                       'amount'        => 50 * $uses,
                       'points_spent'  => 40 * $uses
               )
           ),
           array (
               'rule_id'        => 20,
               'type'           => 'free_shipping',
               'label'          => 'Free shipping on orders over $50 for 10 points',
               'details'        => array (
                       'amount'          => 100 * $uses,
                       'points_spent'    => 10 * $uses
               )
           ),
           array (
               'rule_id'        => 1,
               'type'           => 'free_shipping',
               'label'          => 'Free shipping on orders over $50 for 10 points',
               'details'        => array (
                       'amount'          => 100 * $uses,
                       'points_spent'    => 10 * $uses
               )                       
           ),
   
       );
       $finalResult['cart'] = $cartInfo;
       
       return $finalResult;
   }
}

