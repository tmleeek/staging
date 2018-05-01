<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Alternatelang
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2016 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/
 * =============================================================
 */

class Magmodules_Alternatelang_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getAlternateData()
	{
		if(Mage::getStoreConfig('alternatelang/general/enabled')) {

			$alternate_urls = array();
			$router = Mage::app()->getRequest()->getRouteName();
			$controller = Mage::app()->getRequest()->getControllerName();

			$storeId = Mage::app()->getStore()->getStoreId();
			$stores = $this->getConnectedStores($storeId);
			$alternate_urls['stores'] = $stores;

			if(count($stores) > 1) {

				// CMS PAGES
				if(($router == 'cms') && (Mage::getStoreConfig('alternatelang/config/cms')) && ($controller != 'index')) {
					$cms_identifier = Mage::getBlockSingleton('cms/page')->getPage()->getIdentifier();
					$cms_category = Mage::getBlockSingleton('cms/page')->getPage()->getAlternateCategory();
					$cms_id = Mage::getBlockSingleton('cms/page')->getPage()->getId();
					foreach ($stores as $store_alternate) {
						$url = '';
						$store_alternate_language_code = $store_alternate['language_code'];
						$store_alternate = Mage::getModel('core/store')->load($store_alternate['store_id']);
						if(Mage::getStoreConfig('alternatelang/config/cms', $store_alternate->getId())) {
							$page = Mage::getModel('cms/page')->setStoreId($store_alternate->getId())->load($cms_id);
							$cat = Mage::getModel('cms/page')->setStoreId($store_alternate->getId())->load($cms_category, 'alternate_category');
                            $cmsUrlSuffix = $this->_getCmsUrlSuffix();
							if ($page->getIdentifier()) {
                                $url = $store_alternate->getBaseUrl() . $page->getIdentifier() . $cmsUrlSuffix;
                            }
							if ($cat->getIdentifier()) {
                                $url = $store_alternate->getBaseUrl() . $cat->getIdentifier() . $cmsUrlSuffix;
                            }
						}
						if($url) {
							if($store_alternate_language_code) {
								$alternate_urls['urls'][$store_alternate_language_code] = $url;
							} else {
								$store_locale = substr(Mage::getStoreConfig('general/locale/code', $store_alternate->getId()),0,2);
								$alternate_urls['urls'][$store_locale] = $url;
							}
						}
					}
				}

				// HOME PAGE
				if(($router == 'cms') && (Mage::getStoreConfig('alternatelang/config/homepage')) && ($controller == 'index')) {
					foreach ($stores as $store_alternate) {
						$store_alternate_language_code = $store_alternate['language_code'];
						$store_alternate = Mage::getModel('core/store')->load($store_alternate['store_id']);
						if(Mage::getStoreConfig('alternatelang/config/homepage', $store_alternate->getId())) {
							if($store_alternate_language_code) {
								$alternate_urls['urls'][$store_alternate_language_code] = $store_alternate->getBaseUrl();
							} else {
								$store_locale = substr(Mage::getStoreConfig('general/locale/code', $store_alternate->getId()),0,2);
								$alternate_urls['urls'][$store_locale] = $store_alternate->getBaseUrl();
							}
						}
					}
				}

				// PRODUCT PAGE
				if(($product = Mage::registry('current_product')) && (Mage::getStoreConfig('alternatelang/config/product'))) {
					if($canonical_exclusive = Mage::getStoreConfig('alternatelang/config/canonical_exclusive', $storeId)) {
						if(Mage::helper('catalog/product')->canUseCanonicalTag()) {
							$params = array('_ignore_category' => true);
							$canonical_url = Mage::getModel('catalog/product')->getUrlModel()->getUrl($product, $params);
							$current_url = str_replace('?show-alternate', '', Mage::helper('core/url')->getCurrentUrl());
							if($canonical_url != $current_url) {
								$alternate_urls['errors'] = Mage::helper('alternatelang')->__('There is no alternate URL for this page as this URL is not the canonical URL. The canonical URL for this page is: %s', $canonical_url);
								$canonical_error = true;
							}

						}
					}

            		if(!isset($canonical_error)) {
						foreach ($stores as $store_alternate) {
							$store_alternate_language_code = $store_alternate['language_code'];
							$store_alternate = Mage::getModel('core/store')->load($store_alternate['store_id']);
							if(Mage::getStoreConfig('alternatelang/config/product', $store_alternate->getId())) {
								if($url = $this->getCoreProductUrl($product->getId(), $store_alternate->getId())) {
									$url = $store_alternate->getBaseUrl() . $url;
									if($store_alternate_language_code) {
										$alternate_urls['urls'][$store_alternate_language_code] = $url;
									} else {
										$store_locale = substr(Mage::getStoreConfig('general/locale/code', $store_alternate->getId()),0,2);
										$alternate_urls['urls'][$store_locale] = $url;
									}
								}
							}
							$current = Mage::helper('alternatelang')->getCoreProductUrl($product->getId(), $storeId);
						}
					}
				}

				// CATEGORY PAGE
				if(($category = Mage::registry('current_category')) && (Mage::getStoreConfig('alternatelang/config/category')) && (!Mage::registry('current_product'))) {
					foreach ($stores as $store_alternate) {
						$store_alternate_language_code = $store_alternate['language_code'];
						$store_alternate = Mage::getModel('core/store')->load($store_alternate['store_id']);
						if(Mage::getStoreConfig('alternatelang/config/category', $store_alternate->getId())) {
							if($url = Mage::helper('alternatelang')->getCoreCategoryUrl($category->getId(), $store_alternate->getId())) {
								$url = $store_alternate->getBaseUrl() . $url;
								if($store_alternate_language_code) {
									$alternate_urls['urls'][$store_alternate_language_code] = $url;
								} else {
									$store_locale = substr(Mage::getStoreConfig('general/locale/code', $store_alternate->getId()),0,2);
									$alternate_urls['urls'][$store_locale] = $url;
								}
							}
						}
						$current = Mage::helper('alternatelang')->getCoreCategoryUrl($category->getId(), $storeId);
					}
				}

                // BRAND PAGE

                if((!Mage::registry('current_category')) && (!Mage::registry('current_product')) && ($router == 'aitmanufacturers'))
                {
                    $request = Mage::app()->getRequest();
                    $re_org_path = $request->getOriginalPathInfo();
                    //$currentUrl = $this->helper('core/url')->getCurrentUrl();


                    $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                    $read= Mage::getSingleton('core/resource')->getConnection('core_read');

                    $org_path = substr($re_org_path, 1, strpos($re_org_path, '.html') -  1);
                    $explodedPath = explode('/', $org_path);
                    $url_count = count($explodedPath);

                    $manufacture_attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', "manufacturer");
                    $manufacture_id = $manufacture_attribute->getId();

                    $manufacture_option = 'SELECT manufacturer_id from `aitmanufacturers` where `url_key` = "'.$explodedPath[0].'"';
                    $manufacture_option_id = $read->fetchOne($manufacture_option);

                    //$manufacture_option = 'SELECT a.option_id from `eav_attribute_option_value` as a inner join `eav_attribute_option` as s on a.`option_id` = s.`option_id` where s.`attribute_id` = '.$manufacture_id.' and a.`value`= "'.$explodedPath[0].'"';
                    //$manufacture_option_id = $read->fetchOne($manufacture_option);
                    //print_r($manufacture_option_id);
                    //exit;
                    if(!empty($manufacture_option_id))
                    {
                        if($url_count > 1)
                        {
                            $collection_attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', "gamme_collection_new ");
                            $collection_id = $collection_attribute->getId();

                            $collection_option = 'SELECT manufacturer_id from `aitmanufacturers` where `url_key` = "'.$explodedPath[1].'"';
                            $collection_option_id = $read->fetchOne($collection_option);

                            //$collection_option = 'SELECT a.option_id from `eav_attribute_option_value` as a inner join `eav_attribute_option` as s on a.`option_id` = s.`option_id` where s.`attribute_id` = '.$collection_id.' and a.`value`= "'.$explodedPath[1].'"';
                            //$collection_option_id = $read->fetchOne($collection_option);
                        }

                        foreach ($stores as $store_alternate)
                        {
                            $marque_collection_url = '';
    				        $store_alternate_language_code = $store_alternate['language_code'];
    						$store_alternate = Mage::getModel('core/store')->load($store_alternate['store_id']);

                            $sql_marque = 'SELECT a.value from `eav_attribute_option_value` as a inner join `eav_attribute_option` as s on a.`option_id` = s.`option_id` where a.`option_id` = '.$manufacture_option_id.' and s.`attribute_id` = '.$manufacture_id.' and a.`store_id`= "'.$store_alternate['store_id'].'"';
                            $sql_marque_data = $read->fetchOne($sql_marque);

                            if(empty($sql_marque_data))
                            {
                                $sql_marque_default = 'SELECT a.value from `eav_attribute_option_value` as a inner join `eav_attribute_option` as s on a.`option_id` = s.`option_id` where a.`option_id` = '.$manufacture_option_id.' and s.`attribute_id` = '.$manufacture_id.' and a.`store_id`= 0';
                                $sql_marque_data_default = $read->fetchOne($sql_marque_default);
                                $sql_marque_data = $sql_marque_data_default;
                            }
                            $marque_collection_url .= Mage::helper('aitmanufacturers')->toUrlKey($sql_marque_data);
                            //echo "<br /><br />";
                            //$marque_collection_url .= strtolower($sql_marque_data);

                            if($url_count > 1)
                            {
                                $sql_collection = 'SELECT a.value from `eav_attribute_option_value` as a inner join `eav_attribute_option` as s on a.`option_id` = s.`option_id` where a.`option_id` = '.$collection_option_id.' and s.`attribute_id` = '.$collection_id.' and a.`store_id`= "'.$store_alternate['store_id'].'"';
                                $sql_collection_data = $read->fetchOne($sql_collection);

                                if(empty($sql_collection_data))
                                {
                                    $sql_collection_default = 'SELECT a.value from `eav_attribute_option_value` as a inner join `eav_attribute_option` as s on a.`option_id` = s.`option_id` where a.`option_id` = '.$collection_option_id.' and s.`attribute_id` = '.$collection_id.' and a.`store_id`= 0';
                                    $sql_collection_data_default = $read->fetchOne($sql_collection_default);
                                    $sql_collection_data = $sql_collection_data_default;
                                }
                                $marque_collection_url .= '/'.Mage::helper('aitmanufacturers')->toUrlKey($sql_collection_data);
                                //echo "<br /><br />";
                                //$marque_collection_url .= $marque_collection_url.'/'.strtolower($sql_collection_data).'.html';
                            }
                            $final_url = $marque_collection_url.".html";
                            //echo "<br />-----------------------------<br />";

                            $core_url_check = 'SELECT * from `core_url_rewrite` where `request_path` = "'.$final_url.'"';
                            $core_url_check_result = $read->fetchOne($core_url_check);
                            if(empty($core_url_check_result))
                            {
                                $marque_default_url = Mage::helper('aitmanufacturers')->toUrlKey($sql_marque_data_default);
                                $collection_default_url = $marque_default_url.'/'.Mage::helper('aitmanufacturers')->toUrlKey($sql_collection_data_default).".html";
                                $final_url = $collection_default_url;
                            }

                            $url = $store_alternate->getBaseUrl() . $final_url;
                            //echo "<br />-----------------------------<br />";

                            if($store_alternate_language_code)
                            {
    						    $alternate_urls['urls'][$store_alternate_language_code] = $url;
    						}
                            else
                            {
    						    $store_locale = substr(Mage::getStoreConfig('general/locale/code', $store_alternate->getId()),0,2);
    							$alternate_urls['urls'][$store_locale] = $url;
    						}
    				    }
                        //exit;
                    }
				}

                /*echo "<pre>";
                print_r($alternate_urls);
                echo "</pre>";*/
				if(is_array($alternate_urls)) {
					return $alternate_urls;
				}

			}
		}
		return false;
	}

    protected function _getCmsUrlSuffix()
    {
        $suffix = '';

        if (Mage::helper('core')->isModuleEnabled('Bubble_CmsTree')) {
            $suffix = Mage::helper('bubble_cmstree')->getUrlSuffix();
        }

        return $suffix;
    }

    public function getCoreProductUrl($product_id, $store_id)
    {
		if($this->checkProductVisibility($product_id, $store_id)) {
			if($category = Mage::registry('current_category')) {
				$category_id = $category->getId();
			} else {
				$category_id = '';
			}
			$core_url = Mage::getModel('core/url_rewrite');
			$id_path = sprintf('product/%d', $product_id);

			if(($category_id) && (Mage::getStoreConfig('catalog/seo/product_use_categories', $store_id))) {
				if(!Mage::getStoreConfig('alternatelang/config/canonical', $store_id)) {
					$id_path = sprintf('%s/%d', $id_path, $category_id);
				}
			}
			$core_url->setStoreId($store_id);
			$core_url->loadByIdPath($id_path);
			return $core_url->getRequestPath();
		} else {
			return false;
		}
    }

    public function getCoreCategoryUrl($category_id, $store_id)
    {
		if($this->checkCategogyVisibility($category_id, $store_id)) {
			$core_url = Mage::getModel('core/url_rewrite');
			$id_path = sprintf('category/%d', $category_id);
			$core_url->setStoreId($store_id);
			$core_url->loadByIdPath($id_path);
			return $core_url->getRequestPath();
		} else {
			return false;
		}
    }

    public function checkProductVisibility($product_id, $store_id)
    {
    	$_productshop = Mage::getModel('catalog/product')->setStoreId($store_id)->load($product_id);
		if(($_productshop->getStatus() != 1) || ($_productshop->getVisibility() == 1)) {
			return false;
		} else {
			return true;
		}
    }

    public function checkCategogyVisibility($category_id, $store_id)
    {
		$_categoryshop = Mage::getModel('catalog/category')->setStoreId($store_id)->load($category_id);
		if(!$_categoryshop->getIsActive()) {
			return false;
		} else {
			return true;
		}
    }

    public function getConnectedStores($storeId)
    {
    	$shops = @unserialize(Mage::getStoreConfig('alternatelang/targeting/shops'));
		$group = $this->getConnectedGroup($storeId, $shops);
		$stores = array();
		foreach($shops as $shop) {
			if($shop['group'] == $group) {
				$stores[] = array("store_id" => $shop['store_id'], "language_code" => $shop['language_code'], "group" => $shop['group']);
			}
		}
		return $stores;
    }

    public function getConnectedGroup($storeId, $shops)
    {
		foreach($shops as $shop) {
			if($shop['store_id'] == $storeId) {
				$group = $shop['group'];
				break;
			}
		}
		if(isset($group)) {
			return $group;
		}
    }

}