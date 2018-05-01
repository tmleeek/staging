<?php
/**
 * Shipping.php
 * CommerceExtensions @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commerceextensions.com/LICENSE-M1.txt
 *

 * @category   Orders
 * @package    Shipping
 * @copyright  Copyright (c) 2003-2009 CommerceExtensions @ InterSEC Solutions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */ 
class Intersec_Orderimportexport_Model_Shipping extends Mage_Shipping_Model_Shipping
{
    public function getCarrierByCode($carrierCode, $storeId = null)
    {
        if ($carrierCode == 'imported') {
            $className = Mage::getStoreConfig('carriers/'.$carrierCode.'/model', $storeId);
            if (!$className) {
                return false;
            }
            $obj = Mage::getModel($className);
            if ($storeId) {
                $obj->setStore($storeId);
            }
            return $obj;
        } else {
            return parent::getCarrierByCode($carrierCode, $storeId);
        }
    }
}