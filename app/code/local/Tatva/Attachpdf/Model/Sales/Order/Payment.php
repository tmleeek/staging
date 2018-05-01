<?php
/**
 * created : 01 oct. 2009
 * Order payment information
 * 
 * updated by <user> : <date>
 * Description of the update
 * 
 * @category SQLI
 * @package Sqli_Sales
 * @author alay
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * 
 * @package Sqli_Sales
 */
class Tatva_Attachpdf_Model_Sales_Order_Payment extends Mage_Sales_Model_Order_Payment
{
 	/**
     * Import data
     *
     * @param   array $data
     * @return  Mage_Sales_Model_Quote_Payment
     */
    public function importData(array $data)
    {
        $data = new Varien_Object($data);
        $this->setMethod($data->getMethod());
        $method = $this->getMethodInstance();

        $method->assignData($data);
        /*
        * validating the payment data
        */
        //$method->validate();
        return $this;
    }
    
    
}