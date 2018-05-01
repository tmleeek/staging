<?php

/**
 * Description of the class
 * @package Socolissimo_Customer
 */
class Tatva_Customer_Model_Address extends Mage_Customer_Model_Address
{

    protected function _construct()
    {
        parent::_construct();

    }

    /**
     * Validate address attribute values
     *
     * @return bool
     */
    public function validate()
    {
        
            $errors = array();
            $helper = Mage::helper('customer');
            $this->implodeStreetAddress();

            echo "<pre>";
            print_r($this->getData());
            echo "</pre>";
            exit;
            if (!Zend_Validate::is($this->getFirstname(), 'NotEmpty')) {
                $errors[] = $helper->__('Please enter the first name.');
            }

            if (!Zend_Validate::is($this->getLastname(), 'NotEmpty')) {
                $errors[] = $helper->__('Please enter the last name.');
            }

            if (!Zend_Validate::is($this->getStreet(1), 'NotEmpty')) {
                $errors[] = $helper->__('Please enter the street.');
            }

            if (!Zend_Validate::is($this->getCity(), 'NotEmpty')) {
                $errors[] = $helper->__('Please enter the city.');
            }

            $_havingOptionalZip = Mage::helper('directory')->getCountriesWithOptionalZip();
            if (!in_array($this->getCountryId(), $_havingOptionalZip) && !Zend_Validate::is($this->getPostcode(), 'NotEmpty')) {
                $errors[] = $helper->__('Please enter the zip/postal code.');
            }

            if (!Zend_Validate::is($this->getCountryId(), 'NotEmpty')) {
                $errors[] = $helper->__('Please enter the country.');
            }

          /*  if ($this->getCountryModel()->getRegionCollection()->getSize()
                   && !Zend_Validate::is($this->getRegionId(), 'NotEmpty')) {
                $errors[] = $helper->__('Please enter the state/province.');
            }*/

    		if (!Zend_Validate::is($this->getPostcode(), 'NotEmpty'))
            {
                $errors[] = $helper->__('Please enter zip/postal code.');
            }
            else
            {
                if  ($this->getCountryId() == 'FR')
                {
                     if (strlen($this->getPostcode()) != 5 || strpos($this->getPostcode(),' ') > 0 )
                     {
                         $errors[] = $helper->__('Your zip code is incorrect. For France, the zip code must contain 5 numbers without space.');
                     }
                }
            }

            if (!Zend_Validate::is($this->getTelephone(), 'NotEmpty') && !Zend_Validate::is($this->getMobilephone(), 'NotEmpty') ) {
                $errors[] = $helper->__('Please enter the telephone number or a mobile number.');
            }

            if (Zend_Validate::is($this->getTelephone(), 'NotEmpty')) {
            	 if(!preg_match('/^(\+)([0-9])*$/',$this->getTelephone()) && !preg_match('/^([0-9])*$/',$this->getTelephone())){
            	 	$errors[] = $helper->__('The format of the telephone number is incorrect.');
            	 }
            }

            if  ($this->getCountryId() == 'FR'){
                if (Zend_Validate::is($this->getMobilephone(), 'NotEmpty')) {
            	    if(!preg_match('#^([0]{1}[67]{1}[0-9]{8})$#',$this->getMobilephone()) || !preg_match('#^[0-9]{10}+$#', $this->getMobilephone())){
            	 	    $errors[] = $helper->__('The mobile phone number you entered is incorrect. It must begin by 06 or 07 for France and be composed of 10 numbers. If you don\'t have a French mobile phone number, just indicate your phone number in the field telephone sets and let the mobile phone number field empty.');
            	    }
                }
            }
            else {
                if (Zend_Validate::is($this->getMobilephone(), 'NotEmpty')) {
            	    if(!preg_match('/^(\+)([0-9])*$/',$this->getMobilephone()) && !preg_match('/^([0-9])*$/',$this->getMobilephone())){
            	 	    $errors[] = $helper->__('The format of the mobile phone number is incorrect.');
            	    }
                }
            }

            if (empty($errors) || $this->getShouldIgnoreValidation()) {
                return true;
            }
            return $errors;
        }
        
}