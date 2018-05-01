<?php

class MDN_SalesOrderPlanning_Block_Front_Catalog_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable {

    /**
     * Overwrite getjsonconfig to add stock status information for each sub product (to display dynamic stock status)
     *
     * @return unknown
     */
    //todo: externalize to SalesOrderPlanning
    public function getJsonConfig() {
        $attributes = array();
        $options = array();
        $subProductsAvailability = array();
        $store = Mage::app()->getStore();
        foreach ($this->getAllowProducts() as $product) {
            $productId = $product->getId();

            //add sub product availability information
            $productAvailabilityStatus = mage::getModel('SalesOrderPlanning/ProductAvailabilityStatus')->load($productId, 'pa_product_id');
            $subProductInfo = array();
            $subProductInfo['id'] = $productId;
            if ($productAvailabilityStatus->getId())
                $subProductInfo['availability'] = $productAvailabilityStatus->getMessage();
            else
                $subProductInfo['availability'] = $this->__('No information available');
            $subProductsAvailability[] = $subProductInfo;

            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttribute->getId()])) {
                    $options[$productAttribute->getId()] = array();
                }

                if (!isset($options[$productAttribute->getId()][$attributeValue])) {
                    $options[$productAttribute->getId()][$attributeValue] = array();
                }
                $options[$productAttribute->getId()][$attributeValue][] = $productId;
            }
        }

        $this->_resPrices = array(
            $this->_preparePrice($this->getProduct()->getFinalPrice())
        );

        foreach ($this->getAllowAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $info = array(
                'id' => $productAttribute->getId(),
                'code' => $productAttribute->getAttributeCode(),
                'label' => $attribute->getLabel(),
                'options' => array()
            );

            $optionPrices = array();
            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if (!$this->_validateAttributeValue($attributeId, $value, $options)) {
                        continue;
                    }

                    $info['options'][] = array(
                        'id' => $value['value_index'],
                        'label' => $value['label'],
                        'price' => $this->_preparePrice($value['pricing_value'], $value['is_percent']),
                        'oldPrice'  => $this->_preparePrice($value['pricing_value'], $value['is_percent']),
                        'products' => isset($options[$attributeId][$value['value_index']]) ? $options[$attributeId][$value['value_index']] : array(),
                    );
                    $optionPrices[] = $this->_preparePrice($value['pricing_value'], $value['is_percent']);
                    //$this->_registerAdditionalJsPrice($value['pricing_value'], $value['is_percent']);
                }
            }
            /**
             * Prepare formated values for options choose
             */
            foreach ($optionPrices as $optionPrice) {
                foreach ($optionPrices as $additional) {
                    $this->_preparePrice(abs($additional - $optionPrice));
                }
            }
            if ($this->_validateAttributeInfo($info)) {
                $attributes[$attributeId] = $info;
            }
        }

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $taxConfig = array(
            'includeTax' => Mage::helper('tax')->priceIncludesTax(),
            'showIncludeTax' => Mage::helper('tax')->displayPriceIncludingTax(),
            'showBothPrices' => Mage::helper('tax')->displayBothPrices(),
            'defaultTax' => $defaultTax,
            'currentTax' => $currentTax,
            'inclTaxTitle' => Mage::helper('catalog')->__('Incl. Tax'),
        );

        $config = array(
            'attributes' => $attributes,
            'template' => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
//            'prices'          => $this->_prices,
            'basePrice' => $this->_registerJsPrice($this->_convertPrice($this->getProduct()->getFinalPrice())),
            'oldPrice' => $this->_registerJsPrice($this->_convertPrice($this->getProduct()->getPrice())),
            'productId' => $this->getProduct()->getId(),
            'chooseText' => Mage::helper('catalog')->__('Choose option...'),
            'taxConfig' => $taxConfig,
            'subProductsAvailability' => $subProductsAvailability
        );

        return Zend_Json::encode($config);
    }

}