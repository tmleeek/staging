<?php
/**
 * created : 21 aout 2009
 * Liste des produits du top des ventes
 * 
 * 
 * @category SQLI
 * @package Sqli_Alsobought
 * @author sgautier
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * 
 * @package Sqli_Alsobought
 */
class Tatva_Freegift_Block_Product extends Mage_Catalog_Block_Product_Abstract {

	const CACHE_TAG = "TATVA_FREEGIFT_PRODUCT";





	protected $_collectionCount = NULL;
	protected $_productCollectionId = NULL;
	protected $_cacheKeyArray = NULL;

	/**
	 * Initialize block's cache
	 */
	protected function _construct()
	{
		parent::_construct();

		$this->addData(array(
			'cache_lifetime'    => 84600,
			'cache_tags'        => array("TATVA_FREEGIFT_PRODUCT"),
		));
	}

	/**
	 * Get Key pieces for caching block content
	 *
	 * @return array
	 */
		public function getCacheKeyInfo()
	{
      $current_product = is_object(Mage::registry("current_product"))?Mage::registry("current_product")->getId():0;

			$this->_cacheKeyArray = array(
				'INFORTIS_ITEMSLIDER',
				Mage::app()->getStore()->getCurrentCurrency()->getCode(),
				Mage::app()->getStore()->getId(),
				Mage::getDesign()->getPackageName(),
				Mage::getDesign()->getTheme('template'),
				Mage::getSingleton('customer/session')->getCustomerGroupId(),
				'template' => $this->getTemplate(),

				$this->getBlockName(),
				(int)Mage::app()->getStore()->isCurrentlySecure(),
                $current_product,
			);
		return $this->_cacheKeyArray;
	}

	/**
	 * Get collection id
	 *
	 * @return string
	 */
	public function getUniqueCollectionId()
	{
		if (NULL === $this->_productCollectionId)
		{
			$this->_prepareCollectionAndCache();
		}
		return $this->_productCollectionId;
	}

	/**
	 * Get number of products in the collection
	 *
	 * @return int
	 */
	public function getCollectionCount()
	{
		if (NULL === $this->_collectionCount)
		{
			$this->_prepareCollectionAndCache();
		}
		return $this->_collectionCount;
	}

	/**
	 * Prepare collection id, count collection
	 */
	protected function _prepareCollectionAndCache()
	{
		$ids = array();
		$i = 0;
		foreach ($this->_getProductCollection() as $product)
		{
			$ids[] = $product->getId();
			$i++;
		}

		$this->_productCollectionId = implode("+", $ids);
		$this->_collectionCount = $i;
	}

	/**
	 * Retrieve loaded category collection.
	 * Variables collected from CMS markup: category_id, product_count, is_random
	 */
	public function _getProductCollection()
	{
       $collection = $this->_getProducts();

       return $collection;
	}

	/**
	 * Create unique block id for frontend
	 *
	 * @return string
	 */


    public function _getProducts()
    {
        $product = Mage::registry('product');
		$collection=$this->getAlsoboughtProuductCollectionForFont($product);
        return $collection;
    }
	public function getFrontendHash()
	{
		return md5(implode("+", $this->getCacheKeyInfo()));
	}

    public function lastviewproduct($pro_sku)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('who_also_view');
        $query = "SELECT *,GROUP_CONCAT(product_id) AS products FROM $table where find_in_set($pro_sku,product_id) having products IS NOT NULL";
        $collectiondata = $readConnection->fetchAll($query);
        //echo "<pre>";print_r($collectiondata);die();
        $skus = array();
        $newarray = array();
        $proskus = array();
        
        if(count($collectiondata)>0)
        {
            foreach($collectiondata as $key=>$value)
            {
                $skus = explode(",",$value['products']);
                foreach($skus as $key => $value_sku) 
                {
                    array_push($newarray, $value_sku);
                }
            }
           foreach($newarray as $data)
           {
               if($data!=$pro_sku)
               {
                   $proskus[] = $data;
               }
           }
        
            return array_unique($proskus);
        }
       
    }      
    public function getAlsoboughtProuductCollectionForFont($product)
    {
     
   
	/*perfect $collection = Mage::getResourceModel('catalog/product_collection')
            ->setPageSize(6);
 
        $collection->getSelect()
            ->join(
                array('tlink' => $collection->getResource()->getTable('catalog/product_link')),
                "e.entity_id = tlink.product_id AND tlink.link_type_id=100",''
            );*/
			
		  /*nisha working $collection = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->setPageSize(20);
 
        $collection->getSelect()
            ->join(
                array('tlink' => $collection->getResource()->getTable('catalog/product_link')),
                "e.entity_id = tlink.product_id AND tlink.link_type_id=100",''
            )->where('product_id = '.$product->getEntityId());*/
 
  
	
	//echo $collection->getSelect();exit; 

   
	
       /* Mauli working $collection = Mage::getModel('catalog/product_link')->getCollection()
        ->addFieldToFilter('linked_product_id', $product->getEntityId())
        ->addFieldToFilter('link_type_id', Mage_Catalog_Model_Product_Link::LINK_TYPE_ALSOBOUGHT);
        $collection->setPageSize(20)
                   ->setCurPage(1);
        $collection->getSelect()->order('rand()');*/
		
		
		$collection = Mage::getModel('catalog/product_link')->getCollection();
        
        
        
		
		  $collection->getSelect()	
            ->join(
                array('tb_status' => 'catalog_product_entity_int'),
                "main_table.product_id = tb_status.entity_id AND attribute_id=96",''
            )->where('value = 1 and linked_product_id = '.$product->getEntityId())->limit(20);
			
			
			
			
			
		
		
        /*Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);*/

        return $collection;


    }
}
