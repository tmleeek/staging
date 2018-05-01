<?php

class MDN_AdvancedStock_Helper_Product_Image extends Mage_Core_Helper_Abstract {

    public function getProductImageUrl($product) {
        $imageUrl = '';

        if (is_numeric($product))
            $product = Mage::getModel('catalog/product')->load($product);


        if (($product->getsmall_image()) && ($product->getsmall_image() != 'no_selection')) {
            $imageUrl = Mage::getBaseUrl('media') . DS . 'catalog' . DS . 'product' . $product->getsmall_image();
        } else {
            //try to find image from configurable product
            $configurableProduct = Mage::helper('AdvancedStock/Product_ConfigurableAttributes')->getConfigurableProduct($product->getId());
            if ($configurableProduct)
                if (($configurableProduct->getsmall_image()) && ($configurableProduct->getsmall_image() != 'no_selection')) {
                    if ($configurableProduct->getSmallImage()) {
                        $imageUrl = Mage::getBaseUrl('media') . DS . 'catalog' . DS . 'product' . $configurableProduct->getSmallImage();
                    }
                }
        }

        return $imageUrl;
    }

}
