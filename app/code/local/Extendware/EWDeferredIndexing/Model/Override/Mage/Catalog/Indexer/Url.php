<?php
class Extendware_EWDeferredIndexing_Model_Override_Mage_Catalog_Indexer_Url extends Extendware_EWDeferredIndexing_Model_Override_Mage_Catalog_Indexer_Url_Bridge
{
	protected function _registerProductEvent(Mage_Index_Model_Event $event)
    {
		if (Mage::helper('ewdeferredindexing/config')->isUrlRewriteOptimizationEnabled() === false) {
    		return parent::_registerProductEvent($event);
    	}
    	
        $product = $event->getDataObject();
        
        $hasChanged = false;
        if (Mage::helper('ewdeferredindexing/config')->isUrlRewriteOptimizationEnabled() === true) {
        	if (Mage::helper('ewdeferredindexing/config')->isUrlRewriteExcludeDisabled() === true) {
		        if ($product->dataHasChangedFor('status') and $product->getData('status') == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
		        	 $hasChanged = true;
		        }
        	}
        	
        	if (Mage::helper('ewdeferredindexing/config')->isUrlRewriteExcludeInvisible() === true) {
		        if ($product->dataHasChangedFor('visibility') and $product->getData('visibility') != Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE) {
		        	$hasChanged = true;
		        }
        	}
        }

        $dataChange = $hasChanged || $product->dataHasChangedFor('url_key')
            || $product->getIsChangedCategories()
            || $product->getIsChangedWebsites();

        if (!$product->getExcludeUrlRewrite() && $dataChange) {
            $event->addNewData('rewrite_product_ids', array($product->getId()));
        }
    }
}
