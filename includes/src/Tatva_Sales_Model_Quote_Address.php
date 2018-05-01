<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Quote address model
 *
 * @method Mage_Sales_Model_Resource_Quote_Address _getResource()
 * @method Mage_Sales_Model_Resource_Quote_Address getResource()
 * @method int getQuoteId()
 * @method Mage_Sales_Model_Quote_Address setQuoteId(int $value)
 * @method string getCreatedAt()
 * @method Mage_Sales_Model_Quote_Address setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Mage_Sales_Model_Quote_Address setUpdatedAt(string $value)
 * @method int getCustomerId()
 * @method Mage_Sales_Model_Quote_Address setCustomerId(int $value)
 * @method int getSaveInAddressBook()
 * @method Mage_Sales_Model_Quote_Address setSaveInAddressBook(int $value)
 * @method int getCustomerAddressId()
 * @method Mage_Sales_Model_Quote_Address setCustomerAddressId(int $value)
 * @method string getAddressType()
 * @method Mage_Sales_Model_Quote_Address setAddressType(string $value)
 * @method string getEmail()
 * @method Mage_Sales_Model_Quote_Address setEmail(string $value)
 * @method string getPrefix()
 * @method Mage_Sales_Model_Quote_Address setPrefix(string $value)
 * @method string getFirstname()
 * @method Mage_Sales_Model_Quote_Address setFirstname(string $value)
 * @method string getMiddlename()
 * @method Mage_Sales_Model_Quote_Address setMiddlename(string $value)
 * @method string getLastname()
 * @method Mage_Sales_Model_Quote_Address setLastname(string $value)
 * @method string getSuffix()
 * @method Mage_Sales_Model_Quote_Address setSuffix(string $value)
 * @method string getCompany()
 * @method Mage_Sales_Model_Quote_Address setCompany(string $value)
 * @method string getCity()
 * @method Mage_Sales_Model_Quote_Address setCity(string $value)
 * @method Mage_Sales_Model_Quote_Address setRegion(string $value)
 * @method Mage_Sales_Model_Quote_Address setRegionId(int $value)
 * @method string getPostcode()
 * @method Mage_Sales_Model_Quote_Address setPostcode(string $value)
 * @method string getCountryId()
 * @method Mage_Sales_Model_Quote_Address setCountryId(string $value)
 * @method string getTelephone()
 * @method Mage_Sales_Model_Quote_Address setTelephone(string $value)
 * @method string getFax()
 * @method Mage_Sales_Model_Quote_Address setFax(string $value)
 * @method int getSameAsBilling()
 * @method Mage_Sales_Model_Quote_Address setSameAsBilling(int $value)
 * @method int getFreeShipping()
 * @method Mage_Sales_Model_Quote_Address setFreeShipping(int $value)
 * @method int getCollectShippingRates()
 * @method Mage_Sales_Model_Quote_Address setCollectShippingRates(int $value)
 * @method string getShippingMethod()
 * @method Mage_Sales_Model_Quote_Address setShippingMethod(string $value)
 * @method string getShippingDescription()
 * @method Mage_Sales_Model_Quote_Address setShippingDescription(string $value)
 * @method float getWeight()
 * @method Mage_Sales_Model_Quote_Address setWeight(float $value)
 * @method float getSubtotal()
 * @method Mage_Sales_Model_Quote_Address setSubtotal(float $value)
 * @method float getBaseSubtotal()
 * @method Mage_Sales_Model_Quote_Address setBaseSubtotal(float $value)
 * @method Mage_Sales_Model_Quote_Address setSubtotalWithDiscount(float $value)
 * @method Mage_Sales_Model_Quote_Address setBaseSubtotalWithDiscount(float $value)
 * @method float getTaxAmount()
 * @method Mage_Sales_Model_Quote_Address setTaxAmount(float $value)
 * @method float getBaseTaxAmount()
 * @method Mage_Sales_Model_Quote_Address setBaseTaxAmount(float $value)
 * @method float getShippingAmount()
 * @method float getBaseShippingAmount()
 * @method float getShippingTaxAmount()
 * @method Mage_Sales_Model_Quote_Address setShippingTaxAmount(float $value)
 * @method float getBaseShippingTaxAmount()
 * @method Mage_Sales_Model_Quote_Address setBaseShippingTaxAmount(float $value)
 * @method float getDiscountAmount()
 * @method Mage_Sales_Model_Quote_Address setDiscountAmount(float $value)
 * @method float getBaseDiscountAmount()
 * @method Mage_Sales_Model_Quote_Address setBaseDiscountAmount(float $value)
 * @method float getGrandTotal()
 * @method Mage_Sales_Model_Quote_Address setGrandTotal(float $value)
 * @method float getBaseGrandTotal()
 * @method Mage_Sales_Model_Quote_Address setBaseGrandTotal(float $value)
 * @method string getCustomerNotes()
 * @method Mage_Sales_Model_Quote_Address setCustomerNotes(string $value)
 * @method string getDiscountDescription()
 * @method Mage_Sales_Model_Quote_Address setDiscountDescription(string $value)
 * @method float getShippingDiscountAmount()
 * @method Mage_Sales_Model_Quote_Address setShippingDiscountAmount(float $value)
 * @method float getBaseShippingDiscountAmount()
 * @method Mage_Sales_Model_Quote_Address setBaseShippingDiscountAmount(float $value)
 * @method float getSubtotalInclTax()
 * @method Mage_Sales_Model_Quote_Address setSubtotalInclTax(float $value)
 * @method float getBaseSubtotalTotalInclTax()
 * @method Mage_Sales_Model_Quote_Address setBaseSubtotalTotalInclTax(float $value)
 * @method int getGiftMessageId()
 * @method Mage_Sales_Model_Quote_Address setGiftMessageId(int $value)
 * @method float getHiddenTaxAmount()
 * @method Mage_Sales_Model_Quote_Address setHiddenTaxAmount(float $value)
 * @method float getBaseHiddenTaxAmount()
 * @method Mage_Sales_Model_Quote_Address setBaseHiddenTaxAmount(float $value)
 * @method float getShippingHiddenTaxAmount()
 * @method Mage_Sales_Model_Quote_Address setShippingHiddenTaxAmount(float $value)
 * @method float getBaseShippingHiddenTaxAmount()
 * @method Mage_Sales_Model_Quote_Address setBaseShippingHiddenTaxAmount(float $value)
 * @method float getShippingInclTax()
 * @method Mage_Sales_Model_Quote_Address setShippingInclTax(float $value)
 * @method float getBaseShippingInclTax()
 * @method Mage_Sales_Model_Quote_Address setBaseShippingInclTax(float $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tatva_Sales_Model_Quote_Address extends Mage_Sales_Model_Quote_Address
{
    /**
     * Collecting shipping rates by address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    

    /**
     * Request shipping rates for entire address or specified address item
     * Returns true if current selected shipping method code corresponds to one of the found rates
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return bool
     */
    
	
	public function requestShippingRates(Mage_Sales_Model_Quote_Item_Abstract $item = null)
    { 
        /** @var $request Mage_Shipping_Model_Rate_Request */
        $request = Mage::getModel('shipping/rate_request');
        $request->setAllItems($item ? array($item) : $this->getAllItems());
        $request->setDestCountryId($this->getCountryId());
        $request->setDestRegionId($this->getRegionId());
        $request->setDestRegionCode($this->getRegionCode());
        /**
         * need to call getStreet with -1
         * to get data in string instead of array
         */
        $request->setDestStreet($this->getStreet(-1));
        $request->setDestCity($this->getCity());
        $request->setDestPostcode($this->getPostcode());
        $request->setPackageValue($item ? $item->getBaseRowTotal() : $this->getBaseSubtotal());
        $packageValueWithDiscount = $item
            ? $item->getBaseRowTotal() - $item->getBaseDiscountAmount()
            : $this->getBaseSubtotalWithDiscount();
        $request->setPackageValueWithDiscount($packageValueWithDiscount);
        $request->setPackageWeight($item ? $item->getRowWeight() : $this->getWeight());
        $request->setPackageQty($item ? $item->getQty() : $this->getItemQty());

        /**
         * Need for shipping methods that use insurance based on price of physical products
         */
        $packagePhysicalValue = $item
            ? $item->getBaseRowTotal()
            : $this->getBaseSubtotal() - $this->getBaseVirtualAmount();
        $request->setPackagePhysicalValue($packagePhysicalValue);

        $request->setFreeMethodWeight($item ? 0 : $this->getFreeMethodWeight());

        /**
         * Store and website identifiers need specify from quote
         */
        /*$request->setStoreId(Mage::app()->getStore()->getId());
        $request->setWebsiteId(Mage::app()->getStore()->getWebsiteId());*/

        $request->setStoreId($this->getQuote()->getStore()->getId());
        $request->setWebsiteId($this->getQuote()->getStore()->getWebsiteId());
        $request->setFreeShipping($this->getFreeShipping());
        /**
         * Currencies need to convert in free shipping
         */
        $request->setBaseCurrency($this->getQuote()->getStore()->getBaseCurrency());
        $request->setPackageCurrency($this->getQuote()->getStore()->getCurrentCurrency());
        $request->setLimitCarrier($this->getLimitCarrier());
		$request->setCartTotal($this->getSubtotalWithDiscount()+$this->getTaxAmount());
		

        $result = Mage::getModel('shipping/shipping')->collectRates($request)->getResult();

        $found = false;
        if ($result) {
            $shippingRates = $result->getAllRates();

            foreach ($shippingRates as $shippingRate) {
                $rate = Mage::getModel('sales/quote_address_rate')
                    ->importShippingRate($shippingRate);
                if (!$item) {
                    $this->addShippingRate($rate);
                }

                if ($this->getShippingMethod() == $rate->getCode()) {
                    if ($item) {
                        $item->setBaseShippingAmount($rate->getPrice());
                    } else {
                        /**
                         * possible bug: this should be setBaseShippingAmount(),
                         * see Mage_Sales_Model_Quote_Address_Total_Shipping::collect()
                         * where this value is set again from the current specified rate price
                         * (looks like a workaround for this bug)
                         */
                        $this->setShippingAmount($rate->getPrice());
                    }

                    $found = true;
                }
            }
        }
        return $found;
    }
}
