<?php

class MDN_Mpm_Helper_Shipping extends Mage_Core_Helper_Abstract
{

    public function getRate($product, $currency, $carrierMethod, $countryCode)
    {
        $request = $this->buildRequest($product, $currency, $countryCode);
        $rates = Mage::getModel('shipping/shipping')->collectRates($request);

        $amount = 0;
        foreach($rates->getResult()->getAllRates() as $rate)
        {
            if ($rate->getMethod() == $carrierMethod)
            {
                $amount = $rate->getprice();
                break;
            }
        }


        return $amount;
    }

    protected function buildRequest($product, $currency, $countryCode)
    {
        $websiteId = 1;
        $price = $product->getPrice();

        $item = Mage::getModel('sales/quote_item');
        $item->setProduct($product);
        $addressItems = array();
        $addressItems[] = $item;

        /** @var $request Mage_Shipping_Model_Rate_Request */
        $request = Mage::getModel('shipping/rate_request');
        $request->setAllItems($addressItems);
        $request->setDestCountryId($countryCode);
        $request->setPackageValue($price);
        $request->setPackageValueWithDiscount($price);
        $request->setPackageWeight($product->getWeight());
        $request->setFreeMethodWeight($product->getWeight());
        $request->setPackageQty(1);
        $request->setWebsiteId($websiteId);
        $request->setBaseCurrency($currency);
        $request->setPackageCurrency($currency);


        $request->setBaseSubtotalInclTax($price);

        return $request;
    }

}
