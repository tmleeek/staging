<?php
/**
 * Shop By Brands
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitmanufacturers
 * @version      3.3.1
 * @license:     zAuKpf4IoBvEYeo5ue8Cll0eto0di8JUzOnOWiuiAF
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

/**
 * It is the god object of this module.
 *
 * I've kept some marks for easier further research of this module:
 * !AITOC_MARK:manufacturer_collection - we use product or manufacturere collection with complicated selects here.
 * !AITOC_MARK:manufacturer_collection_index - we use the catalog product index for EAV attributes her with
 * complecated selects here.
 *
 * Important helpful notes about EAV:
 *  Mage_Eav_Model_Entity_Collection_Abstract - this class uses the following consequence of methods to load attribute
 *  values: _loadAttributes than _addLoadAttributesSelectValues
 *  when you fix or refactor something you should be sure that you don't use any custom tricks to load attribute values.
 * To be sure that you will get default values for attribute when you load product entity for a certain store
 * you should check if your collection has following class among parents: Mage_Catalog_Model_Resource_Collection_Abstract
 * and following consequence of methods is in use joinAttribute than _addAtributeJoin than _joinAttributeToSelect.
 *
 * @author Igor Tkachenko
 * Class Aitoc_Aitmanufacturers_Model_Aitmanufacturers
 */
class Aitoc_Aitmanufacturers_Model_Aitmanufacturers extends Mage_Core_Model_Abstract
{
    protected $_collection = null;
    protected $_optionCollection = null;
    protected static $_url = null;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('aitmanufacturers/aitmanufacturers');
    }
    
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ('' == $this->getData('root_template'))
            $this->setData('root_template', 'two_columns_left');
    }
    
    public function getManufacturerName($manufacturerId,$storeId)
    {   //echo $manufacturerId; exit;
        //return $this->getResource()->getAttributeOptionValue($manufacturerId);
       $options = Mage::getResourceModel('eav/entity_attribute_option_collection');
       $values_gamme  = $options->setStoreFilter($storeId)->toOptionArray();
       $label='';
        foreach($values_gamme as $option_gamme)
       {
        if(!empty($option_gamme['label'])){
           if($option_gamme['value']==$manufacturerId)
           {
             $label= $option_gamme['label'];
           }
        }
       }
       return $label;
    }
    
    public function loadByManufacturer($manufacturerId)
    {
        $storeId = Mage::app()->getStore()->getId();
        return $this->getCollection()->addStoreFilter($storeId)
            ->addFieldToFilter('main_table.manufacturer_id', array("eq"=>$manufacturerId))->getFirstItem();
    }

    public function toManufacturersOptionsArray($storeId = null, $attributeCode)
    {   $storeId = Mage::app()->getStore()->getId();
        return $this->getCollection()->toManufacturersOptionsArray($storeId, $attributeCode);
    }
    
    public function checkUrlKey($urlKey, $storeId)
    {
        return $this->_getResource()->checkUrlKey($urlKey, $storeId);
    }
    
    public function isUniqueUrlKey($urlKey, $id = 0, $storeId = null)
    {
        if (is_null($storeId)){
            $storeId = Mage::app()->getStore()->getId();
        }
        $id = $this->_getResource()->checkUniqueUrlKey($urlKey, $id, $storeId);
        return (bool)empty($id);
    }
    
    public function getUrl($storeId = null)
    {
        if ($this->getId())
        {

            if (is_null($storeId))
            {
                $storeId = Mage::app()->getStore()->getId();
            }

            $rewriteModel = Mage::getModel('core/url_rewrite');
            $rewriteCollection = $rewriteModel->getCollection();
            $rewriteCollection->addStoreFilter($storeId, true)
                              ->setOrder('store_id', 'DESC')
                              ->addFieldToFilter('target_path', 'brands/index/view/id/' . $this->getId())
                              ->setPageSize(1)
                              ->load();
            if (count($rewriteCollection) > 0)
            {
                foreach ($rewriteCollection as $rewrite)
				{
                    $rewriteModel->setData($rewrite->getData());
                   
                }

                return Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . $rewriteModel->getRequestPath();
            } 
			else
            {
                return $this->getUrlInstance()->getUrl('brands/index/view', array('id' => $this->getId()));
            }
        }
        return '';
    }
    
    /**
     * Retrieve URL instance
     *
     * @return Mage_Core_Model_Url
     */
    public function getUrlInstance()
    {
        if (!self::$_url) {
            self::$_url = Mage::getModel('core/url');
        }
        return self::$_url;
    }
    
    public function getProductsByManufacturer($manufacturerId, $storeId, $attributeId)
    {
        /* !AITOC_MARK:manufacturer_collection_index */

        $resource = Mage::getResourceModel('catalogindex/attribute');
        $select = $resource->getReadConnection()->select();
        
        $select->from($resource->getMainTable(), 'entity_id')
            ->distinct(true)
            ->where('store_id = ?', $storeId)
            ->where('attribute_id = ?', $attributeId)
            ->where('value = ?', $manufacturerId);

        return $resource->getReadConnection()->fetchCol($select);
    }
    
    public function getManufacturersByProducts($productIds, $storeId, $attributeId)
    {
        /* !AITOC_MARK:manufacturer_collection_index */
        $resource = Mage::getResourceModel('catalogindex/attribute');
        $select = $resource->getReadConnection()->select();

        if (empty($productIds))
        {
            return array();
        }

        $select->from($resource->getMainTable(), 'value')
            ->distinct(true)
            ->where('store_id = ?', $storeId)
            ->where('attribute_id = ?', $attributeId)
            ->where('entity_id IN (?)', $productIds);

        return $resource->getReadConnection()->fetchCol($select);
    }

    /**
     * Generate brand collection and count all products that are applied to it
     *
     * @var int $attributeId
     * @var string $attributeCode
     *
     * @return Aitoc_Aitmanufacturers_Model_Mysql4_Aitmanufacturers_Collection
     */
    public function getBrandCollection($attributeId, $attributeCode)
    {
        /* !AITOC_MARK:manufacturer_collection_index */
        /* !AITOC_MARK:manufacturer_collection */
        $storeId = Mage::app()->getStore()->getId();
        //generating default manufacturer collection
        $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection()
            ->addStoreFilter($storeId)
            ->addAttributeToFilter($attributeCode, $storeId)
            ->addStatusFilter()
            ->addSortOrder();

        //joining brands with products that assigned to them
        $resource = Mage::getResourceModel('catalogindex/attribute');
        #$product_resource = Mage::getResourceModel('catalog/product');
        $collection->getSelect()
            ->joinLeft(
                array('e'=>$resource->getMainTable()),
                'e.store_id = '.$storeId.' and e.attribute_id = '.$attributeId. ' AND e.value=main_table.manufacturer_id ',
                array()
            )
            /*->joinLeft(
                array('e'=>$product_resource->getEntityTable()),
                'cat_attr.entity_id = e.entity_id',
                array()
            )*/
            ->group('main_table.manufacturer_id');

        //now we need to apply product visibility limitations to collection
        //for this we create another collection
        $productCollection = Mage::getModel('catalog/product')->getResourceCollection();
        //and applying filters to it
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($productCollection);
        //then selecting join conditions
        $from = $productCollection->getSelect()->getPart(Zend_Db_Select::FROM);
        foreach($from as $key => $value) {
            if($value['joinType'] == Zend_Db_Select::FROM) {
                //ignoring main table, because "catalogindex/attribute" table should have same entity_id and it will be used insted
                //but we can use $key here to change "e" alias for same table, if it will be changed anywhere
                continue;
            }
            //do not select anything from this tables
            $select = array();
            //except amount of products in cat_index table, becasue they have visibility filters applied
            if($key == 'cat_index') {
                $select = array('products_amount' => 'count(DISTINCT cat_index.product_id)');
            }
            $collection->getSelect()
                ->joinLeft(array($key => $value['tableName']), $value['joinCondition'], $select);
        }
        /* Default filters limitation by only one column
         * if(isset($from['cat_index'])) {
            $collection->getSelect()
                ->joinLeft(array('cat_index' => $from['cat_index']['tableName']),$from['cat_index']['joinCondition'], array('products_amount' => 'count(DISTINCT cat_index.product_id)'));
        }*/
        return $collection;
    }
    
    public function isBrandsImageExists($imageName , $iManufacturerId, $sFileName)
    {
        return $this->_isFileExists($imageName, $iManufacturerId, $sFileName); 
    }
    
    public function deletePictures($iId)  
    {
    	$this->load($iId);
    	if($this->getImage())
    		@unlink(Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . $this->getImage());
    	if($this->getSmallLogo())
    		@unlink(Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . 'logo' . DS . $this->getSmallLogo());
    	if($this->getListImage())
    		@unlink(Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . 'list' . DS . $this->getListImage());
    }
    
    protected function _isFileExists($sField, $iManufacturerId, $sFileName)
    {
    	$resource = $this->getResource();
        $select = $resource->getReadConnection()->select();
        
        $select->from($resource->getMainTable(), 'count(*)')
            ->where('id != ?', $iManufacturerId)
            ->where($sField.' = ?', $sFileName);

        return $resource->getReadConnection()->fetchOne($select);    
    }
    
    public function fillOut($storeId = 0, $attributeCode = 'brand')
    {
        Mage::register('aitmanufacturers_fillout_inprogress', true);
        //$stores = array_keys(Mage::app()->getStores(true));
        $resource = $this->getResource();
        $select = $resource->getReadConnection()->select();
        $select->from(array('main_table' => $resource->getMainTable()), array('manufacturer_id', 'url_key'))
            ->distinct(true)
            ->join(
                array('stores_table' => $resource->getTable('aitmanufacturers/aitmanufacturers_stores')),
                'main_table.manufacturer_id = stores_table.manufacturer_id'
                //array('store_id')
            )
            ->where('stores_table.store_id = ?', $storeId);
        //print_r($select->__toString());exit;
        $array = $resource->getReadConnection()->fetchPairs($select);
        $optionIds = array_keys($array);
        $urlKeys = array_values($array);

        $manufacturersCollection = $this->getCollection()->getManufacturersCollection($optionIds, $storeId, $attributeCode);

        $showBriefIcons = Mage::helper('aitmanufacturers')->getConfigParam('show_brief_image', $attributeCode, $storeId);
        $showListIcons  = Mage::helper('aitmanufacturers')->getConfigParam('show_list_image', $attributeCode, $storeId);
        foreach ($manufacturersCollection as $manufacturer){
            $urlKey = Mage::helper('aitmanufacturers')->toUrlKey($manufacturer->getValue());
            while (in_array($urlKey, $urlKeys)){
                $urlKey .= rand(0, 99);
            }
            $urlKeys[] = $urlKey;
            $this->load(0);
            $data = array(
                'manufacturer_id' => $manufacturer->getOptionId(),
                'title' => $manufacturer->getValue(),
                'url_key' => $urlKey,
                'status' => 1,
            	'show_brief_image' => $showBriefIcons,
            	'show_list_image' => $showListIcons,
                'stores' => array($storeId),
            );
            $this->setData($data);
            $this->_afterLoad();
            $this->save();
        }
        Mage::unregister('aitmanufacturers_fillout_inprogress');
    }

    public function getProductFilter($marque,$gamme_array)
    {
      $ids=array();
      $ids_return='';

      $product_colls=Mage::getModel('catalog/product')->getCollection()
      ->addAttributeToSelect('manufacturer')
      ->addAttributeToSelect('gamme_collection_new');
      $product_colls->addAttributeToFilter('manufacturer',$marque);
      $product_colls->addAttributeToFilter('gamme_collection_new',$gamme_array);

      /*echo $product_colls->getSelect();
      echo "<br />";*/
      foreach($product_colls as $product)
      {

       $ids[]=$product->getEntityId();
      }

      $ids_return=implode(",",$ids);
      return $ids_return;
    }

  public function getlistUrl($store_id,$id)
  {
    $manufacturer = Mage::getModel('aitmanufacturers/aitmanufacturers')->loadByManufacturer($id);
    $list='';
     //$list_colls= Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();
     //$list_colls->addFieldToFilter('manufacturer_id',$id)->addStoreFilter(Mage :: app()->getStore())->getFirstItem();
     //foreach($list_colls as $list)
     //{
        //$list_key_url=$list['url_key'];
        //if($list_key_url!='')
        //{
            //$list=$list_key_url.'.html';
        //}
     //}

     $list = $manufacturer->getUrlKey().'.html';

     return $list;
  }

   public function  getlistgammeUrl($store_id,$gid,$marque)
  {
     $list_url='';
     $list_final='';
     $list_key_url_brand='';

     $manufacturer = Mage::getModel('aitmanufacturers/aitmanufacturers')->loadByManufacturer($marque);
     $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->loadByManufacturer($gid);

     $list_url= $collection->getUrlKey().'.html';
     $list_key_url_brand = $manufacturer->getUrlKey();
     $list_final = $list_key_url_brand.'/'.$list_url;

     /*$list_colls= Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();
     $list_colls->addFieldToFilter('manufacturer_id',$gid)->addStoreFilter(Mage :: app()->getStore())->getFirstItem();
     foreach($list_colls as $list)
     {
         $list_key_url=$list['url_key'];
        if($list_key_url!='')
        {
            $list_url=$list_key_url.'.html';
        }
     }
      $list_key_url_brand=$this->getMarqueGemaUrl($marque);
      if($list_key_url_brand!='')
      {
          //$marque_collection= 'marque-collection';
          $list_final=$list_key_url_brand.'/'.$list_url;
      }*/
      return $list_final;
  }

   public function getMarqueGemaUrl($marque)
   {
      /*$list_key_url_brand='';
      $list_colls_brand= Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();
      $list_colls_brand->addFieldToFilter('manufacturer_id',$marque)->addStoreFilter(Mage :: app()->getStore())->getFirstItem();
      foreach($list_colls_brand as $list_data)
      {
         $list_key_url_brand=$list_data['url_key'];
      }*/
      $manufacturer = Mage::getModel('aitmanufacturers/aitmanufacturers')->loadByManufacturer($marque);
      $list_key_url_brand = $manufacturer->getUrlKey();

      return $list_key_url_brand;
   }


  	public function getBrand($brandId)
    {
		/*$list_colls= Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();
        $list_colls->addFieldToFilter('manufacturer_id',$brandId)->addStoreFilter(Mage :: app()->getStore())->getFirstItem();
        foreach($list_colls as $list)
        {
         $brand=$list->getTitle();
        }*/
        $manufacturer = Mage::getModel('aitmanufacturers/aitmanufacturers')->loadByManufacturer($marque);
        $brand=$manufacturer->getTitle();

		return $brand;
	}



    public function getGammeCollection($manufacturerId,$storeId)
    {
      $options = Mage::getResourceModel('eav/entity_attribute_option_collection');
       $values_gamme  = $options->setStoreFilter($storeId)->toOptionArray();
       $label='';
        foreach($values_gamme as $option_gamme)
       {
        if(!empty($option_gamme['label'])){
           if($option_gamme['value']==$manufacturerId)
           {
             $label= $option_gamme['label'];
           }
        }
       }
       return $label;
    }

    public function getManufacturerNameforquote($id,$name)
    {
     $label='';
     $product=Mage::getModel('catalog/product')->load($id);
     $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $name);

     if($name=='manufacturer')
     {
       foreach ($attribute->getSource()->getAllOptions(true, true) as $option){

        if($option['value']==$product->getManufacturer())
       {
        $label= $option['label'];
       }
      }
     }
     else
     {
       foreach ($attribute->getSource()->getAllOptions(true, true) as $option){

        if($option['value']==$product->getGammeCollectionNew())
        {
          $label= $option['label'];
        }
       }
     }
     return $label;
    }

   public function getCatalofruledata($_product)
   {
     $customerGroupId = 0;
     $website=Mage::app()->getWebsite()->getId();
	 $catalogRuleProducts = Mage::getModel('catalogrule/rule_product_price')
								->getCollection()
								->addFieldToFilter('main_table.website_id',$website)
								->addFieldToFilter('main_table.customer_group_id',$customerGroupId)
								;

		$catalogRuleProducts->getSelect()->where('main_table.product_id = ?', $_product->getId());

		$tableName = Mage::getModel('catalogrule/rule_product_price')->getResource()->getTable('catalogrule/rule_product');
		$catalogRuleProducts->getSelect()
			->from(array('rule_product' => $tableName), 'rule_id')
			->where ('rule_product.product_id = main_table.product_id ')
			->where('rule_product.customer_group_id = ?',$customerGroupId)
			->where('rule_product.website_id = ?',$website);
     return $catalogRuleProducts;
   }

}