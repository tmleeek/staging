<?php

class MDN_Mpm_Model_Export_Product extends Mage_Core_Model_Abstract
{

    protected $_categoryNames = array();
    protected $_channels = null;

    protected $_attributeSetNames = null;

    public function updateProduct($productId)
    {
        $this->_channels = Mage::helper('Mpm/Carl')->getChannelsSubscribed();

        $product = Mage::getModel('catalog/product')->load($productId);
        if($product->getSku()) {
            $productData = $this->getProductData($product);
            Mage::helper('Mpm/Carl')->updateProduct($productData, $product->getSku());
            return true;
        }

        return false;
    }

    protected function getProductData($product)
    {
        $productData = array();

        $productData['stock'] = $this->getStocks($product);
        $productData['attributes']['global'] = $this->getAttributes($product);

        return $productData;
    }

    protected function getStocks($product)
    {
        $stockData = array();

        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
        foreach($stockItem->getData() as $k => $v) {
            if ($this->isExceptionAttribute($k)) {
                continue;
            }
            $stockData[$k] = $v;
        }

        return $stockData;
    }

    protected function getAttributes($product)
    {
        $attributes = array();

        foreach($product->getData() as $k => $v) {
            if ($this->isExceptionAttribute($k)) {
                continue;
            }

            if ((!is_object($v)) && (!is_array($v)) && ($v !== '')) {
                $value = $v;
                $attributeValue = '';
                if ($product->getResource()->getAttribute($k))
                    $attributeValue = $product->getAttributeText($k);
                if ($attributeValue)
                    $value = $attributeValue;

                if (in_array($k, array('image', 'small_image', 'base_image'))) {
                    if ($value)
                        $value = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product/'.$value;
                }

                if ((!empty($value)) && (!$value == '') && ($value !== null)) {
                    $attributes[$k] = $value;
                }
            }
        }

        // Channels attributes
        foreach($this->_channels as $channel) {
            $reference = Mage::helper('Mpm/MarketPlace')->getMpReference($channel->channelCode, $product);
            if ($reference) {
                $attributes['carl'][$channel->channelCode] = $reference;
            }
        }

        $attributes['attribute_set_name'] = $this->getAttributeSetName($product);

        //add category
        $category = $this->getCategoryPath($product);
        if ($category) {
            $attributes['category'] = $category;
        }

        return $attributes;
    }

    protected function getAttributeSetName($product)
    {
        if ($this->_attributeSetNames === null) {
            $this->_attributeSetNames = array();
            $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection') ->load();
            foreach($attributeSetCollection as $id => $item) {
                $this->_attributeSetNames[$id] = $item->getAttributeSetName();
            }
        }

        return $this->_attributeSetNames[$product->getattribute_set_id()];
    }

    protected function getCategoryPath($product)
    {
        $paths = array();
        $collection = $product->getCategoryCollection();

        $selectedCategory = null;
        foreach($collection as $category)
        {
            if ($selectedCategory == null)
                $selectedCategory = $category;
            else
            {
                if (strlen($selectedCategory->getPath()) < strlen($category->getPath))
                    $selectedCategory = $category;
            }
        }

        if (!$selectedCategory)
            return '';


        $path = $category->getPath();
        $pathItems = explode('/', $path);
        foreach($pathItems as $pathItem)
        {
            if ($pathItem == 1)
                continue;
            if (!isset($this->_categoryNames[$pathItem]))
            {
                $cat = Mage::getModel('catalog/category')->load($pathItem);
                $this->_categoryNames[$pathItem] = $cat->getName();
            }
            $paths[] = $this->_categoryNames[$pathItem];
        }

        return implode(' > ', $paths);
    }

    protected function isExceptionAttribute($k)
    {
        $exceptions = array(
            'entity_type_id',
            'type_id',
            'tier_price_changed',
            'group_price_changed',
            'has_options',
            'required_options',
            'is_recurring',
            'custom_design',
            'page_layout',
            'options_container',
            'custom_design_from',
            'custom_design_to',
            'custom_layout_update',
            'item_id',
            'product_id',
            'pricer_debug_information',
            'use_config_min_qty',
            'is_qty_decimal',
            'stock_status_changed_auto',
            'use_config_qty_increments',
            'qty_increments',
            'is_decimal_divided',
            'stock_status_changed_automatically'
        );

        return in_array($k, $exceptions);
    }
}