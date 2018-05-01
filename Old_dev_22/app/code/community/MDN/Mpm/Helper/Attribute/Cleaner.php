<?php

ini_set('max_execution_time', -1);

/**
 * Class MDN_Mpm_Helper_Attribute_Cleaner
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Helper_Attribute_Cleaner extends Mage_Core_Helper_Abstract {

    /**
     * @var array
     */
    protected $_channelCodes = array();

    /**
     * @var array
     */
    protected $_attributes = array();

    /**
     * @var
     */
    protected $_productCollection;

    public function cleanAttributesForMissingProducts(){

        $this->_initProductCollection();

        $productCatalogIds = Mage::getModel('Mpm/Export_Catalog')->getProductsCollection()->getAllIds();

        $productCatalogIds = array_flip($productCatalogIds);

        foreach($this->_productCollection as $product){

            if(!isset($productCatalogIds[$product->getId()])){

                $this->_cleanCarlAttributesForProduct($product);

            }

        }

    }

    protected function _initProductCollection(){

        $this->_productCollection = Mage::getModel('catalog/product')
            ->getCollection();

        foreach(Mage::Helper('Mpm/Carl')->getAllChannels() as $channel){

            $channelCode = $channel->organization.'_'.$channel->locale.'_'.$channel->subset;
            $priceAttribute = Mage::getStoreConfig('mpm/repricing/price_attributes_'.$channelCode);
            $shippingAttribute = Mage::getStoreConfig('mpm/repricing/shipping_attributes_'.$channelCode);

            if(!empty($priceAttribute) || !empty($shippingAttribute)){

                $channelCode = trim($channelCode);

                $this->_channelCodes[] = $channelCode;

                $this->_attributes[$channelCode] = array(
                    'price' => $priceAttribute,
                    'shipping' => $shippingAttribute,
                    'storeId' => Mage::getStoreConfig('mpm/repricing/store_id_'.$channelCode)
                );

                $this->_productCollection->addAttributeToSelect($priceAttribute);

            }

        }

    }

    /**
     * @param Mage_Catalog_Model_Product $product
     */
    protected function _cleanCarlAttributesForProduct($product){

        foreach($this->_channelCodes as $channelCode){

            $priceAttribute = $this->_attributes[$channelCode]['price'];
            $shippingAttribute = $this->_attributes[$channelCode]['shipping'];
            $storeId = ($this->_attributes[$channelCode]['storeId']) ? $this->_attributes[$channelCode]['storeId'] : 0;

            $productAttributes = array();
            if(!empty($priceAttribute) && $priceAttribute != 'price'){
                $productAttributes[$priceAttribute] = '';
            }

            if(!empty($shippingAttribute)){
                $productAttributes[$shippingAttribute] = '';
            }

            if(count($productAttributes) > 0){

                $this->_updateAttribute($product->getId(), $productAttributes, $storeId);

            }

        }

    }

    /**
     * @param array $productId
     * @param array $attribute
     * @param int $storeId
     * @return int
     */
    protected function _updateAttribute($productId, $attribute, $storeId){

        if (Mage::getModel('catalog/resource_product_action'))
        {
            Mage::getModel('catalog/resource_product_action')->updateAttributes(
                array($productId),
                $attribute,
                $storeId
            );
        }
        else
        {
            //magento < 1.6
            Mage::getModel('catalog/product_action')->updateAttributes(
                array($productId),
                $attribute,
                $storeId
            );
        }

        return 0;

    }

}