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
class MDN_SalesOrderPlanning_Block_Planning_Cart extends Mage_Checkout_Block_Cart_Abstract
{

    private $_planning = null;

    /*****/
    protected function _construct()
    {
        parent::_construct();

        //store anounced date in quote
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote)
        {
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
    public function getDeliveryMsg()
    {

    /*echo "<pre>";
            print_r($this->getPlanning()->getData());
            echo "</pre>";*/
           /* echo $this->getPlanning()->getpsop_anounced_date();
            echo "<br /><br />";
            echo $this->getPlanning()->getpsop_anounced_date_max();
            echo "<br /><br />";*/
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


    public function getDeliveryMsgforcheckout()
    {

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
    public function getPlanning()
    {
        if ($this->_planning == null)
        {
            $quote = $this->getQuote();

            if($this->getQuote()->getShippingAddress()->getShippingMethod()=='' || $this->getQuote()->getShippingAddress()->getShippingMethod()=='domicile_fr')
            {
                $store_name = Mage::app()->getStore()->getName();
                $subotal_inc_vat = $quote->getSubtotalWithDiscount();
                $address = $quote->getShippingAddress();

                $free_amount = '';
                $free_shipping = '';
                $paid_shipping = '';
                $default_shipping = '';

                /*echo "store_name =".$store_name;
                echo "<br /><br />";*/
                if($store_name == "France")
                {
                    $free_amount = '99';
                    $free_shipping = "socolissimo_domicile_sign_fr";
                    $paid_shipping = "socolissimo_domicile_fr";
                }
                elseif($store_name == "USA & World")
                {
                   $free_amount = '299';
                   $free_shipping = "socolissimo_dhl_express_zone5_free";
                   $paid_shipping = "socolissimo_dhl_express_zone5";
                }
                elseif($store_name == "United Kingdom")
                {
                    $free_amount = '199';
                    $free_shipping = "socolissimo_dhl_eco_zone1_free";
                    $paid_shipping = "socolissimo_dhl_eco_zone1";
                }
                elseif($store_name == "Suisse (FR)")
                {
                    $free_amount = '129';
                    $free_shipping = "socolissimo_colissimo_ch_free";
                    $paid_shipping = "socolissimo_colissimo_ch";
                }

                if($subotal_inc_vat > $free_amount)
                    $default_shipping = $free_shipping;
                else
                    $default_shipping = $paid_shipping;

                /*echo "shipping_method =".$default_shipping;
                echo "<br /><br />";*/

               $address->setShippingMethod($default_shipping);
            }
            $this->_planning = mage::helper('SalesOrderPlanning/Planning')->getEstimationForQuote($quote);

        //exit;
        }
        return $this->_planning;
    }

    /**
     * Return comments
     *
     * @return unknown
     */
    public function getComments()
    {
        $retour = '';

        $retour.= $this->getPlanning()->getpsop_consideration_comments() . '<br>';
        $retour.= $this->getPlanning()->getpsop_fullstock_comments() . '<br>';
        $retour.= $this->getPlanning()->getpsop_shipping_comments() . '<br>';
        $retour.= $this->getPlanning()->getpsop_delivery_comments() . '<br>';

        return $retour;
    }

}