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
class MDN_Orderpreparation_Helper_CarrierTemplate extends Mage_Core_Helper_Abstract {

    /**
     * Return template object for carrier
     *
     * @param unknown_type $carrier
     */
    public function getTemplateForCarrier($shippingMethod) {
        $obj = mage::getModel('Orderpreparation/CarrierTemplate')->load($shippingMethod, 'ct_shipping_method');
        if ($obj->getId())
        {
            $this->log('Carrier template for shipping method '.$shippingMethod.' is #'.$obj->getId());
            return $obj;
        }
        else
        {
            $this->log('Carrier template for shipping method '.$shippingMethod.' cant be found');
            return null;
        }
    }

    /**
     * Return matching carrier template for 1 order
     *
     * @param unknown_type $order
     */
    public function getTemplateForOrder($order) {
        $shippingMethod = $order->getshipping_method();
        $t = explode('_', $shippingMethod);
        return $this->getTemplateForCarrier($t[0]);
    }

    public function getTypes() {
        $retour = array();

        $retour['manual'] = $this->__('manual');
        $retour['UpsWorldship'] = $this->__('Ups Worldship');
        $retour['MondialRelay'] = $this->__('Mondial Relay');
        $retour['FedexShipManager'] = $this->__('Fedex Ship Manager');
        $retour['Exaprint'] = $this->__('Exaprint');

        return $retour;
    }
    
    /**
     * 
     * @param type $msg
     */
    protected function log($msg)
    {
        Mage::log($msg, null, 'erp_carrier_template.log');
    }

}