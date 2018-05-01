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
class MDN_Orderpreparation_Helper_SpecificCarrierTemplates_Abstract extends Mage_Core_Helper_Abstract {

    public function createExportFile($orderPreparationCollection) {
        throw new Exception('Implement export file for specific carrier template');
    }

    public function importTrackingFile($t_lines) {
        throw new Exception('Implement import file for specific carrier template');
    }

    protected function getAddress($order) {
        $address = $order->getShippingAddress();
        if (!$order->getShippingAddress())
            $address = $order->getBillingAddress();
        return $address;
    }

}