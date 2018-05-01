<?php
namespace Mpm\GatewayClient\Client;

use Mpm\GatewayClient\Client;

include_once(dirname(__FILE__).DS.'..'.DS.'Client.php');

class Transaction extends Client
{
    public static function getInstance()
    {
        if (self::$_instance === null || false === (self::$_instance instanceof Transaction)) {
            self::$_instance = new Transaction();
        }
        return self::$_instance;
    }

    public function listTransactions($queryParams = array())
    {

        $result = $this->get('/transaction', $queryParams);
        $result = json_decode($result);
        $result = $result->body->results;

        usort($result, array("self", "sortByDate"));

        return $result;
    }

    public function getTransactionReport($transactionId)
    {
        return $this->get('/transaction/'.$transactionId.'/report');
    }
    
    public function getTransactionFile($transactionId)
    {
        return $this->get('/transaction/'.$transactionId.'/file');
    }

    public static function sortByDate($a, $b)
    {

        $al = strtolower($a->created_at);
        $bl = strtolower($b->created_at);
        if ($al == $bl) {
            return 0;
        }
        return -(($al > $bl) ? +1 : -1);
    }
}