<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */
/**
 * Search Autocomplete extension
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @author     MageWorx Dev Team
 */

require_once 'Mage/CatalogSearch/controllers/AjaxController.php';

class MageWorx_SearchAutocomplete_AjaxController extends Mage_CatalogSearch_AjaxController {

    public function suggestAction() { 
        
        if (!Mage::helper('searchautocomplete')->isEnabled()) return parent::suggestAction();
        // xsearch
        $searchParameter = false;
        if (Mage::getConfig()->getModuleConfig('MageWorx_XSearch')->is('active', true)) {
            $xsearchHelper = Mage::helper('xsearch');
            if($xsearchHelper && method_exists($xsearchHelper, 'isSearchByAttributes') && Mage::helper('xsearch')->isSearchByAttributes()){
                $searchParameter = Mage::helper('xsearch')->getSearchParameter();
            }
        }        
        
        $queryText = $this->getRequest()->getParam('q', false);
        if (!$queryText || $queryText == '') {
            exit();
        }

        $queryModel = Mage::helper('catalogsearch')->getQuery();
        $queryModel->setStoreId(Mage::app()->getStore()->getId());
        $queryModel->prepare();
        $queryModel->save();  
        
        if (Mage::helper('searchautocomplete')->isSuggestedSearches() && !$searchParameter) {
            //$suggestData = $this->getLayout()->createBlock('catalogsearch/autocomplete')->getSuggestData();
            $suggestData = Mage::getResourceModel('searchautocomplete/query_collection')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->setQueryFilter($queryText)
                ->setLimit(Mage::helper('searchautocomplete')->getSuggestedSearchesResults())
                ->toArray();            
        } else {
            $suggestData = array();
        }    
        
        if (Mage::helper('searchautocomplete')->isSearchProducts()) {
            if(!$searchParameter){
                $layer = Mage::getModel('catalogsearch/layer');
            } else{
                $layer = Mage::getModel('xsearch/layer');
            }
            
            $attr = array();
            $fields = Mage::helper('searchautocomplete')->getProductSearchResultFields();
            if(in_array('description', $fields)){
                $attr[] = 'description';
            }
            if(in_array('short_description', $fields)){
                $attr[] = 'short_description';
            }
            if(in_array('product_image', $fields)){
                $attr[] = 'image';
            }
			
			if(in_array('sku', $fields)){
                $attr[] = 'sku';
            }
            
            $collection = $layer->getProductCollection();
            $collection->addAttributeToSelect($attr);
            $collection->setOrder('relevance','desc');
            $collection->getSelect()->limit(Mage::helper('searchautocomplete')->getProductSearchResults());
            
            $products = $collection->load();
        } else {
            $products = array();
        }
        
        if (Mage::helper('searchautocomplete')->isSearchCategories() && !$searchParameter) {
            $categories = Mage::getModel('searchautocomplete/search')->getRelevantCategoriesByQuery();
        } else {
            $categories = array();
        }    
        
        if (Mage::helper('searchautocomplete')->isSearchCms() && !$searchParameter) {
            $cmsPages = Mage::getModel('searchautocomplete/search')->getCmsPageCollection();
        } else {
            $cmsPages = array();
        }
        
        if (Mage::helper('searchautocomplete')->isSearchBlog() && !$searchParameter) {
            $blogPosts = Mage::getModel('searchautocomplete/search')->getBlogPostCollection();
        } else {
            $blogPosts = array();
        }
        
        if (Mage::helper('searchautocomplete')->isSearchCmspro() && !$searchParameter) {
            $news = Mage::getModel('searchautocomplete/search')->getCmsproNewsCollection();
        } else {
            $news = array();
        }

        if (count($suggestData) || count($products) || count($categories) || count($cmsPages) || count($blogPosts) || count($news)) {
            Mage::helper('searchautocomplete')->setLastQueryText($queryText);
            $this->getResponse()->setBody($this->getLayout()->createBlock('searchautocomplete/autocomplete')
                    ->setSuggestData($suggestData)
                    ->setProducts($products)
                    ->setCategories($categories)
                    ->setCmsPages($cmsPages)
                    ->setBlogPosts($blogPosts)
                    ->setNews($news)
                    ->toHtml());
        } else {
            exit();
        }
    }
}
