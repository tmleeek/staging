<?php
class Extendware_EWDeferredIndexing_Model_Override_Mage_Catalog_Url extends Extendware_EWDeferredIndexing_Model_Override_Mage_Catalog_Url_Bridge
{
	public function refreshProductRewrites($storeId)
    {
    	if (Mage::helper('ewdeferredindexing/config')->isUrlRewriteOptimizationEnabled() === false) {
    		return parent::refreshProductRewrites($storeId);
    	}
    	
    	$excludeDisabled = Mage::helper('ewdeferredindexing/config')->isUrlRewriteExcludeInvisible();
    	$excludeInvisible = Mage::helper('ewdeferredindexing/config')->isUrlRewriteExcludeInvisible();
    	$doesProductUseCategories = (bool)Mage::getStoreConfig('catalog/seo/product_use_categories');
    	
        $this->_categories      = array();
        $storeRootCategoryId    = $this->getStores($storeId)->getRootCategoryId();
        $storeRootCategoryPath  = $this->getStores($storeId)->getRootCategoryPath();
        $this->_categories[$storeRootCategoryId] = $this->getResource()->getCategory($storeRootCategoryId, $storeId);

        $lastEntityId = 0;
        $process = true;

        while ($process == true) {
            $products = $this->getResource()->getProductsByStore($storeId, $lastEntityId);
            if (!$products) {
                $process = false;
                break;
            }

            $this->_rewrites = $this->getResource()->prepareRewrites($storeId, false, array_keys($products));

            $loadCategories = array();
            foreach ($products as $product) {
                foreach ($product->getCategoryIds() as $categoryId) {
                    if (!isset($this->_categories[$categoryId])) {
                        $loadCategories[$categoryId] = $categoryId;
                    }
                }
            }

            if ($loadCategories) {
                foreach ($this->getResource()->getCategories($loadCategories, $storeId) as $category) {
                    $this->_categories[$category->getId()] = $category;
                }
            }

            foreach ($products as $product) {
            	if ($excludeDisabled === true and $product->getData('status') == Mage_Catalog_Model_Product_Status::STATUS_DISABLED) continue;
            	if ($excludeInvisible === true and $product->getData('visibility') == Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE) continue;
                $this->_refreshProductRewrite($product, $this->_categories[$storeRootCategoryId]);
                if ($doesProductUseCategories === true) {
	                foreach ($product->getCategoryIds() as $categoryId) {
	                    if ($categoryId != $storeRootCategoryId && isset($this->_categories[$categoryId])) {
	                        if (strpos($this->_categories[$categoryId]['path'], $storeRootCategoryPath . '/') !== 0) {
	                            continue;
	                        }
	                        $this->_refreshProductRewrite($product, $this->_categories[$categoryId]);
	                    }
	                }
                }
            }

            unset($products);
            $this->_rewrites = array();
        }

        $this->_categories = array();
        return $this;
    }
}
