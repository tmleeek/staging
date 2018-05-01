<?php

// Define constants for multi-server compatibility if they have not already been defined.
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if(!defined('PS')) define('PS', PATH_SEPARATOR);
if(!defined('BP')) define('BP', dirname(dirname(__FILE__)));

// Include REST library files:
include_once(dirname(__FILE__). DS .'pest'. DS .'PestJSON.php');

// Include core framework files:
include_once(dirname(__FILE__). DS .'etc'. DS .'ApiException.php');
include_once(dirname(__FILE__). DS .'etc'. DS .'Client.php');


class SweetTooth extends SweetToothClient
{
    /**
     * The directory where the resource classes live.
     * Relative to this file. This can be overridden 
     * if a developer wants to specify their own implementation
     * of the resources (like stub data).
     */
    protected $_classDir = 'classes';

    /**
     * Call this to validate API credentials.
     *
     * @throws Exception when connection to the API fails or the SweetTooth class is not ready
     * @return boolean true if connection is successful
     */
    public function authenticate()
    {
        $this->account()->get();
        return true;
    }
    
    /**
     * 
     * @return SweetToothAccount
     */
    public function account() {
        include_once(dirname(__FILE__). DS . $this->_classDir . DS .'Account.php');
        return new SweetToothAccount($this);
    }
    
    public function channel() {
        include_once(dirname(__FILE__). DS . $this->_classDir . DS .'Channel.php');
        return new SweetToothChannel($this);
    }

    /**
     *
     * @return SweetToothCart
     */
    public function cart() {
        include_once(dirname(__FILE__). DS . $this->_classDir . DS .'Cart.php');
        return new SweetToothCart($this);
    }

    /**
     * 
     * @return SweetToothRule
     */
    public function rule() {
        include_once(dirname(__FILE__). DS . $this->_classDir . DS .'Rule.php');
        return new SweetToothRule($this);
    }
    
    /**
     * 
     * @return SweetToothUser
     */
    public function user() {
        include_once(dirname(__FILE__). DS . $this->_classDir . DS .'User.php');
        return new SweetToothUser($this);
    }
    
    /**
     * 
     * @return SweetToothStory
     */
    public function story() {
        include_once(dirname(__FILE__). DS . $this->_classDir . DS .'Story.php');
        return new SweetToothStory($this);
    }
    
    /**
     * @return SweetToothOrder
     */
    public function order() {
        include_once(dirname(__FILE__). DS . $this->_classDir . DS .'Order.php');
        return new SweetToothOrder($this);
    }
    
    /**
     * 
     * @return SweetToothPrediction
     */
    public function prediction() {
        include_once(dirname(__FILE__). DS . $this->_classDir . DS .'Prediction.php');
        return new SweetToothPrediction($this);
    }
    
    /**
     * @return SweetToothCurrency
     */
    public function currency() {
        include_once(dirname(__FILE__). DS . $this->_classDir . DS .'Currency.php');
        return new SweetToothCurrency($this);
    }
    
    /**
    * @return SweetToothTransfer
    */
    public function transfer() {
        include_once(dirname(__FILE__). DS . $this->_classDir . DS .'Transfer.php');
        return new SweetToothTransfer($this);
    }
    
}




