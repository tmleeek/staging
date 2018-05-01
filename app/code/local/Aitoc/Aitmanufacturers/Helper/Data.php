<?php ob_start(); ?>
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
class Aitoc_Aitmanufacturers_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_CONFIG_USE_PAGE_CANONICAL_TAG = 'catalog/aitmanufacturers/use_page_canonical_tag';
    
    const URLPREFIX_BRANDS = 'brands';
    
    private $_config;
    private $_attributeCode;
    private $_storeId;
    
    public function getConfigParam($param, $code, $storeId = null)
    {
        if (!$storeId)
        {
            $storeId = Mage::app()->getStore()->getId();
        }
        
        if (($this->_attributeCode != $code) || ($this->_storeId != $storeId))
        {
            $this->_attributeCode = $code;
            $this->_storeId = $storeId;
            $this->_config  = Mage::getModel('aitmanufacturers/config')->getScopeConfig($code, 'store', $storeId);
        }
        if (isset($this->_config[$param]))
        {
            return $this->_config[$param];
        } else {
            return false;
        }
    }

    public function getIsActive($attributeCode)
    {
        return Mage::getModel('aitmanufacturers/config')->getIsActive($attributeCode);
    }

    public function toUrlKey($string)
    {
        /*$urlKey = preg_replace(array('/[^a-z0-9-_]/i', '/[ ]{2,}/', '/[ ]/'), array(' ', ' ', '-'), $string);
        if (empty($urlKey)){
            $urlKey = time();
        }*/

        $string = str_replace(" ", "-", $string);
        $vowels = array("â", "Ä", "à", "á", "â", "ã", "ä", "ã¨" );
        $string = str_replace($vowels, "a", $string);

        $vowels = array("ê","é","è" ,"ê" ,"ë");
        $string = str_replace($vowels, "e", $string);

        $vowels = array("ò", "ó", "ô", "õ" ,"ö", "Ò" ,"Ó","Ô","Õ","Ö");
        $string = str_replace($vowels, "o", $string);

        $vowels = array("ù","ú","û","ü","Ù","Ú","Û","Ü" );
        $string = str_replace($vowels, "u", $string);

        $vowels = array("Ç","ç");
        $string = str_replace($vowels, "c", $string);

        $vowels = array("ñ");
        $string = str_replace($vowels, "n", $string);

        $vowels = array("Ì","","Î","ì","í","î","ï","");
        $string = str_replace($vowels, "i", $string);

        $vowels = array("ý","ÿ");
        $string = str_replace($vowels, "y", $string);

        $vowels = array("œ" );
        $string = str_replace($vowels, "oe", $string);

        $vowels = array("Ø" );
        $string = str_replace($vowels, "", $string);
        $string = str_replace("'", "-", $string);
        $string = str_replace('"', "-", $string);

        $vowels = array('/','=',',','!','@','#','$','%','^','&','*','(',')','_','|','+','€','«','»','°');
        $string = str_replace($vowels, "-", $string);

        $vowels = array(' – ');
        $string = str_replace($vowels, "-", $string);

        $vowels = array('--','---','----');
        $string = str_replace($vowels, "-", $string);
          if (empty($string)){
            $string = time();
        }
        //echo $string; exit;
        return strtolower($string);
    }



    public function getManufacturersUrl($attributeCode = null, $storeId = null)
    {
        if ($urlPrefix = $this->getConfigParam('url_prefix', $attributeCode, $storeId))
        {
            if ($this->_ifConvertUrlPrefixManufacturerToBrand($urlPrefix))
            {
                $urlPrefix = self::URLPREFIX_BRANDS;
            }
            return Mage::getModel('core/url')->getUrl($urlPrefix);
        }
        return false;
    }

    public function getAttributeId($storeId = null, $attributeCode = 'brand')
    {
        if ($attributeCode = $this->getConfigParam('attribute_code', $attributeCode, $storeId))
        {
            return Mage::getModel('aitmanufacturers/config')->getAttributeId($attributeCode);
        }
        return 0;
    }

    public function getAttributeName($attributeCode)
    {
        return Mage::getModel('aitmanufacturers/config')->getAttributeName($attributeCode);
    }

    public function getColumnsNum($attributeCode)
    {
        $num = $this->getConfigParam('columns_num', $attributeCode, Mage::app()->getStore()->getId());
        return $num > 0 ? $num : 2;
    }

    public function getBriefNum($attributeCode)
    {
        $num = $this->getConfigParam('brief_num', $attributeCode, Mage::app()->getStore()->getId());
        return ($num > 0) ? $num : 9999;
    }

    public function getShowLogo($attributeCode)
    {
        return $this->getConfigParam('show_logo', $attributeCode, Mage::app()->getStore()->getId());
    }
    
    public function getShowLink($attributeCode)
    {
        return $this->getConfigParam('show_link', $attributeCode, Mage::app()->getStore()->getId());
    }
    
    public function getShowListImage($attributeCode )
    {
        return $this->getConfigParam('show_list_image', $attributeCode, Mage::app()->getStore()->getId());
    }
    
	public function getPageTitle($attributeCode)
    {
        $pageTitle = $this->getConfigParam('page_title', $attributeCode, Mage::app()->getStore()->getId());
        return $pageTitle ? $pageTitle : '';
    }
	
	public function getMetaKeywords($attributeCode)
    {
        $metaKeywords = $this->getConfigParam('meta_keywords', $attributeCode, Mage::app()->getStore()->getId());
        return $metaKeywords ? $metaKeywords : '';
    }
	
	public function getMetaDescription($attributeCode)
    {
        $metaDescription = $this->getConfigParam('meta_description', $attributeCode, Mage::app()->getStore()->getId());
        return $metaDescription ? $metaDescription : '';
    }
    
    public function getManufacturerLink($product)
    {
        $attributes = Mage::getModel('aitmanufacturers/config')->getAttributesByScope(Mage::app()->getStore()->getId());
        //echo "<pre>"; print_r($attributes); exit;
        $html = '';
        foreach ($attributes as $attribute)
        {
            $manufacturerId = $product->getData($attribute['attribute_code']);
            $manufacturer = Mage::getModel('aitmanufacturers/aitmanufacturers')->loadByManufacturer($manufacturerId);
            if ($manufacturer->getId() && $manufacturer->getStatus() != 2)
            {
                $logo = $manufacturer->getSmallLogo();
                if ($logo && $attribute['show_logo']){
                    $html .= '<a href="'.$manufacturer->getUrl().'"><img src="'.Mage::getBaseUrl('media').'aitmanufacturers/logo/'. $logo.'" alt="'.$this->htmlEscape($manufacturer->getManufacturer()).'" title="'.$this->htmlEscape($manufacturer->getManufacturer()).'" /></a><br />';
                }
                if ($attribute['show_link']){
                    $html .= '<a href="'.$manufacturer->getUrl().'">'.$this->__('See other %s products', $manufacturer->getManufacturer()).'</a><br />';
                }
            }
        }
        return $html;
    }
	
	public function getNewanufacturerLink($manufacturerId,$collection_Id)
    {
        

        $html = '';
        $manufacturer = Mage::getModel('aitmanufacturers/aitmanufacturers')->loadByManufacturer($manufacturerId);

        if ($manufacturer->getId() && $manufacturer->getStatus() != 2)
        {
            $logo = $manufacturer->getSmallLogo();
            if ($logo)
            {
                if($_SERVER['HTTPS'] == "on")
                {
                    $manufacturerurl = trim($manufacturer->getUrl(),'https:');
                }
                else
                {
                    $manufacturerurl = $manufacturer->getUrl();
                }
                //echo $manufacturerurl;
                $html .= '<a href="'.$manufacturerurl.'"><img src="'.Mage::getBaseUrl('media').'aitmanufacturers/logo/'. $logo.'" alt="'.$this->htmlEscape($manufacturer->getManufacturer()).'" title="'.$this->htmlEscape($manufacturer->getManufacturer()).'" /></a><br />';
            }
            //$admin_set_brand_url_new=$aitio_model->getlistgammeUrl($store,$gamme,$marque);
            $html .= '<a href="'.$manufacturerurl.'">'.$this->__('See other %s products', $manufacturer->getManufacturer()).'</a><br />';

        }

        $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->loadByManufacturer($collection_Id);
        if ($collection->getId() && $collection->getStatus() != 2)
        {
            $list_url= $collection->getUrlKey().'.html';
            $list_key_url_brand = $manufacturer->getUrlKey();
            $list_final = $list_key_url_brand.'/'.$list_url;

            $logo = $collection->getSmallLogo();
	        if ($logo)
            {
	            $html .= '<a href="'.$list_final.'"><img src="'.Mage::getBaseUrl('media').'aitmanufacturers/logo/'. $logo.'" alt="'.$this->htmlEscape($collection->getManufacturer()).'" title="'.$this->htmlEscape($collection->getManufacturer()).'" /></a><br />';
	        }

	        $html .= '<a href="'.$list_final.'">'.$this->__('See other %s products', $collection->getManufacturer()).'</a><br />';
        }

		/*if($collection_Id != "")
		{
            $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->loadByManufacturer($collection_Id);

             $list_colls= Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();
                  $list_colls->addFieldToFilter('manufacturer_id',$collection_Id)->addStoreFilter(Mage :: app()->getStore())->getFirstItem();

                  foreach($list_colls as $list)
                     {
                         $list_key_url=$list['url_key'];
                        if($list_key_url!='')
                        {
                            $list_url=$list_key_url.'.html';
                        }
                     }

                  $list_colls_brand= Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();
                  $list_colls_brand->addFieldToFilter('manufacturer_id',$marque)->addStoreFilter(Mage :: app()->getStore())->getFirstItem();

                  foreach($list_colls_brand as $list_data)
                  {
                     $list_key_url_brand=$list_data['url_key'];
                  }


                   $list_final=$list_key_url_brand.'/'.$list_url;

	            if ($collection->getId() && $collection->getStatus() != 2)
	            {
	                $logo = $collection->getSmallLogo();
	                if ($logo){
	                    $html .= '<a href="'.$list_final.'"><img src="'.Mage::getBaseUrl('media').'aitmanufacturers/logo/'. $logo.'" alt="'.$this->htmlEscape($collection->getManufacturer()).'" title="'.$this->htmlEscape($collection->getManufacturer()).'" /></a><br />';
	                }

	                $html .= '<a href="'.$list_final.'">'.$this->__('See other %s products', $collection->getManufacturer()).'</a><br />';

	            }
			}*/

        return $html;
    }

    public function getCustommanufacturerLink($marque,$collection)
    {
        $html = '';
        $list_colls= Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();
        $list_colls->addFieldToFilter('manufacturer_id',$marque)->addStoreFilter(Mage :: app()->getStore())->getFirstItem();
        foreach($list_colls as $manufacturer)
        {
         $list_key_url=$manufacturer['url_key'];

                /*$logo = $manufacturer->getSmallLogo();
                if ($logo){
                    $html .= '<a href="'.$manufacturer->getUrl().'"><img src="'.Mage::getBaseUrl('media').'aitmanufacturers/logo/'. $logo.'" alt="'.$this->htmlEscape($manufacturer->getManufacturer()).'" title="'.$this->htmlEscape($manufacturer->getManufacturer()).'" /></a><br />';
                }*/

                $html .= '<a href="'.$manufacturer->getUrl().'">'.$this->__('See other %s products', $manufacturer->getManufacturer()).'</a><br />';


        }
        return $html;
    }
    public function checkUrlPrefix($attributeUrlPrefix, $storeId)
    {
        $collection = Mage::getModel('aitmanufacturers/config')->getResourceCollection()->loadByStoreId($storeId);
        
        if ($collection->count())
        {
            foreach ($collection as $item)
            {
                $isAttributeMatched = strtolower($item->getUrlPrefix()) == strtolower($attributeUrlPrefix);
                
                $itemUrlPrefix = $item->getUrlPrefix();
                $isManfucaturerPrefixReplaced = $this->_ifConvertUrlPrefixManufacturerToBrand($itemUrlPrefix)
                    && strtolower($attributeUrlPrefix) == self::URLPREFIX_BRANDS;
                if ($isAttributeMatched || $isManfucaturerPrefixReplaced)
                {
                    return $item->getAttributeCode();
                }
            }
        }
        return false;
    }
    
    public function getSeoBrandsUrl()
    {
    	return $this->_getUrl('catalog/seo_sitemap/attributes');
    }
    
    /**
     * @return bool
     */
    public function isLNPEnabled()
    {        
        return $this->isModuleEnabled('AdjustWare_Nav');
    }
	
	public function getLNPVersion()
    {
        return (string) Mage::getConfig()->getNode()->modules->AdjustWare_Nav->version;
    }
    
    /**
     * 
     * @return boolean
     */
    public function canUseLayeredNavigation($attribute, $code = false)
    {        
        $session = Mage::getSingleton('core/session');
        $action = Mage::app()->getFrontController()->getAction()->getFullActionName();

        if ('catalog_category_view' == $action)
        {
            $session->setLayeredNavigationUsedFromAitocManufacturersModule(false);
        }
        
        $isValidAction = (bool) in_array($action, array('aitmanufacturers_index_view', 'adjnav_ajax_category'));
        $isLayeredNavigationUsedFromAitocManufacturersBrandsPage = $session->getLayeredNavigationUsedFromAitocManufacturersModule();        
        
        $attributeCode = $code?$attribute:Mage::getModel('aitmanufacturers/config')->getAttributeCodeById($attribute);
		if(!$attributeCode) {
            $attributeCode = Mage::app()->getRequest()->get('shopby_attribute');
        }
        $canUse = $this->getConfigParam('layered_navigation', $attributeCode, Mage::app()->getStore()->getId());
        $canUse &= $isValidAction; // Valid front action        
        
        // Check for ajax request is done from aitocmanufacturers brand view page, not from catalog category view page
        $canUse &= ($this->isLNPEnabled() ? $isLayeredNavigationUsedFromAitocManufacturersBrandsPage : true); 
                
        return $canUse;
    }
    
    /**
     *
     * @param Varien_Db_Select $select 
     */
    public function adddSortableAttributes(Varien_Db_Select $select)
    {
        $nameAttribute = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setCodeFilter('name')
            ->getFirstItem();

        $store = Mage::app()->getStore();
        $select->joinLeft(
            array('_table_name_default' => 'catalog_product_entity_varchar'),
            '(_table_name_default.entity_id = e.entity_id) AND (_table_name_default.attribute_id=\'' . $nameAttribute->getId() . '\') AND _table_name_default.store_id=0',
            null
        );

        $select->joinLeft(
            array('_table_name' => 'catalog_product_entity_varchar'),
            '(_table_name.entity_id = e.entity_id) AND (_table_name.attribute_id=\'' . $nameAttribute->getId() . '\') AND (_table_name.store_id=\'' . $store->getId() . '\')',
            new Zend_Db_Expr('IF(_table_name.value_id>0, _table_name.value, _table_name_default.value) AS `name`')
        );
    }

    /**
     * Check is module exists and enabled in global config.
     * Copied from Mage_Core for 1.4.0.x compatibility
     *
     * @param string $moduleName the full module name, example Mage_Core
     * @return boolean
     */
    public function isModuleEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->_getModuleName();
        }

        if (!Mage::getConfig()->getNode('modules/' . $moduleName)) {
            return false;
        }

        $isActive = Mage::getConfig()->getNode('modules/' . $moduleName . '/active');
        if (!$isActive || !in_array((string)$isActive, array('true', '1'))) {
            return false;
        }
        return true;
    }

    public function canUseCanonicalTag($store = null)
    {
        return Mage::getStoreConfig(self::XML_CONFIG_USE_PAGE_CANONICAL_TAG, $store);
    }

    public function getUrlPattern($id)
    {
        $storeId = Mage::app()->getStore()->getId();
        $modelConfig = Mage::getModel('aitmanufacturers/config');
        $code = $modelConfig->getAttributeCodeById($modelConfig->getAttributeIdByOption($id));
        $attributeConfig = $modelConfig->getScopeConfig($code, 'store', $storeId);
        $urlPattern = $attributeConfig['url_pattern'];
        return $urlPattern;
    }

    public function generateUrl($url, $id)
    {
        $urlPattern = $this->getUrlPattern($id);
        $pattern = str_replace('[attribute]', '', $urlPattern);
        return Mage::getBaseUrl().$url.$pattern;
    }

    public function isActiveMenuMain($code)
    {
        $urlAlias = Mage::app()->getRequest()->getAliases();

        if($this->isConvertFromManufactToBrand() && trim(reset($urlAlias), '/') == self::URLPREFIX_BRANDS)
        {
            $code = self::URLPREFIX_BRANDS;
        }

        if (isset($urlAlias['rewrite_request_path']) && $code == trim($urlAlias['rewrite_request_path'], '/'))
        {
            return true;
        }

        return false;
    }

    public function isActiveMenuSub($code, $manufacturers, $manufacturerId = false)
    {
        $storeId = Mage::app()->getStore()->getId();
        $modelConfig = Mage::getModel('aitmanufacturers/config');

        $attributeConfig = $modelConfig->getScopeConfig($code, 'store', $storeId);
        $rewritePattern = $attributeConfig['url_pattern'];

        $urlAlias = Mage::app()->getRequest()->getAliases();
        $route = Mage::app()->getRequest()->getRouteName();

        if (count($manufacturers))
        {
            foreach ($manufacturers as $manufacturer)
            {
                if ($route == 'aitmanufacturers' && (isset($urlAlias['rewrite_request_path']) && $urlAlias['rewrite_request_path'] == str_replace('[attribute]', $manufacturer['item']->getUrlKey(), $rewritePattern)))
                {
                    if ($manufacturerId)
                    {
                        if ($manufacturerId == $manufacturer['item']->getManufacturerId())
                        {
                            return true;
                        }
                    }
                    else
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }
    
    public function isConvertFromManufactToBrand()
    {
        return Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_replace_manufacturer');
    }
    
    private function _ifConvertUrlPrefixManufacturerToBrand( $urlPrefix )
    {
        return Mage::getStoreConfigFlag('catalog/aitmanufacturers/manufacturers_replace_manufacturer') 
            && strtolower($urlPrefix) == 'manufacturer';
    }
    
}
?>
<?php ob_end_flush(); ?>