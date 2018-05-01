<?php
/**
 * *
 *  * Magento
 *  *
 *  * NOTICE OF LICENSE
 *  *
 *  * This source file is subject to the Open Software License (OSL 3.0)
 *  * It is also available through the world-wide-web at this URL:
 *  * http://opensource.org/licenses/osl-3.0.php
 *  *
 *  * @copyright  Copyright (c) 2015 Boostmyshop (http://www.boostmyshop.com)
 *  * @author : Robin CARABALONA
 *  * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

class MDN_Mpm_Helper_PricingImport extends Mage_Core_Helper_Abstract
{

    private $priceAttribute = array();
    private $shippingAttribute = array();
    private $storeId = array();

    public function importAll()
    {
        Mage::helper('Mpm')->log('Start pricing import', 'mpm_pricing_import.log');

        do {
            $pricingCollection = new MDN_Mpm_Model_LastPricingCollection();
            $pricingCollection->load();

            $countResults = count($pricingCollection);
            foreach($pricingCollection as $pricing) {
                if($pricing->status === 'error' || $pricing->final_price == 0) {
                    continue;
                }

                try
                {

                    $this->setPricing(
                        $pricing->getProductId(),
                        $pricing->getChannel(),
                        $pricing->getFinalPrice(),
                        $pricing->getShippingPrice(),
                        $pricing->getTargetPosition()
                    );
                }
                catch(Exception $ex)
                {
                    Mage::helper('Mpm')->log('Error for product #'.$pricing->getProductId().' and channel '.$pricing->getChannel().' : '.$ex->getMessage(), 'mpm_pricing_import.log');
                }

            }

            unset($pricingCollection);
        } while($countResults > 0);

        Mage::helper('Mpm')->log('Pricing import complete', 'mpm_pricing_import.log');
    }

    public function setPricing($sku, $channel, $finalPrice, $shippingPrice, $targetPosition = null)
    {
        Mage::helper('Mpm')->log('Set pricing for '.$sku.' on '.$channel.' : price='.$finalPrice.' and shipping='.$shippingPrice, 'mpm_pricing_import.log');

        $productId = Mage::getModel('catalog/product')->getIdBySku($sku);

        if (!$productId)
        {
            Mage::helper('Mpm')->log('Unable to load sku '.$sku, 'mpm_pricing_import.log');
            return false;
        }

        $priceAttribute    = $this->getPriceAttribute($channel);
        $shippingAttribute = $this->getShippingAttribute($channel);
        $storeId = $this->getStoreId($channel);
        $myPositionAttribute = 'smartprice_google_position';

        if(empty($priceAttribute) || empty($finalPrice)) {
            return;
        }

        $attributes = array($priceAttribute => $finalPrice);
        if ($shippingAttribute) {
            $attributes[$shippingAttribute] = ($shippingPrice ? $shippingPrice : 0);
        }

        if($targetPosition != null) {
            $attributes[$myPositionAttribute] = $targetPosition;
        }

        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load($productId);
        $price = $product->getData($priceAttribute);

        $productPosition = $product->getData($myPositionAttribute);
        if($price != $finalPrice || $targetPosition != $productPosition) {
            $this->_processUpdate($productId, $attributes, $storeId);
        }

    }

    protected function updateAttributes($productId, $attributes, $storeId) {
        if (Mage::getModel('catalog/resource_product_action'))
        {
            Mage::getModel('catalog/resource_product_action')->updateAttributes(
                array($productId),
                $attributes,
                $storeId
            );
        }
        else
        {
            //magento < 1.6
            Mage::getModel('catalog/product_action')->updateAttributes(
                array($productId),
                $attributes,
                $storeId
            );
        }
    }

        /**
     * @param int $productId
     * @param array $attributes
     * @param int $storeId
     */
    protected function _processUpdate($productId, $attributes, $storeId){

        $this->updateAttributes($productId, $attributes, $storeId);

        if(Mage::getStoreConfig('mpm/repricing/save_product_after_update'))
        {
            Mage::getModel('catalog/product')->setStoreId($storeId)->load($productId)->save();
        } else {
            if (Mage::getStoreConfig('mpm/repricing/change_product_updated_at'))
            {
                Mage::helper('Mpm/Product')->touchUpdatedAt($productId);
            }

        }

        if(Mage::getStoreConfig('mpm/repricing/force_price_reindex')) {
            Mage::getResourceModel('catalog/product_indexer_price')->reindexProductIds(array($productId));
            if (Mage::getStoreConfig(Mage_Catalog_Helper_Product_Flat::XML_PATH_USE_PRODUCT_FLAT)) {
                Mage::getModel('catalog/product_flat_indexer')->updateProduct($productId);
            }
        }

    }

    protected function getPriceAttribute($channel)
    {
        if(!isset($this->priceAttribute[$channel])) {
            $this->priceAttribute[$channel] = Mage::getStoreConfig('mpm/repricing/price_attributes_'.$channel);
        }

        return $this->priceAttribute[$channel];
    }

    protected function getShippingAttribute($channel)
    {
        if(!isset($this->shippingAttribute[$channel])) {
            $this->shippingAttribute[$channel] = Mage::getStoreConfig('mpm/repricing/shipping_attributes_'.$channel);
        }

        return $this->shippingAttribute[$channel];
    }

    protected function getStoreId($channel){

        if(!isset($this->storeId[$channel])){
            $storeId = Mage::getStoreConfig('mpm/repricing/store_id_'.$channel);
            $this->storeId[$channel] = ($storeId) ? $storeId : 0;
        }

        return $this->storeId[$channel];

    }
}