<?php
/**
 * created : 9 septembre 2009
 * Retrait shipping model
 * @category SQLI
 * @package Sqli_Shipping
 * @author alay
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * 
 * @package Sqli_Shipping
 */
class Tatva_Shipping_Model_Carrier_Retrait
    extends Tatva_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'retrait';
	//protected $_picture = 'img/logo_enlevement.png';
    protected $_picture = 'images/logo_little_enlevement.png';
	protected $_littlepicture = 'images/logo_little_enlevement.png';
	protected $_formBlockType = 'tatvashipping/carrier_form_retrait';

    /**
     * Calcule les frais de port
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
     	$result = Mage::getModel('shipping/rate_result');
    	$method = Mage::getModel('shipping/rate_result_method');
    		$method->setCarrier('retrait');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('retrait');
            $method->setMethodTitle($this->getConfigData('title'));
            
            //REG PC-422
            $method->setPrice(0);
            $method->setCost(0);

            $result->append($method);
            
    	return $result;   	
    }

    public function getAllowedMethods()
    {
        return array('retrait'=>$this->getConfigData('name'));
    }
    
 	public function getDiscountAmount($request){
    	return 0;
    }
}
