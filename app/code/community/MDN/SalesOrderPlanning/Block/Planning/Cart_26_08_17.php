<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_SalesOrderPlanning_Block_Planning_Cart extends Mage_Checkout_Block_Cart_Abstract {

    private $_planning = null;

    /**
     * 
     *
     */
    protected function _construct() {
        parent::_construct();

        //store anounced date in quote
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote) {
            $planning = $this->getPlanning();

            $quote->setanounced_date($this->getPlanning()->getpsop_anounced_date());
            $quote->setanounced_date_max($this->getPlanning()->getpsop_anounced_date_max());
            $quote->save();
        }
    }

    /**
     * Return delivery msg
     *
     */
    public function getDeliveryMsg() {

        $deliveryDate = mage::helper('core')->formatDate($this->getPlanning()->getpsop_anounced_date(), 'medium');
        $deliveryMaxDate = mage::helper('core')->formatDate($this->getPlanning()->getpsop_anounced_date_max(), 'medium');

        //define message
        $retour = "<div class='discounttextnew'>";
        $retour .= mage::helper('SalesOrderPlanning')->__('Your order will be delivered on the <b>%s</b> or before', $deliveryDate);
        //$retour .= "<br>" . $this->__('Your Order will be Shipped on the <b>%s</b>', $deliveryMaxDate);
        if (false)
            $retour .= "<br>" . mage::helper('SalesOrderPlanning')->__('Those information implies we receive you payment under %s days', $paymentMethodDelay);
        $retour .= "</div>";

        return $retour;
    }


    public function getDeliveryMsgforcheckout() {

        $deliveryDate = mage::helper('core')->formatDate($this->getPlanning()->getpsop_anounced_date(), 'medium');
        $deliveryMaxDate = mage::helper('core')->formatDate($this->getPlanning()->getpsop_anounced_date_max(), 'medium');

        //define message

        $retour .=$deliveryDate;


        return $retour;
    }
    /**
     * Return planning object
     *
     * @return unknown
     */
    public function getPlanning() {
        if ($this->_planning == null) {
            $quote = $this->getQuote();
            if($this->getQuote()->getShippingAddress()->getShippingMethod()=='')
            {
               $address = $quote->getShippingAddress();
               $address->setShippingMethod('colissimo_colissimo');
            }
            $this->_planning = mage::helper('SalesOrderPlanning/Planning')->getEstimationForQuote($quote);
        }
        return $this->_planning;
    }

    /**
     * Return comments
     *
     * @return unknown
     */
    public function getComments() {
        $retour = '';

        $retour.= $this->getPlanning()->getpsop_consideration_comments() . '<br>';
        $retour.= $this->getPlanning()->getpsop_fullstock_comments() . '<br>';
        $retour.= $this->getPlanning()->getpsop_shipping_comments() . '<br>';
        $retour.= $this->getPlanning()->getpsop_delivery_comments() . '<br>';

        return $retour;
    }

}