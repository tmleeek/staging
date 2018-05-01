<?php
/**
 * Categoryimport.php
 * CommerceExtensions @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commerceextensions.com/LICENSE-M1.txt
 *
 * @category   Categoryimport
 * @copyright  Copyright (c) 2003-2010 CommerceExtensions @ InterSEC Solutions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */ 
 
class CommerceExtensions_Categoriesimportexport_Model_Convert_Adapter_Categoryimport extends Mage_Eav_Model_Convert_Adapter_Entity
{
    protected $_categoryCache = array();

    protected $_stores;

    /**
     * Category display modes
     */
    protected $_displayModes = array( 'PRODUCTS', 'PAGE', 'PRODUCTS_AND_PAGE');

    public function parse()
    {
        $batchModel = Mage::getSingleton('dataflow/batch');
        /* @var $batchModel Mage_Dataflow_Model_Batch */

        $batchImportModel = $batchModel->getBatchImportModel();
        $importIds = $batchImportModel->getIdCollection();

        foreach ($importIds as $importId) {
            //print '<pre>'.memory_get_usage().'</pre>';
            $batchImportModel->load($importId);
            $importData = $batchImportModel->getBatchData();

            $this->saveRow($importData);
        }
    }

    /**
     * Save category (import)
     *
     * @param array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData)
    {
		  
        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skip import row, required field "%s" not defined', 'store');
                Mage::throwException($message);
            }
        } else {
						#$store = Mage::getModel('core/store')->load($this->getBatchParams('store'));
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage::helper('catalog')->__('Skip import row, store "%s" field not exists', $importData['store']);
            Mage::throwException($message);
        }
				if(isset($importData['rootid']) && $importData['rootid']!="") {
					$rootId = $importData['rootid'];
				} else {
       	 		    $rootId = $store->getRootCategoryId();
				}
        if (!$rootId) {
            return array();
        }
        $rootPath = '1/'.$rootId; 
        if (empty($this->_categoryCache[$store->getId()])) {
            $collection = Mage::getModel('catalog/category')->getCollection()
                ->setStore($store)
                ->addAttributeToSelect('name');
            $collection->getSelect()->where("path like '".$rootPath."/%'");

            foreach ($collection as $cat) {
                $pathArr = explode('/', $cat->getPath());
                $namePath = '';
                for ($i=2, $l=sizeof($pathArr); $i<$l; $i++) {
                    $name = $collection->getItemById($pathArr[$i])->getName();
                    $namePath .= (empty($namePath) ? '' : '/').trim($name);
                }
                $cat->setNamePath($namePath);
            }

            $cache = array();
            foreach ($collection as $cat) {
                $cache[strtolower($cat->getNamePath())] = $cat;
                $cat->unsNamePath();
            }
            $this->_categoryCache[$store->getId()] = $cache;
        }
        $cache =& $this->_categoryCache[$store->getId()];
		//Remove this line if your using ^ vs / as delimiter for categories.. fix for cat names with / in them
        if($this->getBatchParams('categorydelimiter') == "/") {
        	$importData['categories'] = preg_replace('#\s*/\s*#', '/', trim($importData['categories']));
		}
        if (!empty($cache[$importData['categories']])) {
            return true;
        }

        $path = $rootPath;
        $namePath = '';

        $i = 1;
		$general = array();
		#$delimitertouse = $this->getBatchParams('categorydelimiter');
		if($this->getBatchParams('categorydelimiter') !="") {
			$delimitertouse = $this->getBatchParams('categorydelimiter');
		} else {
			$delimitertouse = "/";
		}
        $categories = explode($delimitertouse, $importData['categories']);
        #$categories = explode('/', $importData['categories']);
		$IsActive = $importData['is_active']; 
		$IsAnchor = $importData['is_anchor']; 
		if(isset($importData['url_key'])) {
			$UrlKey = $importData['url_key'];
		}
		if(isset($importData['url_path'])) {
			$UrlPath = $importData['url_path'];
		}
		if(isset($importData['meta_title'])) {
			$MetaTitle = trim($importData['meta_title']); 
		}
		if(isset($importData['meta_keywords'])) {
			$MetaKeywords = trim($importData['meta_keywords']); 
		}
		if(isset($importData['meta_description'])) {
			$MetaDescription = trim($importData['meta_description']);
		}
		if(isset($importData['description'])) {
			$description = trim($importData['description']); 
		}
		if(isset($importData['display_mode'])) {
			$dispMode = $importData['display_mode'];
		}
		if(isset($importData['cms_block'])) {
			$cmsBlock = $importData['cms_block'];
		}
		if(isset($importData['page_layout'])) {
			$pageLayout = $importData['page_layout'];
		}
		if(isset($importData['custom_layout_update'])) {
			$custom_layout_update = $importData['custom_layout_update'];
		}
		if(isset($importData['custom_design'])) {
			$customDesign = $importData['custom_design'];
		}
		if(isset($importData['include_in_menu'])) {
			$includeInMenu = $importData['include_in_menu'];
		}
		$verChecksplit = explode(".",Mage::getVersion());
		// 1.7.x ONLY
		if ($verChecksplit[1] >= 7) {
			
			if(isset($importData['custom_apply_to_products'])) {
				$customapplytoproducts = $importData['custom_apply_to_products'];
			}
			if(isset($importData['custom_use_parent_settings'])) {
				$customuseparentsettings = $importData['custom_use_parent_settings'];
			}
		}
		
		if(isset($importData['position']) && $importData['position'] != "") {
			$position = $importData['position'];
		} else {
			$position = "1";
		}
		
		
			if(isset($importData['category_id']) && $importData['category_id'] !="") {
				$catId = $importData['category_id'];
				#echo "ID: " . $catId;
				/* THIS IS FOR UPDATING CATEGORY DATA */
				$catupdate = Mage::getModel('catalog/category');
				$catupdate->setStoreId($store->getId());
				$catupdate->load($catId);
				
				if($catupdate->getId() > 0) {
						if(isset($importData['name']) && $importData['name'] !="") {
							$generalupdate['name'] = $importData['name']; 
						} else {
							$generalupdate['name'] = $catupdate->getName();
							#$generalupdate['name'] = $categories[0];
						}
						if(isset($importData['meta_title'])) { 
							$generalupdate['meta_title'] = $MetaTitle; 
						}
						if(isset($importData['meta_keywords'])) { 
							$generalupdate['meta_keywords'] = $MetaKeywords; 
						}
						if(isset($importData['meta_description'])) { 
							$generalupdate['meta_description'] = $MetaDescription;
						}
						if(isset($importData['cms_block'])) { 
							$generalupdate['landing_page'] = $cmsBlock; 
						}
						if(isset($importData['display_mode'])) {
							$generalupdate['display_mode'] = $dispMode; 
						}
						if(isset($importData['is_active'])) {
							$generalupdate['is_active'] = $IsActive; 
						}
						if(isset($importData['is_anchor'])) {
							$generalupdate['is_anchor'] = $IsAnchor; 
						}
						if(isset($importData['url_key'])) {
							$generalupdate['url_key'] = $UrlKey; 
						}
						if(isset($importData['url_path'])) {
							$generalupdate['url_path'] = $UrlPath; 
						}
						if(isset($importData['page_layout'])) {
							$generalupdate['page_layout'] = $pageLayout; 
						}
						if(isset($importData['custom_layout_update'])) {		
							$generalupdate['custom_layout_update'] = $custom_layout_update; 
						}
						if(isset($importData['custom_design'])) {		
							$generalupdate['custom_design'] = $customDesign; 
						}
						if(isset($importData['include_in_menu'])) {
							$generalupdate['include_in_menu'] = $includeInMenu;
						}
						$verChecksplit = explode(".",Mage::getVersion());
						// 1.7.x ONLY
						if ($verChecksplit[1] >= 7) {
							if(isset($importData['custom_apply_to_products'])) {
								$generalupdate['custom_apply_to_products'] = $customapplytoproducts;
							}
							if(isset($importData['custom_use_parent_settings'])) {
								$generalupdate['custom_use_parent_settings'] = $customuseparentsettings;
							}
						}
						$generalupdate['position'] = $position; 
						
						$catupdate->addData($generalupdate); 
                    	#$catupdate->setIsAnchor($IsAnchor);
						$catupdate->setDescription($description); 
						$catupdate->save();
						
						//echo 'hello';print_r($catupdate);exit;
						//CUSTOM CODE CATEGORY PRODUCT'S / POSITIONS
						$resource = Mage::getSingleton('core/resource');
						$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
						$write = $resource->getConnection('core_write');
						$read = $resource->getConnection('core_read');
						if(isset($importData['category_products']) && $importData['category_products'] != "") {
							$pipedelimiteddatabycomma = explode(',',$importData['category_products']);
							foreach ($pipedelimiteddatabycomma as $options_data) {
									
								$option_parts = explode(':',$options_data);
								$catalogProductModel = Mage::getModel('catalog/product');
								$productId = $catalogProductModel->getIdBySku($option_parts[0]);
								//$write_qry2 = $write->query("UPDATE `".$prefix."catalog_category_product` SET created_at = '".  ."', updated_at = '". date ("Y-m-d H:i:s", $dateTime) ."' WHERE entity_id = '". $order1->getId() ."'");
								if($productId > 0) {
								$write_qry =$write->query("DELETE FROM `".$prefix."catalog_category_product` WHERE category_id = '$catId' AND product_id = '$productId'");
								if(isset($option_parts[1])) {
									$cat_product_position = $option_parts[1];
								} else {
									$cat_product_position = "0";
								}
								//echo "Insert into `".$prefix."catalog_category_product` (category_id,product_id,position) VALUES ('$catId','$productId','0')";
								$write_qry =$write->query("INSERT INTO `".$prefix."catalog_category_product` (category_id,product_id,position) VALUES ('$catId','$productId','$cat_product_position')");
								} else {
									echo "PRODUCT DOES NOT EXIST".$option_parts[0].'cat_id=='.$importData['category_id'].'<br>';
									#echo "PRODUCT DOES NOT EXIST - " . $options_data;
								}
								
							}
						} 
						//CUSTOM CODE CATEGORY PRODUCT'S / POSITIONS
						#$catupdate->delete();
						#echo "saved Name" . $catupdate->getName();
						#echo "saved Path" . $catupdate->getPath();
						#echo "saved Id" . $catupdate->getId();
				} else {
						
						#echo "saved one2";
						if(isset($importData['category_id']) && $importData['category_id'] !="") {
									
									foreach ($categories as $catName) {
														
									$namePath .= (empty($namePath) ? '' : '/').strtolower($catName);
									if (empty($cache[$namePath])) {
										#$dispMode = $this->_displayModes[2];
														/*
										$cat = Mage::getModel('catalog/category')
											->setStoreId($store->getId())
											->setPath($path)
											->setName($catName)
											->setAttributeSetId($cat->getDefaultAttributeSetId()) 
											->setIsActive($importData['is_active'])
											->setIsAnchor($importData['is_anchor'])
											->setDisplayMode($importData['display_mode'])
											->save();
														*/
														$cat = Mage::getModel('catalog/category'); 
														$cat->setStoreId($store->getId()); 
														$general['name'] = $catName; 
														$general['path'] = $path; 
														$general['meta_title'] = $MetaTitle; 
														$general['meta_keywords'] = $MetaKeywords; 
														$general['meta_description'] = $MetaDescription; 
														$general['description'] = $description; 
														$general['landing_page'] = $cmsBlock; 
														$general['display_mode'] = $dispMode; 
														$general['is_active'] = $IsActive; 
														$general['is_anchor'] = $IsAnchor; 
														$general['url_key'] = $UrlKey; 
														$general['url_path'] = $UrlPath; 
														if(isset($importData['page_layout'])) {
															$general['page_layout'] = $pageLayout; 
														}
														if(isset($importData['custom_layout_update'])) {		
															$general['custom_layout_update'] = $custom_layout_update; 
														}
														if(isset($importData['custom_design'])) {
															$general['custom_design'] = $customDesign; 
														}
														if(isset($importData['include_in_menu'])) {
															$general['include_in_menu'] = $includeInMenu;
														}
														$verChecksplit = explode(".",Mage::getVersion());
														// 1.7.x ONLY
														if ($verChecksplit[1] >= 7) {
															if(isset($importData['custom_apply_to_products'])) {
																$general['custom_apply_to_products'] = $customapplytoproducts;
															}
															if(isset($importData['custom_use_parent_settings'])) {
																$general['custom_use_parent_settings'] = $customuseparentsettings;
															}
														}
														$general['position'] = $position; 
														
														$cat->addData($general); 
														$cat->setDescription($description); 
														$cat->save();
														//CUSTOM CODE CATEGORY PRODUCT'S / POSITIONS
															$resource = Mage::getSingleton('core/resource');
															$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
															$write = $resource->getConnection('core_write');
															$read = $resource->getConnection('core_read');
															if(isset($importData['category_products']) && $importData['category_products'] != "") {
																$pipedelimiteddatabycomma = explode(',',$importData['category_products']);
																foreach ($pipedelimiteddatabycomma as $options_data) {
																		
																	$option_parts = explode(':',$options_data);
																	$catalogProductModel = Mage::getModel('catalog/product');
																	$productId = $catalogProductModel->getIdBySku($option_parts[0]);
																	//$write_qry2 = $write->query("UPDATE `".$prefix."catalog_category_product` SET created_at = '".  ."', updated_at = '". date ("Y-m-d H:i:s", $dateTime) ."' WHERE entity_id = '". $order1->getId() ."'");
																	if($productId > 0) {
																	$write_qry =$write->query("DELETE FROM `".$prefix."catalog_category_product` WHERE category_id = '$catId' AND product_id = '$productId'");
																	if(isset($option_parts[1])) {
																		$cat_product_position = $option_parts[1];
																	} else {
																		$cat_product_position = "0";
																	}
																	//echo "Insert into `".$prefix."catalog_category_product` (category_id,product_id,position) VALUES ('$catId','$productId','0')";
																	$write_qry =$write->query("INSERT INTO `".$prefix."catalog_category_product` (category_id,product_id,position) VALUES ('$catId','$productId','$cat_product_position')");
																	} else {
																		echo "PRODUCT DOES NOT EXIST".$option_parts[0].'cat_id=='.$importData['category_id'].'<br>';
																		#echo "PRODUCT DOES NOT EXIST - " . $options_data;
																	}
																	
																}
															}
														//CUSTOM CODE CATEGORY PRODUCT'S / POSITIONS
														$cache[$namePath] = $cat;
									}
									$catId = $cache[$namePath]->getId();
									$path .= '/'.$catId;
									$i++;
												
								}
		
							$newpath ="";
							$new_id = $importData['category_id'];
							$copy = clone $catupdate;
							$path2 = $cat->getPath();
							$oldid = $cat->getId();
							$oldlevelid = $cat->getLevel();
							if($oldlevelid < 1) {
								$oldlevelid = 1;
							}
							$oldchilderncount = $cat->getChildernCount();
							$oldattributesetid = $cat->getDefaultAttributeSetId();
							#if($oldchilderncount < 1) {
								#$oldchilderncount = 1;
							#}
							$cat->delete();
							#echo "PATH: " . $path . " <br/>";
							#echo "PATH2: " . $path2;
							#echo "NAME: " . $cat->getName();
							$currentcatname = $cat->getName();
							
							$a = explode("/", $path);
							$count = 1;
							foreach ($a as $i => $k) {
								if ($count != count($a)) {
									$newpath .= $k . "/";
									$count++;
								}
							}
							$newpath .= $new_id;
							#$newpath = "1/".$importData['rootid']."/".$new_id;
							#echo "NEW PATH: " . $newpath;
							
							$generalupdateidset['name'] = $currentcatname; 
							$generalupdateidset['meta_title'] = $MetaTitle; 
							$generalupdateidset['meta_keywords'] = $MetaKeywords; 
							$generalupdateidset['meta_description'] = $MetaDescription; 
							$generalupdateidset['landing_page'] = $cmsBlock; 
							$generalupdateidset['display_mode'] = $dispMode; 
							$generalupdateidset['is_active'] = $IsActive; 
							$generalupdateidset['is_anchor'] = $IsAnchor; 
							$generalupdateidset['url_key'] = $UrlKey; 
							$generalupdateidset['url_path'] = $UrlPath; 
							if(isset($importData['page_layout'])) {
								$generalupdateidset['page_layout'] = $pageLayout; 
							}
							if(isset($importData['custom_layout_update'])) {		
								$generalupdateidset['custom_layout_update'] = $custom_layout_update; 
							}
							if(isset($importData['custom_design'])) {		
								$generalupdateidset['custom_design'] = $customDesign; 
							}
							if(isset($importData['include_in_menu'])) {
								$generalupdateidset['include_in_menu'] = $includeInMenu;
							}
							$verChecksplit = explode(".",Mage::getVersion());
							// 1.7.x ONLY
							if ($verChecksplit[1] >= 7) {
								if(isset($importData['custom_apply_to_products'])) {
									$generalupdateidset['custom_apply_to_products'] = $customapplytoproducts;
								}
								if(isset($importData['custom_use_parent_settings'])) {
									$generalupdateidset['custom_use_parent_settings'] = $customuseparentsettings;
								}
							}
							$generalupdateidset['position'] = $position; 
														
							$copy->addData($generalupdateidset); 
							#$copy->setIsAnchor($IsAnchor);
							$copy->setDescription($description); 
						
							#$copy->setId($new_id)->setPath($newpath)->save();
							if($position != "" && $position > 0) {
								$copy->setId($new_id)->setPath($newpath)->setPosition($position)->setLevel($oldlevelid)->setAttributeSetId($oldattributesetid)->setChildernCount($oldchilderncount)->save();
								#$copy->setId($new_id)->setPath($newpath)->setPosition($position)->setLevel(1)->setChildernCount($oldchilderncount)->save();
							} else {
								#$copy->setId($new_id)->setPath($newpath)->setPosition(1)->setLevel(1)->setChildernCount($oldchilderncount)->save();
								$copy->setId($new_id)->setPath($newpath)->setPosition(1)->setLevel($oldlevelid)->setAttributeSetId($oldattributesetid)->setChildernCount($oldchilderncount)->save(); //added postion default of 1
							}
							//might need ->setChilderCount() also on this..
							#echo "Imported the categoryID successfully." . $copy->getName() . " - ". $oldid . " - ". $new_id;
						}
				
				}		
			
			} else {
			
				foreach ($categories as $catName) {
														
									$namePath .= (empty($namePath) ? '' : '/').strtolower($catName);
									if (empty($cache[$namePath])) {
										#$dispMode = $this->_displayModes[2];
														/*
										$cat = Mage::getModel('catalog/category')
											->setStoreId($store->getId())
											->setPath($path)
											->setName($catName)
											->setAttributeSetId($cat->getDefaultAttributeSetId()) 
											->setIsActive($importData['is_active'])
											->setIsAnchor($importData['is_anchor'])
											->setDisplayMode($importData['display_mode'])
											->save();
														*/
														$cat = Mage::getModel('catalog/category'); 
														$cat->setStoreId($store->getId()); 
														$general['name'] = $catName; 
														$general['path'] = $path; 
														if(isset($importData['meta_title'])) {
															$general['meta_title'] = $MetaTitle;
														}
														if(isset($importData['meta_keywords'])) {
															$general['meta_keywords'] = $MetaKeywords;
														}
														if(isset($importData['meta_description'])) {
															$general['meta_description'] = $MetaDescription; 
														}
														if(isset($importData['meta_title'])) {
															$general['meta_title'] = $description; 
														}
														if(isset($importData['landing_page'])) {
															$general['landing_page'] = $cmsBlock;
														}
														if(isset($importData['display_mode'])) {
															$general['display_mode'] = $dispMode;
														}
														if(isset($importData['is_active'])) {
															$general['is_active'] = $IsActive;
														}
														if(isset($importData['is_anchor'])) {
															$general['is_anchor'] = $IsAnchor;
														}
														if(isset($importData['url_key'])) {
															$general['url_key'] = $UrlKey;
														}
														if(isset($importData['url_path'])) {
															$general['url_path'] = $UrlPath;
														}
														if(isset($importData['page_layout'])) {
															$general['page_layout'] = $pageLayout; 
														}
														if(isset($importData['custom_layout_update'])) {		
															$general['custom_layout_update'] = $custom_layout_update; 
														}
														if(isset($importData['custom_design'])) {
															$general['custom_design'] = $customDesign; 
														}
														if(isset($importData['include_in_menu'])) {
															$general['include_in_menu'] = $includeInMenu;
														}
														$verChecksplit = explode(".",Mage::getVersion());
														// 1.7.x ONLY
														if ($verChecksplit[1] >= 7) {
														
															if(isset($importData['custom_apply_to_products'])) {
																$general['custom_apply_to_products'] = $customapplytoproducts;
															}
															if(isset($importData['custom_use_parent_settings'])) {
																$general['custom_use_parent_settings'] = $customuseparentsettings;
															}
														
														}
														$general['position'] = $position; 
														
														$cat->addData($general); 
														$cat->setDescription($description); 
														$cat->save();
														//CUSTOM CODE CATEGORY PRODUCT'S / POSITIONS
														$resource = Mage::getSingleton('core/resource');
														$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
														$write = $resource->getConnection('core_write');
														$read = $resource->getConnection('core_read');
														if(isset($importData['category_products']) && $importData['category_products'] != "") {
															$pipedelimiteddatabycomma = explode(',',$importData['category_products']);
															foreach ($pipedelimiteddatabycomma as $options_data) {
																	
																$option_parts = explode(':',$options_data);
																$catalogProductModel = Mage::getModel('catalog/product');
																$productId = $catalogProductModel->getIdBySku($option_parts[0]);
																//$write_qry2 = $write->query("UPDATE `".$prefix."catalog_category_product` SET created_at = '".  ."', updated_at = '". date ("Y-m-d H:i:s", $dateTime) ."' WHERE entity_id = '". $order1->getId() ."'");
																if($productId > 0) {
																$write_qry =$write->query("DELETE FROM `".$prefix."catalog_category_product` WHERE category_id = '$catId' AND product_id = '$productId'");
																if(isset($option_parts[1])) {
																	$cat_product_position = $option_parts[1];
																} else {
																	$cat_product_position = "0";
																}
																//echo "Insert into `".$prefix."catalog_category_product` (category_id,product_id,position) VALUES ('$catId','$productId','0')";
																$write_qry =$write->query("INSERT INTO `".$prefix."catalog_category_product` (category_id,product_id,position) VALUES ('$catId','$productId','$cat_product_position')");
																} else {
																	echo "PRODUCT DOES NOT EXIST".$option_parts[0].'cat_id=='.$importData['category_id'].'<br>';
																	#echo "PRODUCT DOES NOT EXIST - " . $options_data;
																}
																
															}
														}
														//CUSTOM CODE CATEGORY PRODUCT'S / POSITIONS
														$cache[$namePath] = $cat;
									}
									$catId = $cache[$namePath]->getId();
									$path .= '/'.$catId;
									$i++;
												
								}
				}  
				/* END UPDATE CATEGORY DATA */
		
		/* THIS IS FOR UPDATING CATEGORY DATA */ 
		$catupdate = Mage::getModel('catalog/category')->load($catId); 
		$catupdate->setStoreId($store->getId()); 
		
		if($catupdate->getId() > 0) {
				
				if(isset($importData['meta_title'])) {
					$generalupdate['meta_title'] = $MetaTitle;
				}
				if(isset($importData['meta_keywords'])) {
					$generalupdate['meta_keywords'] = $MetaKeywords; 
				} 
				if(isset($importData['meta_description'])) {
					$generalupdate['meta_description'] = $MetaDescription;
				} 
				if(isset($importData['cms_block'])) {
					$generalupdate['landing_page'] = $cmsBlock; 
				}
				if(isset($importData['display_mode'])) {
					$generalupdate['display_mode'] = $dispMode; 
				}
				if(isset($importData['is_active'])) {
					$generalupdate['is_active'] = $IsActive; 
				}
				if(isset($importData['is_anchor'])) {
					$generalupdate['is_anchor'] = $IsAnchor; 
				}
				if(isset($importData['url_key'])) {
					$generalupdate['url_key'] = $UrlKey; 
				}
				if(isset($importData['url_path'])) {
					$generalupdate['url_path'] = $UrlPath;
				} 
				if(isset($importData['page_layout'])) {
					$generalupdate['page_layout'] = $pageLayout; 
				}
				if(isset($importData['custom_layout_update'])) {		
					$generalupdate['custom_layout_update'] = $custom_layout_update; 
				}
				if(isset($importData['custom_design'])) {		
					$generalupdate['custom_design'] = $customDesign; 
				}
				if(isset($importData['include_in_menu'])) {
					$generalupdate['include_in_menu'] = $includeInMenu;
				}
				$verChecksplit = explode(".",Mage::getVersion());
				// 1.7.x ONLY
				if ($verChecksplit[1] >= 7) {
				
					if(isset($importData['custom_apply_to_products'])) {
						$generalupdate['custom_apply_to_products'] = $customapplytoproducts;
					}
					if(isset($importData['custom_use_parent_settings'])) {
						$generalupdate['custom_use_parent_settings'] = $customuseparentsettings;
					}
				
				}
				$generalupdate['position'] = $position; 
					 					
				$catupdate->addData($generalupdate); 
				#$catupdate->setIsAnchor($IsAnchor);
				if(isset($importData['description'])) {
					$catupdate->setDescription($description);
				}
				/* ABILITY TO IMPORT IMAGE FOR CATEGORIES START */
				if(isset($importData['category_image'])) {
					if($importData['category_image'] != "") {
						$file = preg_replace('#\s*/\s*#', '/', trim($importData['category_image']));
						#echo "FILE: " . $file;
						$sourceFilePath = Mage::getBaseDir('media') . DS . 'import' . DS . $file;
						#$targetFileName = $cache[$namePath]->getId().'-'.$file;
						$targetFileName = $file;
						if(file_exists($sourceFilePath) && !is_dir($sourceFilePath))
						{
								copy($sourceFilePath,Mage::getBaseDir('media') . DS . 'catalog'.DS.'category' . DS . $targetFileName);
								$catupdate->setImage($targetFileName); 
						}
					} else {
						$catupdate->setImage(''); 
					}
				}
				/* ABILITY TO IMPORT IMAGE FOR CATEGORIES END */
				/* ABILITY TO IMPORT THUMB IMAGE FOR CATEGORIES START */
					if(isset($importData['category_thumb_image']) && $importData['category_thumb_image'] != "") {
						$file = preg_replace('#\s*/\s*#', '/', trim($importData['category_thumb_image']));
						$sourceFilePath = Mage::getBaseDir('media') . DS . 'import' . DS . $file;
						#$targetFileName = $cache[$namePath]->getId().'-'.$file;
						$targetFileName = $file;
						if(file_exists($sourceFilePath) && !is_dir($sourceFilePath))
						{
							copy($sourceFilePath,Mage::getBaseDir('media') . DS . 'catalog'.DS.'category' . DS . $targetFileName);
							$catupdate->setThumbnail($targetFileName); 
						}
					}
				/* ABILITY TO IMPORT IMAGE FOR CATEGORIES END */
				
				$catupdate->save();
				//CUSTOM CODE CATEGORY PRODUCT'S / POSITIONS
				$resource = Mage::getSingleton('core/resource');
				$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
				$write = $resource->getConnection('core_write');
				$read = $resource->getConnection('core_read');
				if(isset($importData['category_products']) && $importData['category_products'] != "") {
					$pipedelimiteddatabycomma = explode(',',$importData['category_products']);
					foreach ($pipedelimiteddatabycomma as $options_data) {
							
						$option_parts = explode(':',$options_data);
						$catalogProductModel = Mage::getModel('catalog/product');
						$productId = $catalogProductModel->getIdBySku($option_parts[0]);
						//$write_qry2 = $write->query("UPDATE `".$prefix."catalog_category_product` SET created_at = '".  ."', updated_at = '". date ("Y-m-d H:i:s", $dateTime) ."' WHERE entity_id = '". $order1->getId() ."'");
						if($productId > 0) {
						$write_qry =$write->query("DELETE FROM `".$prefix."catalog_category_product` WHERE category_id = '$catId' AND product_id = '$productId'");
						if(isset($option_parts[1])) {
							$cat_product_position = $option_parts[1];
						} else {
							$cat_product_position = "0";
						}
						//echo "Insert into `".$prefix."catalog_category_product` (category_id,product_id,position) VALUES ('$catId','$productId','0')";
						$write_qry =$write->query("INSERT INTO `".$prefix."catalog_category_product` (category_id,product_id,position) VALUES ('$catId','$productId','$cat_product_position')");
						} else {
						   echo "PRODUCT DOES NOT EXIST".$option_parts[0].'cat_id=='.$importData['category_id'].'<br>';
							#echo "PRODUCT DOES NOT EXIST - " . $options_data;
						}
						
					}
				}
				//CUSTOM CODE CATEGORY PRODUCT'S / POSITIONS
				#echo "saved one";
		} 
        return true;
    }

    /**
     * Retrieve store object by code
     *
     * @param string $store
     * @return Mage_Core_Model_Store
     */
    public function getStoreByCode($store)
    {
        $this->_initStores();
        if (isset($this->_stores[$store])) {
            return $this->_stores[$store];
        }
        return false;
    }

    /**
     *  Init stores
     *
     *  @param    none
     *  @return      void
     */
    protected function _initStores ()
    {
        if (is_null($this->_stores)) {
            $this->_stores = Mage::app()->getStores(true, true);
            foreach ($this->_stores as $code => $store) {
                $this->_storesIdCode[$store->getId()] = $code;
            }
        }
    }
	protected function getStoreById($id)
   {
       $this->_initStores();
       /**
        * In single store mode all data should be saved as default
        */
       if (Mage::app()->isSingleStoreMode()) {
           return Mage::app()->getStore(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);
       }

       if (isset($this->_storesIdCode[$id])) {
           return $this->getStoreByCode($this->_storesIdCode[$id]);
       }
       return false;
   }
   
   protected $_pathids = array(1);
   protected function getPathId($level, $catname) {
		$pathid = false;
		$collection = Mage::getModel('catalog/category')->getCollection()
				->addAttributeToSelect('name')
				->addFieldToFilter('level', $level);
		$collection->getSelect()->where("path like '".implode('/', $this->_pathids)."/%'");
     
     		foreach($collection as $category) {
			if ($category->getName() == $catname) {
				$this->_pathids[] = $category->getId();
				$pathid = true;
				break;
			}
		}
		return $pathid;
	}
   protected function getCategoryIdFromPath($path, $catname) 
   {
		$paths = explode('/', $path);
		for ($i=0; $i<count($paths); $i++) {
			$pathid = $this->getPathId($i+1, $paths[$i]);
			if (!$pathid) break;
		}
		if (count($this->_pathids) == count($paths)+1) {
			$pathid = $this->getPathId($i+1, $catname);
			if (!$pathid) return 0;
			else return end($this->_pathids);
		} else return array();
	}
}

?>