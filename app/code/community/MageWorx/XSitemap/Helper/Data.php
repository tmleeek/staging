<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_XSitemap_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_HTML_SITEMAP_TITLE                      = 'mageworx_seo/xsitemap/sitemap_meta_title';
    const XML_PATH_HTML_SITEMAP_META_DESCRIPTION           = 'mageworx_seo/xsitemap/sitemap_meta_desc';
    const XML_PATH_HTML_SITEMAP_META_KEYWORDS              = 'mageworx_seo/xsitemap/sitemap_meta_keywords';

    const XML_PATH_SORT_ORDER                              = 'mageworx_seo/xsitemap/sort_order';
    const XML_PATH_HTML_SITEMAP_PRODUCT_URL                = 'mageworx_seo/xsitemap/product_url';
    const XML_PATH_XML_SITEMAP_URL_LENGTH                  = 'mageworx_seo/google_sitemap/product_url_length';
    const XML_PATH_XML_SITEMAP_VALIDATE_URLS_ENABLED       = 'mageworx_seo/google_sitemap/validate_urls';
    const XML_PATH_TRAILING_SLASH                          = 'mageworx_seo/xsitemap_common/trailing_slash';
    const XML_PATH_CROP_TRAILING_SLASH_HOME_PAGE           = 'mageworx_seo/xsitemap_common/trailing_slash_home_page';
    const XML_PATH_SITEMAP_FAST_METHOD                     = 'mageworx_seo/xsitemap/fast_products_generate';
    const XML_PATH_USE_INDEX                               = 'mageworx_seo/google_sitemap/use_index';
    const XML_PATH_SPLIT_SIZE                              = 'mageworx_seo/google_sitemap/split_size';
    const XML_PATH_MAX_LINKS                               = 'mageworx_seo/google_sitemap/max_links';
    const XML_PATH_CATEGORY_CHANGEFREQ                     = 'mageworx_seo/google_sitemap/category_changefreq';
    const XML_PATH_PRODUCT_CHANGEFREQ                      = 'mageworx_seo/google_sitemap/product_changefreq';
    const XML_PATH_PRODUCT_TAGS_CHANGEFREQ                 = 'mageworx_seo/google_sitemap/product_tags_changefreq';
    const XML_PATH_PAGE_CHANGEFREQ                         = 'mageworx_seo/google_sitemap/page_changefreq';
    const XML_PATH_BLOG_CHANGEFREQ                         = 'mageworx_seo/google_sitemap/blog_changefreq';
    const XML_PATH_LINK_CHANGEFREQ                         = 'mageworx_seo/google_sitemap/link_changefreq';
    const XML_PATH_CATEGORY_PRIORITY                       = 'mageworx_seo/google_sitemap/category_priority';
    const XML_PATH_PRODUCT_PRIORITY                        = 'mageworx_seo/google_sitemap/product_priority';
    const XML_PATH_PRODUCT_TAGS_PRIORITY                   = 'mageworx_seo/google_sitemap/product_tags_priority';
    const XML_PATH_PAGE_PRIORITY                           = 'mageworx_seo/google_sitemap/page_priority';
    const XML_PATH_BLOG_PRIORITY                           = 'mageworx_seo/google_sitemap/blog_priority';
    const XML_PATH_LINK_PRIORITY                           = 'mageworx_seo/google_sitemap/link_priority';
    const XML_PATH_PRODUCT_IMAGES                          = 'mageworx_seo/google_sitemap/product_images';
    const XML_PATH_ALTERNATE_URLS_ENABLED                  = 'mageworx_seo/google_sitemap/alternate_urls';

    const XML_PATH_XML_SITEMAP_EXCLUDE_OUTOFSTOCK_PRODUCT  = 'mageworx_seo/google_sitemap/exclude_out_of_stock';
    const XML_PATH_HTML_SITEMAP_EXCLUDE_OUTOFSTOCK_PRODUCT = 'mageworx_seo/xsitemap/exclude_out_of_stock';
    const XML_PATH_ITEMS_LIMIT                             = 'mageworx_seo/google_sitemap/xml_limit';
    const XML_PATH_PRODUCT_TAGS_GENERATE_ENABLED           = 'mageworx_seo/google_sitemap/product_tags';
    const XML_PATH_ADD_LINKS_FOR_XML_SITEMAP               = 'mageworx_seo/google_sitemap/add_links';
    const XML_PATH_SITEMAP_FILE_LINKS_FOR_INDEX            = 'mageworx_seo/google_sitemap/sitemapfile_links';
    const XML_PATH_BLOG_GENERATE_ENABLED                   = 'mageworx_seo/google_sitemap/blog';
    const XML_PATH_AW_BLOG_ENABLED                         = 'blog/blog/enabled';
    const XML_PATH_FISHPIG_BLOG_ENABLED                    = 'wordpress/module/enabled';
    const XML_CONFIG_WORDPRESS_SINGLE_STORE                = 'wordpress/integration/force_single_store';
    const XML_PATH_FP_ATTRIBUTE_SPLASH_GENERATE_ENABLED    = 'mageworx_seo/google_sitemap/fishpig_attribute_splash';
    const XML_PATH_FP_ATTRIBUTE_SPLASH_PAGE_CHANGEFREQ     = 'attributeSplash/sitemap/page_change_frequency';
    const XML_PATH_FP_ATTRIBUTE_SPLASH_PAGE_PRIORITY       = 'attributeSplash/sitemap/page_priority';
    const XML_PATH_FP_ATTRIBUTE_SPLASH_GROUP_CHANGEFREQ    = 'attributeSplash/sitemap/group_change_frequency';
    const XML_PATH_FP_ATTRIBUTE_SPLASH_GROUP_PRIORITY      = 'attributeSplash/sitemap/group_priority';
    const XML_PATH_FP_ATTRIBUTE_SPLASH_GROUP_ENABLED       = 'attributeSplash/list_page/enabled';
    const XML_PATH_FP_SPLASH_PRO_GENERATE_ENABLED          = 'mageworx_seo/google_sitemap/fishpig_attribute_splash_pro';
    const XML_PATH_FP_ATTRIBUTE_SPLASH_PRO_PAGE_CHANGEFREQ = 'splash/sitemap/change_frequency';
    const XML_PATH_FP_ATTRIBUTE_SPLASH_PRO_PAGE_PRIORITY   = 'splash/sitemap/priority';

    const XML_PATH_XS_ATTRIBUTE_SPLASH_PAGE_CHANGEFREQ     = 'mageworx_seo/google_sitemap/fishpig_splash_changefreq';
    const XML_PATH_XS_ATTRIBUTE_SPLASH_PAGE_PRIORITY       = 'mageworx_seo/google_sitemap/fishpig_splash_priority';
    const XML_PATH_XS_ATTRIBUTE_SPLASH_PRO_PAGE_CHANGEFREQ = 'mageworx_seo/google_sitemap/fishpig_splash_pro_changefreq';
    const XML_PATH_XS_ATTRIBUTE_SPLASH_PRO_PAGE_PRIORITY   = 'mageworx_seo/google_sitemap/fishpig_splash_pro_priority';

    const XML_PATH_USE_CSS_FOR_XML                         = 'mageworx_seo/google_sitemap/use_css_for_xml';

    protected $_storeId             = null;
    protected $_trailingSlashAction = null;
    protected $_enabledFastMethod   = false;
    protected $_enterpriseSince113  = null;


    public function getTitle()
    {
        $title = (string) Mage::getStoreConfig(self::XML_PATH_HTML_SITEMAP_TITLE);
        return trim(htmlspecialchars(html_entity_decode($title, ENT_QUOTES, 'UTF-8')));
    }

    public function getMetaDescription()
    {
        $description = (string) Mage::getStoreConfig(self::XML_PATH_HTML_SITEMAP_META_DESCRIPTION, $this->_storeId);
        return trim(htmlspecialchars(html_entity_decode($description, ENT_QUOTES, 'UTF-8')));
    }

    public function getMetaKeywords()
    {
        $keywords = (string) Mage::getStoreConfig(self::XML_PATH_HTML_SITEMAP_META_KEYWORDS, $this->_storeId);
        return trim(htmlspecialchars(html_entity_decode($keywords, ENT_QUOTES, 'UTF-8')));
    }

    public function isEnterpriseSince113()
    {
        if(is_null($this->_enterpriseSince113)){
            $mage = new Mage();
            if(method_exists($mage, 'getEdition') && Mage::getEdition() == Mage::EDITION_ENTERPRISE
                && version_compare(Mage::getVersion(), '1.13.0.0', '>=') ){
                $this->_enterpriseSince113 = true;
            }else{
                $this->_enterpriseSince113 = false;
            }
        }
        return $this->_enterpriseSince113;
    }

    public function init($storeId)
    {
        $this->_storeId = $storeId;
    }

    public function isValidateUrls()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_XML_SITEMAP_VALIDATE_URLS_ENABLED, $this->_storeId);
    }

    /*
     * Retrive magento setting
     * @return bool
     */
    public function isProductUrlUseCategory()
    {
        if (defined('Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_USE_CATEGORY')) {
            return Mage::getStoreConfig(Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_USE_CATEGORY, $this->_storeId);
        }
        elseif (is_null(Mage::getStoreConfig('catalog/seo/product_use_categories', $this->_storeId))) {
            return true;
        }
        return Mage::getStoreConfig('catalog/seo/product_use_categories', $this->_storeId);
    }

    /*
     * If don't use canonical URL from SeoBase
     * @return string
     */
    public function getXmlSitemapUrlLength()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_XML_SITEMAP_URL_LENGTH, $this->_storeId);
    }

    /**
     * Retrive setting: "product url to category"
     * @return string (yes|no|canonical)
     */
    public function getHtmlSitemapProductUrlType()
    {
        $param = (string) Mage::getStoreConfig(self::XML_PATH_HTML_SITEMAP_PRODUCT_URL, $this->_storeId);
        if ($param == 'yes' || $param == 'no') {
            return $param;
        }
        elseif ($param == 'canonical') {
            $seoHelper = Mage::helper('xsitemap/adapter_seobase')->getSeoBaseHelper('isCanonicalUrlEnabled');
            if ($seoHelper) {
                $canonicalUrlEnabled = (string) $seoHelper->isCanonicalUrlEnabled($this->_storeId);
                if ($canonicalUrlEnabled) {
                    return 'canonical';
                }
            }
        }
        return 'yes';
    }

    /**
     *
     * @return string (name|position)
     */
    public function getHtmlSitemapSort()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_SORT_ORDER, $this->_storeId);
    }

    /**
     * Retrive modify url for home page. If SeoBase module installed its setting used.
     * @param string $url
     * @return string
     */
    public function getTrailingSlashUrlForHomePage($url, $storeId)
    {
        $helper = Mage::helper('xsitemap/adapter_seobase')->getSeoBaseHelper('cropTrailingSlashForHomePageUrl');

        if(is_object($helper)){
            return $helper->cropTrailingSlashForHomePageUrl($storeId) ? trim($url ,'/') : $url;
        }

        return $this->cropTrailingSlashForHomePageUrl() ? trim($url ,'/') : $url;
    }

    /**
     * Retrive modify url. If SeoBase module installed its setting used.
     * @param string $url
     * @return string
     */
    public function trailingSlash($url)
    {
        if (is_null($this->_trailingSlashAction)) {
            $this->setTrailingSlashAction();
        }

        if ($this->_trailingSlashAction == 'add') {
            $url        = rtrim($url);
            $extensions = array('rss', 'html', 'htm', 'xml', 'php');
            if (substr($url, -1) != '/' && !in_array(substr(strrchr($url, '.'), 1), $extensions)) {
                $url.= '/';
            }
        }
        elseif ($this->_trailingSlashAction == 'crop') {
            $url = rtrim(rtrim($url), '/');
        }
        elseif ($this->_trailingSlashAction == 'default') {

        }
        else {
            $this->_trailingSlashAction == 'default';
        }

        return $url;
    }

    public function setTrailingSlashAction()
    {
        $helper = Mage::helper('xsitemap/adapter_seobase')->getSeoBaseHelper('getTrailingSlashAction');
        if ($helper) {
            $this->_trailingSlashAction = $helper->getTrailingSlashAction($this->_storeId);
            return true;
        }
        $this->_trailingSlashAction = $this->getTrailingSlashAction();
    }


    /**
     * Fast method without create product model.
     * @return bool
     */
    function isEnabledFastMethod()
    {
        return true;
        /*
         * To provide to the client a choice, to uncomment in system.xml fast_products_generate field
         */

        /*
          if(!$this->_enabledFastMethod){
          return Mage::getStoreConfigFlag(self::XML_PATH_SITEMAP_FAST_METHOD, Mage::app()->getStore()->getStoreId());
          }
          return $this->_enabledFastMethod;
         */
    }

    /**
     * Fast method without create product model
     * @param type $bool
     */
    function setEnableFastMethod($bool)
    {
        $this->_enabledFastMethod = $bool;
    }

    function getSitemapFileLinks()
    {
        $data     = Mage::getStoreConfig(self::XML_PATH_SITEMAP_FILE_LINKS_FOR_INDEX, $this->_storeId);
        $addLinks = array_filter(preg_split('/\r?\n/', $data));
        if (count($addLinks)) {
            foreach ($addLinks as $k => $link) {
                $addLinks[$k] = trim($link);
            }
        }

        if (count($addLinks)) {
            return $addLinks;
        }
        return false;
    }

    function getAdditionalLinksForXmlSitemap()
    {
        return Mage::getStoreConfig(self::XML_PATH_ADD_LINKS_FOR_XML_SITEMAP, $this->_storeId);
    }

    function getTrailingSlashAction()
    {
        return Mage::getStoreConfig(self::XML_PATH_TRAILING_SLASH, $this->_storeId);
    }

    public function cropTrailingSlashForHomePageUrl()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CROP_TRAILING_SLASH_HOME_PAGE, $this->_storeId);
    }

    function isFishpigBlogEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_FISHPIG_BLOG_ENABLED, $this->_storeId);
    }

    function isAwBlogEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_AW_BLOG_ENABLED, $this->_storeId);
    }

    function isBlogGenerateEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_BLOG_GENERATE_ENABLED, $this->_storeId);
    }

    function useIndex()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_INDEX, $this->_storeId);
    }

    function getSplitSize()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_SPLIT_SIZE, $this->_storeId) * 1024;
    }

    function getMaxLinks()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_MAX_LINKS, $this->_storeId);
    }

    function getSitemapUrl()
    {
        return Mage::getUrl('sitemap');
    }

    function getCategoryChangeFrequency()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_CATEGORY_CHANGEFREQ, $this->_storeId);
    }

    function getProductChangeFrequency()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_PRODUCT_CHANGEFREQ, $this->_storeId);
    }

    function getProductTagsChangeFrequency()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_PRODUCT_TAGS_CHANGEFREQ, $this->_storeId);
    }

    function getPageChangeFrequency()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_PAGE_CHANGEFREQ, $this->_storeId);
    }

    function getBlogChangeFrequency()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_BLOG_CHANGEFREQ, $this->_storeId);
    }

    function getLinkChangeFrequency()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_LINK_CHANGEFREQ, $this->_storeId);
    }

    function getCategoryPriority()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_CATEGORY_PRIORITY, $this->_storeId);
    }

    function getProductPriority()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_PRODUCT_PRIORITY, $this->_storeId);
    }

    function getProductTagsPriority()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_PRODUCT_TAGS_PRIORITY, $this->_storeId);
    }

    function getPagePriority()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_PAGE_PRIORITY, $this->_storeId);
    }

    function getBlogPriority()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_BLOG_PRIORITY, $this->_storeId);
    }

    function getLinkPriority()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_LINK_PRIORITY, $this->_storeId);
    }

    function isExcludeFromXMLOutOfStockProduct()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_XML_SITEMAP_EXCLUDE_OUTOFSTOCK_PRODUCT, $this->_storeId);
    }

    function isExcludeFromHTMLOutOfStockProduct()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_HTML_SITEMAP_EXCLUDE_OUTOFSTOCK_PRODUCT, $this->_storeId);
    }

    function isProductImages()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PRODUCT_IMAGES, $this->_storeId);
    }

    function getProductImageSize()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_PRODUCT_IMAGE_SIZE, $this->_storeId);
    }

    function getXmlItemsLimit()
    {
        return Mage::getStoreConfig(self::XML_PATH_ITEMS_LIMIT, $this->_storeId);
    }

    function isProductTagsGenerateEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PRODUCT_TAGS_GENERATE_ENABLED, $this->_storeId);
    }

    function isWordpressSingleStore()
    {
        return Mage::getStoreConfigFlag(self::XML_CONFIG_WORDPRESS_SINGLE_STORE);
    }

    function isFishpigAttributeSplashGenerateEnabled()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_FP_ATTRIBUTE_SPLASH_GENERATE_ENABLED);
    }

    function isFishpigAttributeSplashGroupPagesEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_FP_ATTRIBUTE_SPLASH_GROUP_ENABLED);
    }



    function getFishpigAttributeSplashPagePriority()
    {
        $priority = Mage::getStoreConfig(self::XML_PATH_FP_ATTRIBUTE_SPLASH_PAGE_PRIORITY, $this->_storeId);
        if(!$priority){
            $priority = Mage::getStoreConfig(self::XML_PATH_XS_ATTRIBUTE_SPLASH_PAGE_PRIORITY, $this->_storeId);
        }
        return $priority;
    }

    function getFishpigAttributeSplashPageFrequency()
    {
        $frequency = Mage::getStoreConfig(self::XML_PATH_FP_ATTRIBUTE_SPLASH_PAGE_CHANGEFREQ, $this->_storeId);
        if(!$frequency){
            $frequency = Mage::getStoreConfig(self::XML_PATH_XS_ATTRIBUTE_SPLASH_PAGE_CHANGEFREQ, $this->_storeId);
        }
        return $frequency;
    }

    function getFishpigAttributeSplashGroupPriority()
    {
        $priority = Mage::getStoreConfig(self::XML_PATH_FP_ATTRIBUTE_SPLASH_GROUP_PRIORITY, $this->_storeId);
        if(!$priority){
            $priority = Mage::getStoreConfig(self::XML_PATH_XS_ATTRIBUTE_SPLASH_PAGE_PRIORITY, $this->_storeId);
        }
        return $priority;
    }

    function getFishpigAttributeSplashGroupFrequency()
    {
        $frequency = Mage::getStoreConfig(self::XML_PATH_FP_ATTRIBUTE_SPLASH_GROUP_CHANGEFREQ, $this->_storeId);
        if(!$frequency){
            $frequency = Mage::getStoreConfig(self::XML_PATH_XS_ATTRIBUTE_SPLASH_PAGE_CHANGEFREQ, $this->_storeId);
        }
        return $frequency;
    }

    /**
     * Retrieve a collection of splash pages for the sitemap
     *
     * @return Fishpig_AttributeSplash_Model_Mysl4_Page_Collection
     */
    function getFishpigAttributSplashPages()
    {
        $pages = Mage::getResourceModel('attributeSplash/page_collection')
            ->addIsEnabledFilter()
            ->addStoreIdFilter($this->_storeId)
            ->load();
        return $pages;
    }

    /**
     * Retrieve a collection of splash groups for the sitemap
     *
     * @return Fishpig_AttributeSplash_Model_Mysl4_Page_Collection
     */
    function getFishpigAttributSplashGroups()
    {
        $pages = Mage::getResourceModel('attributeSplash/group_collection')
            ->addIsEnabledFilter()
            ->load();

        return $pages;
    }

    /**
     * Section Fishpig_Attribute_Splash_Pro
     */
    function isFishpigAttributeSplashProGenerateEnabled()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_FP_SPLASH_PRO_GENERATE_ENABLED);
    }

    public function getFishpigAttributSplashProPages()
    {
        $pages = Mage::getResourceModel('splash/page_collection')
            ->addStoreFilter($this->_storeId)
            ->addFieldToFilter('status', 1)
            ->load();

        return $pages;
    }

    public function getFishpigAttributSplashProChangeFrequency()
    {
        $frequency = Mage::getStoreConfig(self::XML_PATH_FP_ATTRIBUTE_SPLASH_PRO_PAGE_CHANGEFREQ, $this->_storeId);
        if(!$frequency){
            $frequency = Mage::getStoreConfig(self::XML_PATH_XS_ATTRIBUTE_SPLASH_PRO_PAGE_CHANGEFREQ, $this->_storeId);
        }
        return $frequency;
    }

    public function getFishpigAttributSplashProPriority()
    {
        $priority = Mage::getStoreConfig(self::XML_PATH_FP_ATTRIBUTE_SPLASH_PRO_PAGE_PRIORITY, $this->_storeId);
        if(!$priority){
            $priority = Mage::getStoreConfig(self::XML_PATH_XS_ATTRIBUTE_SPLASH_PRO_PAGE_PRIORITY, $this->_storeId);
        }
        return $priority;
    }

    public function getFishpigAttributSplashProLastModifiedDate(Fishpig_AttributeSplashPro_Model_Page $page)
    {
        return ($date = $page->getUpdatedAt()) ? substr($date, 0, strpos($date, ' ')) : date('Y-m-d');
    }

    public function isAlternateUrlsEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ALTERNATE_URLS_ENABLED, $this->_storeId);
    }

    public function getAlternateFinalCodes($type, $storeId)
    {
        if($this->isAlternateUrlsEnabled()){
            return Mage::helper('xsitemap/adapter_seobase')->getSeoAlternateFinalCodes($type, $storeId);
        }
        return false;
    }

    public function isUseCssForXmlSitemap()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_CSS_FOR_XML, $this->_storeId);
    }
    
    public function getCurrentFullActionName()
    {
        $controller = Mage::app()->getFrontController();
        if (is_object($controller) && is_callable(array($controller, 'getAction'))) {
            $action = $controller->getAction();
            if (is_object($action) && is_callable(array($action, 'getFullActionName'))) {
                $actionName = $action->getFullActionName();
                if ($actionName) return $actionName;
            }
        }
        return null;
    }

    public function getCategoryMaxLevel($storeId = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_CATEGORY_MAX_LEVEL, $storeId);
    }

    /**
     * Retrive home page identifier
     *
     * @param int $storeId
     * @return string
     */
    public function getHomeIdentifier($storeId = null)
    {
        return Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE, $storeId);
    }
}