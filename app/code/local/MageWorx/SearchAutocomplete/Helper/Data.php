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
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Search Autocomplete extension
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @author     MageWorx Dev Team
 */

class MageWorx_SearchAutocomplete_Helper_Data extends Mage_Core_Helper_Abstract {
    
    const XML_SEARCHAUTOCOMPLETE_ENABLED = 'mageworx_tweaks/searchautocomplete/enabled';
    const XML_SEARCHAUTOCOMPLETE_HIGHLIGHTING_ENABLED = 'mageworx_tweaks/searchautocomplete/highlighting_enabled';
    const XML_SEARCHAUTOCOMPLETE_SEPARATOR_ENABLED = 'mageworx_tweaks/searchautocomplete/separator_enabled';
    
    const XML_SEARCHAUTOCOMPLETE_SUGGESTED_SEARCHES = 'mageworx_tweaks/searchautocomplete/suggested_searches';
    const XML_SEARCHAUTOCOMPLETE_SUGGESTED_SEARCHES_RESULTS = 'mageworx_tweaks/searchautocomplete/suggested_searches_results';
    
    const XML_SEARCHAUTOCOMPLETE_SEARCH_PRODUCTS = 'mageworx_tweaks/searchautocomplete/search_products';
    const XML_SEARCHAUTOCOMPLETE_PRODUCT_IMAGE_SIZE = 'mageworx_tweaks/searchautocomplete/product_image_size';
    const XML_SEARCHAUTOCOMPLETE_PRODUCT_SEARCH_RESULTS = 'mageworx_tweaks/searchautocomplete/product_search_results';
    const XML_SEARCHAUTOCOMPLETE_PRODUCT_SEARCH_RESULTS_SORT_ORDER = 'mageworx_tweaks/searchautocomplete/product_search_results_sort_order';
    const XML_SEARCHAUTOCOMPLETE_PRODUCT_SEARCH_RESULT_FIELDS = 'mageworx_tweaks/searchautocomplete/product_search_result_fields';
    const XML_SEARCHAUTOCOMPLETE_PRODUCT_SHORT_DESCRIPTION_SIZE = 'mageworx_tweaks/searchautocomplete/product_short_description_size';
    const XML_SEARCHAUTOCOMPLETE_PRODUCT_TITLE_SIZE = 'mageworx_tweaks/searchautocomplete/product_title_size';
    
    const XML_SEARCHAUTOCOMPLETE_SEARCH_CATEGORIES = 'mageworx_tweaks/searchautocomplete/search_categories';
    const XML_SEARCHAUTOCOMPLETE_CATEGORY_SEARCH_RESULTS = 'mageworx_tweaks/searchautocomplete/category_search_results';    
    
    const XML_SEARCHAUTOCOMPLETE_SEARCH_CMS = 'mageworx_tweaks/searchautocomplete/search_cms';
    const XML_SEARCHAUTOCOMPLETE_CMS_SEARCH_RESULT_FIELDS = 'mageworx_tweaks/searchautocomplete/cms_search_result_fields';

    const XML_SEARCHAUTOCOMPLETE_SEARCH_BLOG = 'mageworx_tweaks/searchautocomplete/search_blog';

    const XML_SEARCHAUTOCOMPLETE_SHOW_PRODUCT_RESULTS_GROUPED_BY_CATEGORIES = 'mageworx_tweaks/searchautocomplete/show_product_results_grouped_by_categories';
    
    public function isEnabled() {
        return Mage::getStoreConfigFlag(self::XML_SEARCHAUTOCOMPLETE_ENABLED);
    }
    
    public function isHighlightingEnabled() {
        return (int)Mage::getStoreConfigFlag(self::XML_SEARCHAUTOCOMPLETE_HIGHLIGHTING_ENABLED);
    }
    
    public function isSeparatorEnabled() {
        return Mage::getStoreConfigFlag(self::XML_SEARCHAUTOCOMPLETE_SEPARATOR_ENABLED);
    }    
    
    public function isSuggestedSearches() {
        return Mage::getStoreConfigFlag(self::XML_SEARCHAUTOCOMPLETE_SUGGESTED_SEARCHES);
    }
    
    public function getSuggestedSearchesResults() {
        return Mage::getStoreConfig(self::XML_SEARCHAUTOCOMPLETE_SUGGESTED_SEARCHES_RESULTS);
    }
    
    public function isSearchProducts() {
        return Mage::getStoreConfigFlag(self::XML_SEARCHAUTOCOMPLETE_SEARCH_PRODUCTS);
    }    
    
    public function getProductImageSize() {
        $size = Mage::getStoreConfig(self::XML_SEARCHAUTOCOMPLETE_PRODUCT_IMAGE_SIZE);
        $size = explode('x', trim($size));

        return $size;
    }

    public function getProductSearchResults() {
        return Mage::getStoreConfig(self::XML_SEARCHAUTOCOMPLETE_PRODUCT_SEARCH_RESULTS);
    }

    public function getProductSearchResultsSortOrder() {
        return Mage::getStoreConfig(self::XML_SEARCHAUTOCOMPLETE_PRODUCT_SEARCH_RESULTS_SORT_ORDER);
    }

    public function getProductShortDescriptionSize() {
        return Mage::getStoreConfig(self::XML_SEARCHAUTOCOMPLETE_PRODUCT_SHORT_DESCRIPTION_SIZE);
    }
    
    public function getProductTitleSize() {
        return Mage::getStoreConfig(self::XML_SEARCHAUTOCOMPLETE_PRODUCT_TITLE_SIZE);
    }
    
    public function getProductSearchResultFields() {
        return explode(',', Mage::getStoreConfig(self::XML_SEARCHAUTOCOMPLETE_PRODUCT_SEARCH_RESULT_FIELDS));
    }
    

    public function isSearchCategories() {
        return Mage::getStoreConfigFlag(self::XML_SEARCHAUTOCOMPLETE_SEARCH_CATEGORIES);
    }
    
    public function getCatalogSearchResults() {
        return Mage::getStoreConfig(self::XML_SEARCHAUTOCOMPLETE_CATEGORY_SEARCH_RESULTS);
    }   

    public function isSearchCms() {
        return Mage::getStoreConfigFlag(self::XML_SEARCHAUTOCOMPLETE_SEARCH_CMS);
    }

    public function isSearchBlog() {
        return Mage::getStoreConfigFlag(self::XML_SEARCHAUTOCOMPLETE_SEARCH_BLOG) && (string)Mage::getConfig()->getModuleConfig('AW_Blog')->active == 'true';
    }
    
    public function getCmsSearchResultFields() {
        return explode(',', Mage::getStoreConfig(self::XML_SEARCHAUTOCOMPLETE_CMS_SEARCH_RESULT_FIELDS));        
    }
    
    public function getSearchType($storeId = null) {
        return Mage::getStoreConfig(Mage_CatalogSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_TYPE, $storeId);
    }

    public function isShowProductResultsGroupedByCategories() {
        return Mage::getStoreConfig(self::XML_SEARCHAUTOCOMPLETE_SHOW_PRODUCT_RESULTS_GROUPED_BY_CATEGORIES);
    }
    
    public function isSearchCmspro() {
        return Mage::getStoreConfigFlag('mageworx_tweaks/searchautocomplete/search_cmspro') && (string)Mage::getConfig()->getModuleConfig('MW_Cmspro')->active == 'true';
    }
    
    public function getCmsproSearchResultFields() {
        return explode(',', Mage::getStoreConfig('mageworx_tweaks/searchautocomplete/search_cmspro_result_fields'));
    }
    public function getPopUpDelay() {
        return (int)Mage::getStoreConfig('mageworx_tweaks/searchautocomplete/popup_delay');
    }
    public function limitText($str, $limit) {
        $queryText = Mage::helper('catalogSearch')->getQueryText();
        $str = preg_replace('/[ ]{2,}/', ' ', $str);
        $words = explode(' ', $str);
        $count = count($words);
        if ($count > $limit) {
            $offset = 0;
            foreach ($words as $key => $word) {
                if (preg_match('/(' . $queryText . ')/is', $word)) {
                    $offset = $key;
                    break;
                }
            }
            if ($offset + $limit / 2 > $count) {
                $str = '...' . implode(' ', array_slice($words, $count - $limit));
            } else if ($offset - $limit / 2 < 0) {
                $str = implode(' ', array_slice($words, 0, $limit)) . '...';
            } else {
                $str = '...' . implode(' ', array_slice($words, $offset - $limit / 2, $limit)) . '...';
            }
        }
        return $this->highlightText($str);
    }
        
    public function highlightText($str,$query = null) {
        if (!$this->isHighlightingEnabled())
            return $str;
        if (is_null($query)) {
            $q = Mage::helper('catalogsearch')->getEscapedQueryText();
            $q = preg_quote($q, '/');
        } else {
            $q = $query;
        }
        //return preg_replace('/('.$q.')/is', '<highlight>\\1</highlight>', $str);   
        return preg_replace('/('.$q.')(?:(?![^>]*(?:".*>))|(?=[^>]*(?:<a\s)))/is', '<span class="highlight">\\1</span>', $str); 
        
    }    

    public function getMoreResultsUrl() {
        return Mage::helper('catalogsearch')->getResultUrl(Mage::helper('catalogsearch')->getQueryText(Mage::helper('catalogsearch')->getQuery()->getQueryText()));
    }

    public function sanitizeContent($page) {
        $processor = Mage::getModel('searchautocomplete/cms_content_filter');
        
        if (is_object($page)) {
            $text = ($page->getContent()) ? $page->getContent() : $page->getPostContent();
        } else {
            $text = strval($page);
        }
        
        $text = preg_replace_callback('@{{.*?}}@si','MageWorx_SearchAutocomplete_Helper_Data::loadWidget', $text);        
        
        if (is_object($page)) {
            $designSettings = $processor->getDesignSettings();
            $designSettings->setArea('frontend');
            $arStoreId = $page->getStoreId();
            if (is_array($arStoreId) && count($arStoreId)>0) $storeId = $arStoreId[0]; else $storeId = intval($arStoreId);            
            $designSettings->setStore($storeId);       
            $text = $processor->process($text);
        }
        
        
        $search = array('@&lt;script.*?&gt;.*?&lt;/script&gt;@si', '@&lt;style.*?&gt;.*?&lt;/style&gt;@si');
        $replace = array('', '');
        $text = trim(strip_tags(preg_replace($search, $replace, $text)));    
        $result = $this->limitText($text, 8);
        if ($result!='' && strlen($text)>strlen($result)) $result .= '...';
        return $this->highlightText($result);
    }

    public static function loadWidget($match) {
        if (version_compare(Mage::getVersion(), '1.4.0', '<=')) return $match[0];
        $widget = str_replace(array('{{widget','}}'), '', $match[0]);
        $html = Mage::getModel('widget/template_filter')->widgetDirective(array($match[0],'widget',$widget));
        return $html;
    }
    public function setLastQueryText($query) {
        if ($this->isHighlightingEnabled()) {
            Mage::getSingleton('core/session')->setData('lastsearchquery', $query);
        }
    }
    public function getLastQueryText(){
        $referer = false;
        if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],'catalogsearch/result') > 0){
            $referer = true;
        }
        $popup = false;
        if(Mage::app()->getRequest()->getParam('ref') == '1'){
            $popup = true;
        }
        if ($this->isHighlightingEnabled() && ($referer || $popup)) {
            return Mage::getSingleton('core/session')->getData('lastsearchquery');
        }
        return null;
    }
    public function getHttpRefferer() {
        return strtolower(Mage::helper('core/http')->getHttpReferer(true));
    }
}