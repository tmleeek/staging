<?php

/**
 * Addonline
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline (http://www.addonline.fr)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Addonline_SoColissimo
 *
 * @category Addonline
 * @package Addonline_SoColissimo
 * @copyright Copyright (c) 2014 Addonline
 * @author Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Block_Selector extends Mage_Core_Block_Template
{

    /**
     * adresse de livraison
     */
    private function _getShippingAddress ()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
    }

    /**
     * mode de livraison
     *
     * @return string
     */
    public function getAddressShippingMethod ()
    {
        if ($adress = $this->_getShippingAddress()) {
            return $adress->getShippingMethod();
        } else {
            return '';
        }
    }

    /**
     * rue
     */
    public function getShippingStreet ()
    {
        return $this->_getShippingAddress()->getStreetFull();
    }

    /**
     * code postal
     */
    public function getShippingPostcode ()
    {
        return $this->_getShippingAddress()->getPostcode();
    }

    /**
     * ville
     */
    public function getShippingCity ()
    {
        return $this->_getShippingAddress()->getCity();
    }

    /**
     * pays
     */
    public function getShippingCountry ()
    {
        return $this->_getShippingAddress()->getCountry();
    }

    /**
     * telephone
     */
    public function getTelephone ()
    {
        return $this->_getShippingAddress()->getTelephone();
    }
    
    /*
     * (non-PHPdoc) @see Mage_Core_Block_Template::_toHtml()
     */
    protected function _toHtml ()
    {
    	MAge::log('block selector');
        $storeId = Mage::app()->getStore()->getStoreId();
        
        if (Mage::helper('addonline_licence')->_9cd4777ae76310fd6977a5c559c51820(
            (Mage::getModel('socolissimo/observer')), $storeId)) {
            return parent::_toHtml();
        } else {
            return "<H1>La clé de licence du module Colissimo est invalide</H1>";
        }
    }
}