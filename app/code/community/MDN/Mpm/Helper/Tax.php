<?php

class MDN_Mpm_Helper_Tax extends Mage_Core_Helper_Abstract
{


    public function getProductTaxRate($product, $storeId, $countryCode, $region, $postCode)
    {
        $store = Mage::app()->getStore($storeId);
        $customerTaxClassId = 3;    //todo : use variable
        $productTaxId = $product->gettax_class_id();

        $TaxRequest  = new Varien_Object();
        $TaxRequest->setCountryId( $countryCode );
        $TaxRequest->setRegionId($region);
        $TaxRequest->setPostcode($postCode);
        $TaxRequest->setStore($store);
        $TaxRequest->setCustomerClassId( $customerTaxClassId );
        $TaxRequest->setProductClassId($productTaxId);

        $taxCalculationModel = Mage::getSingleton('tax/calculation');
        $rate = $taxCalculationModel->getRate($TaxRequest);

        return $rate;
    }

}