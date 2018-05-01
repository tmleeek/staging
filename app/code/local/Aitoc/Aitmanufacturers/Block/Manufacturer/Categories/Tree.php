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
class Aitoc_Aitmanufacturers_Block_Manufacturer_Categories_Tree extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    /**
    * THIS BLOCK IS NOT FOR OUTPUT!
    * IT GIVES THE CATEGORIES TREE.
    * Is developed as a block in order to use parent functionality.
    */
    public function __construct()
    {
        
    }
    
    protected function _prepareLayout()
    {
    }
    
    public function isReadonly()
    {
        return false;
    }
    
    protected function _toHtml()
    {
        return '';
    }

    protected function getCategoryIds($storeId = null)
    {
        return array();
        
        if (!$this->_storeCategories){
            $storeCategories = Mage::registry('store_categories');
            $this->_storeCategories = array();
            foreach ($storeCategories->getItems() as $item){
                $this->_storeCategories[$item->getStoreId()] = explode(',', $item->getCategoryIds());
            }
        }
        if (isset($this->_storeCategories[$storeId])){
            return $this->_storeCategories[$storeId];
        }
        return array();
    }

    public function getIdsString($storeId = null)
    {
        return implode(',', $this->getCategoryIds($storeId));
    }

    public function getRootNode($storeId = null)
    {
        $root = $this->getRoot(null, 3, $storeId);
        
        $root->setIsVisible(true);
            
        if ($root && in_array($root->getId(), $this->getCategoryIds($storeId))) {
            $root->setChecked(true);
        }
        return $root;
    }

    public function getRoot($parentNodeCategory=null, $recursionLevel=3, $storeId = null)
    {
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        //$root = Mage::registry('root');
        //if (is_null($root)) {
            if (is_null($storeId)){
                $storeId = (int) $this->getRequest()->getParam('store');
            }

            if ($storeId) {
                $store = Mage::app()->getStore($storeId);
                $rootId = $store->getRootCategoryId();
            }
            else {
                $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            }

            $ids = $this->getSelectedCategoriesPathIds($rootId, $storeId);
            $tree = Mage::getResourceSingleton('catalog/category_tree')
                ->loadByIds($ids, false, false);

            if ($this->getCategory()) {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }

            $tree->addCollectionData($this->getCategoryCollection());
            $root = $tree->getNodeById($rootId);

            if ($root && $rootId != Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                $root->setIsVisible(true);
                if ($this->isReadonly()) {
                    $root->setDisabled(true);
                }
            }
            elseif($root && $root->getId() == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                $root->setName(Mage::helper('catalog')->__('Root'));
            }

            //Mage::register('root', $root);
        //}

        return $root;
    }
    
    public function getCategoryCollection()
    {
        $storeId = $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
        $collection = $this->getData('category_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('catalog/category')->getCollection();
            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */

            $collection->addAttributeToSelect('name')
                       ->addAttributeToSelect('is_active')
                       ->setStoreId($storeId);

            if (!Mage::getStoreConfig('catalog/frontend/flat_catalog_category'))
            {
                $collection
                    ->setProductStoreId($storeId)
                    ->setLoadProductCount($this->_withProductCount);
            }

            $this->setData('category_collection', $collection);
        }
        
        return $collection;
    }

    public function getTree($parenNodeCategory=null, $storeId = null)
    {
        if (is_null($storeId))
        {
            $storeId = Mage::app()->getStore()->getId();
        }
        $rootArray = $this->_getNodeJson($this->getRoot($parenNodeCategory, 3, $storeId), 1, $storeId);
        $tree = isset($rootArray['children']) ? $rootArray['children'] : array();
        return $tree;
    }
    
    public function getTreeJson($parenNodeCategory=null, $storeId = null)
    {
        //print 'store '.$storeId;
        $this->getRequest()->setParam('store', $storeId);
        //print_r($this->getRoot($parenNodeCategory, 3));
        //print 'getTreeJson storeId='.$storeId."\n";
        $rootArray = $this->_getNodeJson($this->getRoot($parenNodeCategory, 3, $storeId), 1, $storeId);
        //print_r($rootArray);
        //print_r($rootArray);
        $json = Zend_Json::encode(isset($rootArray['children']) ? $rootArray['children'] : array());
        return $json;
    }

    protected function _getNodeJson($node, $level=1, $storeId = null)
    {
        //$item = parent::_getNodeJson($node, $level, $storeId);
        $item = $this->_getNodeJson2($node, $level, $storeId);
//print_r($node);exit;
        $isParent = $this->_isParentSelectedCategory($node, $storeId);

        $item['level'] = $level - 1;
        
        if ($isParent) {
            $item['expanded'] = true;
        }

        //print $storeId.' store ';var_dump($this->getCategoryIds($storeId));
        //print $node->getId().' ';var_dump(in_array($node->getId(), $this->getCategoryIds($storeId)));
        //print '_getNodeJson storeId='.$storeId."\n";
        
//        if (in_array($node->getId(), $this->getCategoryIds($storeId))) {

        if ($this->isReadonly()) {
            $item['disabled'] = true;
        }
        return $item;
    }

    protected function _getNodeJson2($node, $level = 0, $storeId = null)
    {
        // create a node from data array
        if (is_array($node)) {
            $node = new Varien_Data_Tree_Node($node, 'entity_id', new Varien_Data_Tree);
        }

        $item = array();
        $item['text'] = $this->buildNodeName($node);

        //$rootForStores = Mage::getModel('core/store')->getCollection()->loadByCategoryIds(array($node->getEntityId()));
        $rootForStores = in_array($node->getEntityId(), $this->getRootIds());

        $item['id']  = $node->getId();
        $item['store']  = (int) $this->getStore()->getId();
        $item['path'] = $node->getData('path');

        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        //$item['allowDrop'] = ($level<3) ? true : false;
        $allowMove = false;//$this->_isCategoryMoveable($node);
        $item['allowDrop'] = $allowMove;
        // disallow drag if it's first level and category is root of a store
        $item['allowDrag'] = $allowMove && (($node->getLevel()==1 && $rootForStores) ? false : true);

        if ((int)$node->getChildrenCount()>0) {
            $item['children'] = array();
        }

        $isParent = $this->_isParentSelectedCategory($node, $storeId);

        if ($node->hasChildren()) {
            $item['children'] = array();
            //if ($node->getLevel() > 1) {
                foreach ($node->getChildren() as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level+1, $storeId);
                }
            //}
        }

        if ($isParent || $node->getLevel() < 2) {
            $item['expanded'] = true;
        }

        return $item;
    }

    protected function _isParentSelectedCategory($node, $storeId = null)
    {
        foreach ($this->_getSelectedNodes($storeId) as $selected) {
            if ($selected) {
                $pathIds = explode('/', $selected->getPathId());
                if (in_array($node->getId(), $pathIds)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function _getSelectedNodes($storeId = null)
    {
        //if ($this->_selectedNodes === null) {
            $this->_selectedNodes = array();
            foreach ($this->getCategoryIds($storeId) as $categoryId) {
                $this->_selectedNodes[] = $this->getRoot(null, 3, $storeId)->getTree(null, $storeId)->getNodeById($categoryId);
            }
        //}

        return $this->_selectedNodes;
    }
    
    public function getCategoryChildrenJson($categoryId, $storeId = null)
    {
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $node = $this->getRoot($category, 1, $storeId)->getTree(null, $storeId)->getNodeById($categoryId);

        if (!$node || !$node->hasChildren()) {
            return '[]';
        }

        $children = array();
        foreach ($node->getChildren() as $child) {
            $children[] = $this->_getNodeJson($child);
        }

        return Zend_Json::encode($children);
    }


    public function getSelectedCategoriesPathIds($rootId = false, $storeId = null)
    {
        $ids = array();
        $collection = Mage::getModel('catalog/category')->getCollection();
            //->addFieldToFilter('entity_id', array('in'=>$this->getCategoryIds($storeId)));
        foreach ($collection as $item) {
            if ($rootId && !in_array($rootId, $item->getPathIds())) {
                continue;
            }
            foreach ($item->getPathIds() as $id) {
                if (!in_array($id, $ids)) {
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }

    public function getRootIds()
    {
        $ids = $this->getData('root_ids');
        if (is_null($ids)) {
            $ids = array();
            foreach ($this->getGroups() as $store) {
                if ($store->getId() == Mage::app()->getStore()->getId())
                {
                    $ids[] = $store->getRootCategoryId();
                }
            }
            $this->setData('root_ids', $ids);
        }
        return $ids;
    }
    
    public function getGroups($withDefault = false, $codeKey = false)
    {
        $websiteCollection = Mage::getModel('core/website')->getCollection()
            ->initCache($this->getCache(), 'app', array(Mage_Core_Model_Website::CACHE_TAG))
            ->setLoadDefault(true);
        $groupCollection = Mage::getModel('core/store_group')->getCollection()
            ->initCache($this->getCache(), 'app', array(Mage_Core_Model_Store_Group::CACHE_TAG))
            ->setLoadDefault(true);
                                
        foreach ($groupCollection as $group) {
            /* @var $group Mage_Core_Model_Store_Group */
            if (!isset($groupStores[$group->getId()])) {
                $groupStores[$group->getId()] = array();
            }
            $group->setStores($groupStores[$group->getId()]);
            $group->setWebsite($websiteCollection->getItemById($group->getWebsiteId()));

            $websiteGroups[$group->getWebsiteId()][$group->getId()] = $group;

            $_groups[$group->getId()] = $group;
        }
            
        $groups = array();
        if (is_array($_groups)) {
            foreach ($_groups as $group) {
                if (!$withDefault && $group->getId() == 0) {
                    continue;
                }
                if ($codeKey) {
                    $groups[$group->getCode()] = $group;
                }
                else {
                    $groups[$group->getId()] = $group;
                }
            }
        }

        return $groups;
    }
    
}