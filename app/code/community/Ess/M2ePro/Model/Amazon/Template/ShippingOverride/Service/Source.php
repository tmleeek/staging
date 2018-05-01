<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Amazon_Template_ShippingOverride_Service_Source
{
    /**
     * @var $magentoProduct Ess_M2ePro_Model_Magento_Product
     */
    private $magentoProduct = null;

    /**
     * @var $shippingOverrideServiceTemplateModel Ess_M2ePro_Model_Amazon_Template_ShippingOverride_Service
     */
    private $shippingOverrideServiceTemplateModel = null;

    //########################################

    /**
     * @param Ess_M2ePro_Model_Magento_Product $magentoProduct
     * @return $this
     */
    public function setMagentoProduct(Ess_M2ePro_Model_Magento_Product $magentoProduct)
    {
        $this->magentoProduct = $magentoProduct;
        return $this;
    }

    /**
     * @return Ess_M2ePro_Model_Magento_Product
     */
    public function getMagentoProduct()
    {
        return $this->magentoProduct;
    }

    // ---------------------------------------

    /**
     * @param Ess_M2ePro_Model_Amazon_Template_ShippingOverride_Service $instance
     * @return $this
     */
    public function setShippingOverrideServiceTemplate(
        Ess_M2ePro_Model_Amazon_Template_ShippingOverride_Service $instance)
    {
        $this->shippingOverrideServiceTemplateModel = $instance;
        return $this;
    }

    /**
     * @return Ess_M2ePro_Model_Amazon_Template_ShippingOverride_Service
     */
    public function getShippingOverrideServiceTemplate()
    {
        return $this->shippingOverrideServiceTemplateModel;
    }

    //########################################

    /**
     * @param null $storeForConvertingAttributeTypePrice
     * @return float
     */
    public function getCost($storeForConvertingAttributeTypePrice = NULL)
    {
        $result = 0;

        switch ($this->getShippingOverrideServiceTemplate()->getCostMode()) {
            case Ess_M2ePro_Model_Amazon_Template_ShippingOverride_Service::COST_MODE_FREE:
                $result = 0;
                break;
            case Ess_M2ePro_Model_Amazon_Template_ShippingOverride_Service::COST_MODE_CUSTOM_VALUE:
                $result = $this->getShippingOverrideServiceTemplate()->getCostValue();
                break;
            case Ess_M2ePro_Model_Amazon_Template_ShippingOverride_Service::COST_MODE_CUSTOM_ATTRIBUTE:
                $result = $this->getMagentoProductAttributeValue(
                    $this->getShippingOverrideServiceTemplate()->getCostValue(),
                    $storeForConvertingAttributeTypePrice
                );
                break;
        }

        is_string($result) && $result = str_replace(',','.',$result);

        return round((float)$result,2);
    }

    // ---------------------------------------

    protected function getMagentoProductAttributeValue($attributeCode, $store)
    {
        $attributeValue = $this->getMagentoProduct()->getAttributeValue($attributeCode);

        if (empty($attributeValue) || is_null($store)) {
            return $attributeValue;
        }

        $isPriceConvertEnabled = (int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue(
            '/magento/attribute/', 'price_type_converting'
        );

        if ($isPriceConvertEnabled &&
            Mage::helper('M2ePro/Magento_Attribute')->isAttributeInputTypePrice($attributeCode)) {

            $currency = $this->getShippingOverrideServiceTemplate()
                             ->getShippingOverrideTemplate()
                             ->getMarketplace()
                             ->getChildObject()
                             ->getCurrency();
            return Mage::getSingleton('M2ePro/Currency')->convertPrice($attributeValue, $currency, $store);
        }

        return $attributeValue;
    }

    //########################################
}