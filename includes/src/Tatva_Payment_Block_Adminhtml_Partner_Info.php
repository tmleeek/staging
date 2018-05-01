<?php
/**
 * created : 30 sept. 2009
 *  
 * updated by <user> : <date>
 * Description of the update
 * 
 * @category SQLI
 * @package Sqli_Payment
 * @author alay
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * 
 * @package Sqli_Payment
 */
class Tatva_Payment_Block_Adminhtml_Partner_Info extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/info/partner.phtml');
    }
    
    /**
     * Retrieves order partner reference
     *
     */
    public function getPartner(){
    	return $this->getInfo()->getData('marketplaces_partner_code');
    }
    
    /**
     * Retrieves order partner date
     *
     */
    public function getOrderDatePartner(){
    	//return $this->getOrderDatePartnerFormated()->getTimezone();
    	return $this->getInfo()->getData('marketplaces_partner_date');
    }
    
    private function getOrderDatePartnerFormated()
    {
    	$date = $this->getInfo()->getData('marketplaces_partner_date');
        return Mage::helper('core')->formatDate($date, 'medium');
    }
    
    /**
     * Retrieves order partner
     *
     */
    public function getOrderPartner(){
    	return $this->getInfo()->getData('marketplaces_partner_order');
    }
}