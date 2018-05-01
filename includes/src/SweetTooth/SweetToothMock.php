<?php

// Define constants for multi-server compatibility if they have not already been defined.
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if(!defined('PS')) define('PS', PATH_SEPARATOR);
if(!defined('BP')) define('BP', dirname(dirname(__FILE__)));

// Include REST library files:
include_once(dirname(__FILE__). DS .'pest'. DS .'PestJSON.php');

// Include core framework files:
include_once(dirname(__FILE__). DS .'etc'. DS .'ApiException.php');
include_once(dirname(__FILE__). DS .'SweetTooth.php');


class SweetToothMock extends SweetTooth
{
    /**
     * Override the directory where SweetTooth looks for its
     * resource classes. The mockclasses directory contains
     * classes that return stub data to remove the dependency
     * of an available platform for development purposes.
     */
    protected $_classDir = 'mockclasses';
}




