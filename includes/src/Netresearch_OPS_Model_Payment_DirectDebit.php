<?php
/**
 * Netresearch_OPS_Model_Payment_DirectDebit
 * 
 * @package   
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de> 
 * @license   OSL 3.0
 */
class Netresearch_OPS_Model_Payment_DirectDebit
    extends Netresearch_OPS_Model_Payment_Abstract
{
    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** Check if we can capture directly from the backend */
    protected $_canUseInternal = true;

    /** info source path */
    protected $_infoBlockType = 'ops/info_redirect';

    /* define a specific form block */
    protected $_formBlockType = 'ops/form_directDebit';

    /** payment code */
    protected $_code = 'ops_directDebit';

    public function getOrderPlaceRedirectUrl()
    {
        // Prevent redirect on direct debit payment
        return false; 
    }
}

