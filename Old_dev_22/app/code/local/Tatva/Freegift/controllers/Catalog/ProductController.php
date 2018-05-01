<?php
include_once('Mage/Adminhtml/controllers/Catalog/ProductController.php');
class Tatva_Freegift_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
	public function freegiftAction()
	{ 
		$this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.freegift')
            ->setProductsFreegift($this->getRequest()->getPost('products_freegift', null));
        $this->renderLayout();
	}
	
	public function freegiftGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.freegift')
            ->setProductsFreegift($this->getRequest()->getPost('products_freegift', null));
        $this->renderLayout();
    }
	
	/**
     * Initialize product before saving
     */
    protected function _initProductSave()
    {
        $product     = $this->_initProduct();
        $productData = $this->getRequest()->getPost('product');

        if ($productData) {
            $this->_filterStockData($productData['stock_data']);
        }

        /**
         * Websites
         */
        if (!isset($productData['website_ids'])) {
            $productData['website_ids'] = array();
        }

        $wasLockedMedia = false;
        if ($product->isLockedAttribute('media')) {
            $product->unlockAttribute('media');
            $wasLockedMedia = true;
        }

        $product->addData($productData);

        if ($wasLockedMedia) {
            $product->lockAttribute('media');
        }

        if (Mage::app()->isSingleStoreMode()) {
            $product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
        }

        /**
         * Create Permanent Redirect for old URL key
         */
        if ($product->getId() && isset($productData['url_key_create_redirect']))
        // && $product->getOrigData('url_key') != $product->getData('url_key')
        {
            $product->setData('save_rewrites_history', (bool)$productData['url_key_create_redirect']);
        }

        /**
         * Check "Use Default Value" checkboxes values
         */
        if ($useDefaults = $this->getRequest()->getPost('use_default')) {
            foreach ($useDefaults as $attributeCode) {
                $product->setData($attributeCode, false);
            }
        }

        /**
         * Init product links data (related, upsell, crosssel)
         */
        $links = $this->getRequest()->getPost('links');
        if (isset($links['related']) && !$product->getRelatedReadonly()) {
            $product->setRelatedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related']));
        }
        if (isset($links['upsell']) && !$product->getUpsellReadonly()) {
            $product->setUpSellLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['upsell']));
        }
        if (isset($links['crosssell']) && !$product->getCrosssellReadonly()) {
            $product->setCrossSellLinkData(Mage::helper('adminhtml/js')
                ->decodeGridSerializedInput($links['crosssell']));
        }
        if (isset($links['grouped']) && !$product->getGroupedReadonly()) {
            $product->setGroupedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['grouped']));
        }


        /**
         * Initialize product categories
         */
        $categoryIds = $this->getRequest()->getPost('category_ids');
        if (null !== $categoryIds) {
            if (empty($categoryIds)) {
                $categoryIds = array();
            }
            $product->setCategoryIds($categoryIds);
        }

        /**
         * Initialize data for configurable product
         */
        if (($data = $this->getRequest()->getPost('configurable_products_data'))
            && !$product->getConfigurableReadonly()
        ) {
            $product->setConfigurableProductsData(Mage::helper('core')->jsonDecode($data));
        }
        if (($data = $this->getRequest()->getPost('configurable_attributes_data'))
            && !$product->getConfigurableReadonly()
        ) {
            $product->setConfigurableAttributesData(Mage::helper('core')->jsonDecode($data));
        }

        $product->setCanSaveConfigurableAttributes(
            (bool) $this->getRequest()->getPost('affect_configurable_product_attributes')
                && !$product->getConfigurableReadonly()
        );

        /**
         * Initialize product options
         */
        if (isset($productData['options']) && !$product->getOptionsReadonly()) {
            $product->setProductOptions($productData['options']);
        }

        $product->setCanSaveCustomOptions(
            (bool)$this->getRequest()->getPost('affect_product_custom_options')
            && !$product->getOptionsReadonly()
        );

        Mage::dispatchEvent(
            'catalog_product_prepare_save',
            array('product' => $product, 'request' => $this->getRequest())
        );


        	if (isset($links['freegift']))
		   {
            //$freegiftData=array();
			$freegiftData = Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['freegift']);
            $product_id= $product->getEntityId();
			$this->getSaveLinkProduct($product_id,$freegiftData);
		  }


        
        return $product;
    }

	protected function _filterStockData(&$stockData) {
        if (!isset($stockData['use_config_manage_stock'])) {
            $stockData['use_config_manage_stock'] = 0;
        }
        if (isset($stockData['qty']) && (float)$stockData['qty'] > self::MAX_QTY_VALUE) {
            $stockData['qty'] = self::MAX_QTY_VALUE;
        }
        if (isset($stockData['min_qty']) && (int)$stockData['min_qty'] < 0) {
            $stockData['min_qty'] = 0;
        }
        if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
            $stockData['is_decimal_divided'] = 0;
        }
    }


    public function getSaveLinkProduct($final_product_id,$link_products)
    {
       $write = Mage::getSingleton("core/resource")->getConnection("core_write");
       $read= Mage::getSingleton('core/resource')->getConnection('core_read');
       $cvarchartable="catalog_product_link";

       foreach($link_products as $final_linked_id)
       {
             $link_id=0;
            if($final_product_id!='' && $final_linked_id!='')
            {


            $sql_check_link_id='SELECT link_id FROM `catalog_product_link` WHERE `product_id` ='.$final_product_id.' AND `linked_product_id` ='.$final_linked_id.' AND link_type_id=100 Limit 1';
            $link_id=$read->FetchOne($sql_check_link_id);
            /* new data insert */
            if($link_id=='0')
            {

              $sql = "INSERT INTO ".$cvarchartable." (product_id,linked_product_id,link_type_id)
    		                                          VALUES ('".$final_product_id."','".$final_linked_id."','100')";
              $write->query($sql);
            }


          }
       }
    }

}