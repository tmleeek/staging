<?php
/**
 * Categoryexport.php
 * CommerceExtensions @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commerceextensions.com/LICENSE-M1.txt
 *
 * @category   Categoryexport
 * @copyright  Copyright (c) 2003-2010 CommerceExtensions @ InterSEC Solutions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */ 
 
class CommerceExtensions_Categoriesimportexport_Model_Convert_Parser_Categoryexport extends Mage_Eav_Model_Convert_Parser_Abstract
{
/**
     * @deprecated not used anymore
     */
    public function parse()
    {
			return $this;
		}
 /**
     * Unparse (prepare data) loaded categories
     *
     * @return Mage_Catalog_Model_Convert_Adapter_Categoryexport
     */
    public function unparse()
    {
					if($this->getVar('categorydelimiter') !="") {
						$category_delimiter = $this->getVar('categorydelimiter');
					} else {
						$category_delimiter = "/";
					}
					#$id = 3;
					#$ids = array();
					
					if($this->getVar('rootids')!="") {echo 'no';exit;
						$allrootids = explode(",", $this->getVar('rootids'));
						foreach ($allrootids as $rootId) {
						#echo "ID: " . $rootId;					
						/* Load category by id*/
						$cat = Mage::getModel('catalog/category')->load($rootId);
						$categories = Mage::getModel('catalog/category')->getCollection()
    										->addAttributeToFilter(array(array('attribute'=>'parent_id', 'eq'=>$rootId)))//first level from the tree
    										->addAttributeToSelect('*')//or any other attributes you need
    										->setOrder('position'); 
						#print_r($categories);						
						if(count($categories)) {
				    	foreach ($categories as $_categorytop) {
							
							  #echo "CATID: " . $cat->getName();
							  #echo "CATID: " . $_categorytop->getId();
								#echo "Name: " . $_categorytop->getName();
								#echo "Image: " . $_categorytop->getImage();
								#echo "CATNAME: " . $cat->getName() . $category_delimiter . $_categorytop->getName() . "<br/>";
								$row['rootid'] = $rootId;
								#$row['store'] = strtolower($_categorytop->getStore()->getName());
								$row['store'] = strtolower($_categorytop->getStore()->getCode());
								if($this->getVar('export_categories_for_transfer') == "true") {
									$row['category_id'] = $_categorytop->getId();
									$row['name'] = $_categorytop->getName();
								}
								$row['categories'] = $cat->getName() . $category_delimiter . $_categorytop->getName();
								$row['description'] = $_categorytop->getDescription();
								$row['url_key'] = $_categorytop->getUrlKey();
								$row['is_active'] = $_categorytop->getIsActive();
								$row['meta_title'] = $_categorytop->getMetaTitle();
								$row['url_path'] = $_categorytop->getUrlPath();
								$row['is_anchor'] = $_categorytop->getIsAnchor();
								$row['meta_keywords'] = $_categorytop->getMetaKeywords();
								$row['meta_description'] = $_categorytop->getMetaDescription();
								$row['display_mode'] = $_categorytop->getDisplayMode();
								$row['page_layout'] = $_categorytop->getPageLayout();
								$row['cms_block'] = $_categorytop->getLandingPage();
								$row['custom_layout_update'] = $_categorytop->getCustomLayoutUpdate();
								$row['custom_design'] = $_categorytop->getCustomDesign();
								$row['category_image'] = $_categorytop->getImage();
								$row['category_thumb_image'] = $_categorytop->getThumbnail();
								$row['include_in_menu'] = $_categorytop->getIncludeInMenu();
								$verChecksplit = explode(".",Mage::getVersion());
								// 1.7.x ONLY
								if ($verChecksplit[1] >= 7) {
									$row['custom_apply_to_products'] = $_categorytop->getCustomApplyToProducts();
									$row['custom_use_parent_settings'] = $_categorytop->getCustomUseParentSettings();
								}
								$row['position'] = $_categorytop->getPosition();
								
								//START CUSTOM CODE CATEGORY PRODUCT EXPORT
								if($this->getVar('export_products_for_categories') == "true") {
									$category_products_export = "";
									$resource = Mage::getSingleton('core/resource');
									$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
									$read = $resource->getConnection('core_read');
									
									$select_qry = "SELECT category_id,product_id,position FROM `".$prefix."catalog_category_product` WHERE category_id ='".$_categorytop->getId()."'";
									$catrows = $read->fetchAll($select_qry);
									foreach($catrows as $catproductdata)
									{ 
										$product = Mage::getModel('catalog/product')->load($catproductdata['product_id']);
										if($this->getVar('export_product_position') == "true") {
											$category_products_export .= $product->getSku() . ":" . $catproductdata['position'] . ",";
										} else {
											$category_products_export .= $product->getSku() . ",";
										}
									}
									$row['category_products'] = substr_replace($category_products_export,"",-1);
								}
								//END CUSTOM CODE CATEGORY PRODUCT EXPORT
								
								$batchExport = $this->getBatchExportModel()
										->setId(null)
										->setBatchId($this->getBatchModel()->getId())
										->setBatchData($row)
										->setStatus(1)
										->save();
							
												/*Returns comma separated ids*/
												$subcategoriesmodel = Mage::getModel('catalog/category')->getCollection()
																->addAttributeToFilter(array(array('attribute'=>'parent_id', 'eq'=>$_categorytop->getId())))//first level from the tree
																->addAttributeToSelect('*')//or any other attributes you need
																->setOrder('position', 'asc'); 
										#$subcategories = $subcategoriesmodel->getChildren();
										#print_r($subcategoriesmodel);
										if(count($subcategoriesmodel)) {
											foreach ($subcategoriesmodel as $subcategories) {
										#print_r($subcategories);
										#echo "SUB CAT ID: " . $subcategories->getId();
										#foreach(explode(',',$subcategories) as $subcategoriesid)
										#{
											if($subcategories->getId() > 0) {
												$_sub_category = Mage::getModel('catalog/category')->load($subcategories->getId());
												$row3['rootid'] = $rootId;
												#$row3['store'] = strtolower($_sub_category->getStore()->getName());
												$row3['store'] = strtolower($_categorytop->getStore()->getCode());
												if($this->getVar('export_categories_for_transfer') == "true") {
													$row3['category_id'] = $_sub_category->getId();
													$row3['name'] = $_sub_category->getName();
												}
												#echo "CATNAME: " . $cat->getName() . $category_delimiter . $_categorytop->getName() . $category_delimiter . $_sub_category->getName() . "<br/>";
												$row3['categories'] = $cat->getName() . $category_delimiter . $_categorytop->getName() . $category_delimiter . $_sub_category->getName();
												$row3['description'] = $_sub_category->getDescription();
												$row3['url_key'] = $_sub_category->getUrlKey();
												$row3['is_active'] = $_sub_category->getIsActive();
												$row3['meta_title'] = $_sub_category->getMetaTitle();
												$row3['url_path'] = $_sub_category->getUrlPath();
												$row3['is_anchor'] = $_sub_category->getIsAnchor();
												$row3['meta_keywords'] = $_sub_category->getMetaKeywords();
												$row3['meta_description'] = $_sub_category->getMetaDescription();
												$row3['display_mode'] = $_sub_category->getDisplayMode();
												$row3['page_layout'] = $_sub_category->getPageLayout();
												$row3['cms_block'] = $_sub_category->getLandingPage();
												$row3['custom_layout_update'] = $_sub_category->getCustomLayoutUpdate();
												$row3['custom_design'] = $_sub_category->getCustomDesign();
								       			$row3['category_image'] = $_sub_category->getImage();
												$row3['category_thumb_image'] = $_sub_category->getThumbnail();
												$row3['include_in_menu'] = $_sub_category->getIncludeInMenu();
												$verChecksplit = explode(".",Mage::getVersion());
												// 1.7.x ONLY
												if ($verChecksplit[1] >= 7) {
													$row3['custom_apply_to_products'] = $_sub_category->getCustomApplyToProducts();
													$row3['custom_use_parent_settings'] = $_sub_category->getCustomUseParentSettings();
												}
												$row3['position'] = $_sub_category->getPosition();
												//START CUSTOM CODE CATEGORY PRODUCT EXPORT
												if($this->getVar('export_products_for_categories') == "true") {
													$category_products_export = "";
													$resource = Mage::getSingleton('core/resource');
													$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
													$read = $resource->getConnection('core_read');
													
													$select_qry = "SELECT category_id,product_id,position FROM `".$prefix."catalog_category_product` WHERE category_id ='".$_sub_category->getId()."'";
													$catrows = $read->fetchAll($select_qry);
													foreach($catrows as $catproductdata)
													{ 
														$product = Mage::getModel('catalog/product')->load($catproductdata['product_id']);
														if($this->getVar('export_product_position') == "true") {
															$category_products_export .= $product->getSku() . ":" . $catproductdata['position'] . ",";
														} else {
															$category_products_export .= $product->getSku() . ",";
														}
													}
													$row3['category_products'] = substr_replace($category_products_export,"",-1);
												}
												//END CUSTOM CODE CATEGORY PRODUCT EXPORT
												
												$batchExport = $this->getBatchExportModel()
														->setId(null)
														->setBatchId($this->getBatchModel()->getId())
														->setBatchData($row3)
														->setStatus(1)
														->save();
														
														/* START OF 3rd LEVEL CATEGORY EXPORT */
													  #echo "CAT ID: " . $_category->getId();
														$subsubcategoriesmodel = Mage::getModel('catalog/category')->load($_sub_category->getId());
														$subsubcategories = $subsubcategoriesmodel->getChildren();
														#echo "SUB CAT ID: " . $subcategories;
														foreach(explode(',',$subsubcategories) as $subsubcategoriesid)
														{
															if($subsubcategoriesid > 0) {
																$_sub_sub_category = Mage::getModel('catalog/category')->load($subsubcategoriesid);
																$row4['rootid'] = $rootId;
																#$row4['store'] = strtolower($_sub_sub_category->getStore()->getName());
																#$row4['store'] = strtolower($_categorytop->getStore()->getName());
																$row4['store'] = strtolower($_categorytop->getStore()->getCode());
																if($this->getVar('export_categories_for_transfer') == "true") {
																$row4['category_id'] = $_sub_sub_category->getId();
																$row4['name'] = $_sub_sub_category->getName();
																}
																$row4['categories'] = $cat->getName() . $category_delimiter . $_categorytop->getName() . $category_delimiter . $_sub_category->getName() . $category_delimiter . $_sub_sub_category->getName();
																$row4['description'] = $_sub_sub_category->getDescription();
																$row4['url_key'] = $_sub_sub_category->getUrlKey();
																$row4['is_active'] = $_sub_sub_category->getIsActive();
																$row4['meta_title'] = $_sub_sub_category->getMetaTitle();
																$row4['url_path'] = $_sub_sub_category->getUrlPath();
																$row4['is_anchor'] = $_sub_sub_category->getIsAnchor();
																$row4['meta_keywords'] = $_sub_sub_category->getMetaKeywords();
																$row4['meta_description'] = $_sub_sub_category->getMetaDescription();
																$row4['display_mode'] = $_sub_sub_category->getDisplayMode();
																$row4['page_layout'] = $_sub_sub_category->getPageLayout();
																$row4['cms_block'] = $_sub_sub_category->getLandingPage();
																$row4['custom_layout_update'] = $_sub_sub_category->getCustomLayoutUpdate();
																$row4['custom_design'] = $_sub_sub_category->getCustomDesign();
														 		$row4['category_image'] = $_sub_sub_category->getImage();
																$row4['category_thumb_image'] = $_sub_sub_category->getThumbnail();
																$row4['include_in_menu'] = $_sub_sub_category->getIncludeInMenu();
																$verChecksplit = explode(".",Mage::getVersion());
																// 1.7.x ONLY
																if ($verChecksplit[1] >= 7) {
																$row4['custom_apply_to_products'] = $_sub_sub_category->getCustomApplyToProducts();
																$row4['custom_use_parent_settings'] = $_sub_sub_category->getCustomUseParentSettings();
																}
																$row4['position'] = $_sub_sub_category->getPosition();
																//START CUSTOM CODE CATEGORY PRODUCT EXPORT
																if($this->getVar('export_products_for_categories') == "true") {
																	$category_products_export = "";
																	$resource = Mage::getSingleton('core/resource');
																	$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
																	$read = $resource->getConnection('core_read');
																	
																	$select_qry = "SELECT category_id,product_id,position FROM `".$prefix."catalog_category_product` WHERE category_id ='".$_sub_sub_category->getId()."'";
																	$catrows = $read->fetchAll($select_qry);
																	foreach($catrows as $catproductdata)
																	{ 
																		$product = Mage::getModel('catalog/product')->load($catproductdata['product_id']);
																		if($this->getVar('export_product_position') == "true") {
																			$category_products_export .= $product->getSku() . ":" . $catproductdata['position'] . ",";
																		} else {
																			$category_products_export .= $product->getSku() . ",";
																		}
																	}
																	$row4['category_products'] = substr_replace($category_products_export,"",-1);
																}
																//END CUSTOM CODE CATEGORY PRODUCT EXPORT
																
																$batchExport = $this->getBatchExportModel()
																		->setId(null)
																		->setBatchId($this->getBatchModel()->getId())
																		->setBatchData($row4)
																		->setStatus(1)
																		->save();
																		/* START OF 4th LEVEL CATEGORY EXPORT */
																		#echo "CAT ID: " . $_category->getId();
																		$subsubsubcategoriesmodel = Mage::getModel('catalog/category')->load($_sub_sub_category->getId());
																		$subsubsubcategories = $subsubsubcategoriesmodel->getChildren();
																		#echo "SUB CAT ID: " . $subcategories;
																		#print_r($subsubsubcategories);
																		foreach(explode(',',$subsubsubcategories) as $subsubsubcategoriesid)
																		{
																			if($subsubsubcategoriesid > 0) {
																				$_sub_sub_sub_category = Mage::getModel('catalog/category')->load($subsubsubcategoriesid);
																				$row5['rootid'] = $rootId;
																				#echo "CAT ID: " . $subsubsubcategoriesid . "<br/>";
																				#echo "CAT NAME: " . $_sub_sub_sub_category->getName(). "<br/>";
																				#$row5['store'] = strtolower($_sub_sub_sub_category->getStore()->getName());
																				#$row5['store'] = strtolower($_categorytop->getStore()->getName());
																				$row5['store'] = strtolower($_categorytop->getStore()->getCode());
																				if($this->getVar('export_categories_for_transfer') == "true") {
																				$row5['category_id'] = $_sub_sub_sub_category->getId();
																				$row5['name'] = $_sub_sub_sub_category->getName();
																				}
																				$row5['categories'] = $cat->getName() . $category_delimiter . $_categorytop->getName() . $category_delimiter . $_sub_category->getName() . $category_delimiter . $_sub_sub_category->getName(). $category_delimiter . $_sub_sub_sub_category->getName();
																				$row5['description'] = $_sub_sub_sub_category->getDescription();
																				$row5['url_key'] = $_sub_sub_sub_category->getUrlKey();
																				$row5['is_active'] = $_sub_sub_sub_category->getIsActive();
																				$row5['meta_title'] = $_sub_sub_sub_category->getMetaTitle();
																				$row5['url_path'] = $_sub_sub_sub_category->getUrlPath();
																				$row5['is_anchor'] = $_sub_sub_sub_category->getIsAnchor();
																				$row5['meta_keywords'] = $_sub_sub_sub_category->getMetaKeywords();
																				$row5['meta_description'] = $_sub_sub_sub_category->getMetaDescription();
																				$row5['display_mode'] = $_sub_sub_sub_category->getDisplayMode();
																				$row5['page_layout'] = $_sub_sub_sub_category->getPageLayout();
																				$row5['cms_block'] = $_sub_sub_sub_category->getLandingPage();
																				$row5['custom_layout_update'] = $_sub_sub_sub_category->getCustomLayoutUpdate();
																				$row5['custom_design'] = $_sub_sub_sub_category->getCustomDesign();
																				$row5['category_image'] = $_sub_sub_sub_category->getImage();
																				$row5['category_thumb_image'] = $_sub_sub_sub_category->getThumbnail();
																				$row5['include_in_menu'] = $_sub_sub_sub_category->getIncludeInMenu();
																				$verChecksplit = explode(".",Mage::getVersion());
																				// 1.7.x ONLY
																				if ($verChecksplit[1] >= 7) {
																				$row5['custom_apply_to_products'] = $_sub_sub_sub_category->getCustomApplyToProducts();
																				$row5['custom_use_parent_settings'] = $_sub_sub_sub_category->getCustomUseParentSettings();																			
																				}
																				$row5['position'] = $_sub_sub_sub_category->getPosition();
																				//START CUSTOM CODE CATEGORY PRODUCT EXPORT
																				if($this->getVar('export_products_for_categories') == "true") {
																					$category_products_export = "";
																					$resource = Mage::getSingleton('core/resource');
																					$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
																					$read = $resource->getConnection('core_read');
																					
																					$select_qry = "SELECT category_id,product_id,position FROM `".$prefix."catalog_category_product` WHERE category_id ='".$_sub_sub_sub_category->getId()."'";
																					$catrows = $read->fetchAll($select_qry);
																					foreach($catrows as $catproductdata)
																					{ 
																						$product = Mage::getModel('catalog/product')->load($catproductdata['product_id']);
														if($this->getVar('export_product_position') == "true") {
															$category_products_export .= $product->getSku() . ":" . $catproductdata['position'] . ",";
														} else {
															$category_products_export .= $product->getSku() . ",";
														}
																					}
																					$row5['category_products'] = substr_replace($category_products_export,"",-1);
																				}
																				//END CUSTOM CODE CATEGORY PRODUCT EXPORT
																				
																				$batchExport = $this->getBatchExportModel()
																						->setId(null)
																						->setBatchId($this->getBatchModel()->getId())
																						->setBatchData($row5)
																						->setStatus(1)
																						->save();
																						
																						/* START OF 5th LEVEL CATEGORY EXPORT */
																						#echo "CAT ID: " . $_category->getId();
																						$subsubsubsubcategoriesmodel = Mage::getModel('catalog/category')->load($_sub_sub_sub_category->getId());
																						$subsubsubsubcategories = $subsubsubsubcategoriesmodel->getChildren();
																						#echo "SUB CAT ID: " . $subcategories;
																						#print_r($subsubsubcategories);
																						foreach(explode(',',$subsubsubsubcategories) as $subsubsubsubcategoriesid)
																						{
																							if($subsubsubcategoriesid > 0) {
																								$_sub_sub_sub_sub_category = Mage::getModel('catalog/category')->load($subsubsubsubcategoriesid);
																								$row6['rootid'] = $rootId;
																								#echo "CAT ID: " . $subsubsubcategoriesid . "<br/>";
																								#echo "CAT NAME: " . $_sub_sub_sub_category->getName(). "<br/>";
																								#$row5['store'] = strtolower($_sub_sub_sub_category->getStore()->getName());
																								#$row6['store'] = strtolower($_categorytop->getStore()->getName());
																								$row6['store'] = strtolower($_categorytop->getStore()->getCode());
																								if($this->getVar('export_categories_for_transfer') == "true") {
																								$row6['category_id'] = $_sub_sub_sub_sub_category->getId();
																								$row6['name'] = $_sub_sub_sub_sub_category->getName();
																								}
																								$row6['categories'] = $cat->getName() . $category_delimiter . $_categorytop->getName() . $category_delimiter . $_sub_category->getName() . $category_delimiter . $_sub_sub_category->getName(). $category_delimiter . $_sub_sub_sub_category->getName(). $category_delimiter . $_sub_sub_sub_sub_category->getName();
																								$row6['description'] = $_sub_sub_sub_sub_category->getDescription();
																								$row6['url_key'] = $_sub_sub_sub_sub_category->getUrlKey();
																								$row6['is_active'] = $_sub_sub_sub_sub_category->getIsActive();
																								$row6['meta_title'] = $_sub_sub_sub_sub_category->getMetaTitle();
																								$row6['url_path'] = $_sub_sub_sub_sub_category->getUrlPath();
																								$row6['is_anchor'] = $_sub_sub_sub_sub_category->getIsAnchor();
																								$row6['meta_keywords'] = $_sub_sub_sub_sub_category->getMetaKeywords();
																								$row6['meta_description'] = $_sub_sub_sub_sub_category->getMetaDescription();
																								$row6['display_mode'] = $_sub_sub_sub_sub_category->getDisplayMode();
																								$row6['page_layout'] = $_sub_sub_sub_sub_category->getPageLayout();
																								$row6['cms_block'] = $_sub_sub_sub_sub_category->getLandingPage();
																								$row6['custom_layout_update'] = $_sub_sub_sub_sub_category->getCustomLayoutUpdate();
																								$row6['custom_design'] = $_sub_sub_sub_sub_category->getCustomDesign();
																								$row6['category_image'] = $_sub_sub_sub_sub_category->getImage();
																								$row6['category_thumb_image'] = $_sub_sub_sub_sub_category->getThumbnail();
																								$row6['include_in_menu'] = $_sub_sub_sub_sub_category->getIncludeInMenu();
																								$verChecksplit = explode(".",Mage::getVersion());
																								// 1.7.x ONLY
																								if ($verChecksplit[1] >= 7) {
																								$row6['custom_apply_to_products'] = $_sub_sub_sub_sub_category->getCustomApplyToProducts();
																								$row6['custom_use_parent_settings'] = $_sub_sub_sub_sub_category->getCustomUseParentSettings();
																								
																								}
																								$row6['position'] = $_sub_sub_sub_sub_category->getPosition();
																				//START CUSTOM CODE CATEGORY PRODUCT EXPORT
																				if($this->getVar('export_products_for_categories') == "true") {
																					$category_products_export = "";
																					$resource = Mage::getSingleton('core/resource');
																					$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
																					$read = $resource->getConnection('core_read');
																					
																					$select_qry = "SELECT category_id,product_id,position FROM `".$prefix."catalog_category_product` WHERE category_id ='".$_sub_sub_sub_sub_category->getId()."'";
																					$catrows = $read->fetchAll($select_qry);
																					foreach($catrows as $catproductdata)
																					{ 
																						$product = Mage::getModel('catalog/product')->load($catproductdata['product_id']);
																						
																						if($this->getVar('export_product_position') == "true") {
																							$category_products_export .= $product->getSku() . ":" . $catproductdata['position'] . ",";
																						} else {
																							$category_products_export .= $product->getSku() . ",";
																						}
																					}
																					$row6['category_products'] = substr_replace($category_products_export,"",-1);
																				}
																				//END CUSTOM CODE CATEGORY PRODUCT EXPORT
																								$batchExport = $this->getBatchExportModel()
																										->setId(null)
																										->setBatchId($this->getBatchModel()->getId())
																										->setBatchData($row6)
																										->setStatus(1)
																										->save();
																							}
																						}
																			}
																		}
														}
													}
													
													
												
											}
										}
									}
						} // end for each
						} else {
						
								#$row['store'] = strtolower($cat->getStore()->getName());
								$row['store'] = strtolower($cat->getStore()->getCode());
								if($this->getVar('export_categories_for_transfer') == "true") {
								$row['category_id'] = $cat->getId();
								$row['name'] = $cat->getName();
								}
								$row['categories'] = $cat->getName();
								$row['description'] = $cat->getDescription();
								$row['url_key'] = $cat->getUrlKey();
								$row['is_active'] = $cat->getIsActive();
								$row['meta_title'] = $cat->getMetaTitle();
								$row['url_path'] = $cat->getUrlPath();
								$row['is_anchor'] = $cat->getIsAnchor();
								$row['meta_keywords'] = $cat->getMetaKeywords();
								$row['meta_description'] = $cat->getMetaDescription();
								$row['display_mode'] = $cat->getDisplayMode();
								$row['page_layout'] = $cat->getPageLayout();
								$row['cms_block'] = $cat->getLandingPage();
								$row['custom_layout_update'] = $cat->getCustomLayoutUpdate();
								$row['custom_design'] = $cat->getCustomDesign();
								$row['category_image'] = $cat->getImage();
								$row['category_thumb_image'] = $cat->getThumbnail();
								$row['include_in_menu'] = $cat->getIncludeInMenu();
								$verChecksplit = explode(".",Mage::getVersion());
								// 1.7.x ONLY
								if ($verChecksplit[1] >= 7) {
								$row['custom_apply_to_products'] = $cat->getCustomApplyToProducts();
								$row['custom_use_parent_settings'] = $cat->getCustomUseParentSettings();
								}
								$row['position'] = $cat->getPosition();
								
								//START CUSTOM CODE CATEGORY PRODUCT EXPORT
								if($this->getVar('export_products_for_categories') == "true") {
									$category_products_export = "";
									$resource = Mage::getSingleton('core/resource');
									$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
									$read = $resource->getConnection('core_read');
									
									$select_qry = "SELECT category_id,product_id,position FROM `".$prefix."catalog_category_product` WHERE category_id ='".$cat->getId()."'";
									$catrows = $read->fetchAll($select_qry);
									foreach($catrows as $catproductdata)
									{ 
										$product = Mage::getModel('catalog/product')->load($catproductdata['product_id']);
										if($this->getVar('export_product_position') == "true") {
											$category_products_export .= $product->getSku() . ":" . $catproductdata['position'] . ",";
										} else {
											$category_products_export .= $product->getSku() . ",";
										}
									}
									$row['category_products'] = substr_replace($category_products_export,"",-1);
								}
								//END CUSTOM CODE CATEGORY PRODUCT EXPORT
								
								$batchExport = $this->getBatchExportModel()
										->setId(null)
										->setBatchId($this->getBatchModel()->getId())
										->setBatchData($row)
										->setStatus(1)
										->save();
						}
						}
					
					} else {
					
					//comment by nisha foreach (Mage::app()->getStores() as $store) {echo '<pre>';print_r($store->getData());exit;
						$store = Mage::getModel('core/store')->load(4);
						$rootId = $store->getRootCategoryId();
						$row['rootid'] = $rootId;
					#echo "ID: " . $rootId;
					/* Load category by id*/
					$categories = Mage::getModel('catalog/category')->getCollection()
            						->setStore($store)//sets store ID
									->addAttributeToFilter(array(array('attribute'=>'parent_id', 'eq'=>$rootId)))//first level from the tree
									->addAttributeToSelect('*')//or any other attributes you need
									->setOrder('position'); 
												
						if(count($categories)) {
				    	foreach ($categories as $_categorytop) {
							
							  #echo "CAT ID: " . $_category->getId();
								#echo "Name: " . $_categorytop->getName();
								#echo "Image: " . $_categorytop->getImage();
								#echo "STOREID: " . $store->getId();
							  #$cat = Mage::getModel('catalog/category')->load($_categorytop->getId());
							  $subcats = Mage::getModel('catalog/category')->getCollection()
            						->setStore($store)//sets store ID
									->addAttributeToFilter(array(array('attribute'=>'parent_id', 'eq'=>$_categorytop->getId())))//first level from the tree
									->addAttributeToSelect('*')//or any other attributes you need
									->setOrder('position'); 
								#$row['store'] = strtolower($store->getName());
								$row['store'] = strtolower($store->getCode());
								if($this->getVar('export_categories_for_transfer') == "true") {
								$row['category_id'] = $_categorytop->getId();
								$row['name'] = $_categorytop->getName();
								}
								$row['categories'] = $_categorytop->getName();
								$row['description'] = $_categorytop->getDescription();
								$row['url_key'] = $_categorytop->getUrlKey();
								$row['is_active'] = $_categorytop->getIsActive();
								$row['meta_title'] = $_categorytop->getMetaTitle();
								$row['url_path'] = $_categorytop->getUrlPath();
								$row['is_anchor'] = $_categorytop->getIsAnchor();
								$row['meta_keywords'] = $_categorytop->getMetaKeywords();
								$row['meta_description'] = $_categorytop->getMetaDescription();
								$row['display_mode'] = $_categorytop->getDisplayMode();
								$row['page_layout'] = $_categorytop->getPageLayout();
								$row['cms_block'] = $_categorytop->getLandingPage();
								$row['custom_layout_update'] = $_categorytop->getCustomLayoutUpdate();
								$row['custom_design'] = $_categorytop->getCustomDesign();
								$row['category_image'] = $_categorytop->getImage();
								$row['category_thumb_image'] = $_categorytop->getThumbnail();
								$row['include_in_menu'] = $_categorytop->getIncludeInMenu();
								$verChecksplit = explode(".",Mage::getVersion());
								// 1.7.x ONLY
								if ($verChecksplit[1] >= 7) {
								$row['custom_apply_to_products'] = $_categorytop->getCustomApplyToProducts();
								$row['custom_use_parent_settings'] = $_categorytop->getCustomUseParentSettings();
								}
								$row['position'] = $_categorytop->getPosition();
								
								//START CUSTOM CODE CATEGORY PRODUCT EXPORT
								if($this->getVar('export_products_for_categories') == "true") {
									$category_products_export = "";
									$resource = Mage::getSingleton('core/resource');
									$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
									$read = $resource->getConnection('core_read');
									
									$select_qry = "SELECT category_id,product_id,position FROM `".$prefix."catalog_category_product` WHERE category_id ='".$_categorytop->getId()."'";
									$catrows = $read->fetchAll($select_qry);
									foreach($catrows as $catproductdata)
									{ 
										$product = Mage::getModel('catalog/product')->load($catproductdata['product_id']);
										if($this->getVar('export_product_position') == "true") {
											$category_products_export .= $product->getSku() . ":" . $catproductdata['position'] . ",";
										} else {
											$category_products_export .= $product->getSku() . ",";
										}
									}
									$row['category_products'] = substr_replace($category_products_export,"",-1);
								}
								//END CUSTOM CODE CATEGORY PRODUCT EXPORT
								$batchExport = $this->getBatchExportModel()
										->setId(null)
										->setBatchId($this->getBatchModel()->getId())
										->setBatchData($row)
										->setStatus(1)
										->save();
							
					/*Returns comma separated ids*/
					#$subcats = $cat->getChildren();
					
					//Print out categories string
					#print_r($subcats);
					
					#foreach(explode(',',$subcats) as $subCatid)
					foreach ($subcats as $_category)
					{
						#$_category = Mage::getModel('catalog/category')->load($subCatid);
						#if($_category->getIsActive())
						#{
							#$_category->getURL();
							
							#if($subCatid > 0) {
							$row2['rootid'] = $rootId;
							#$row2['store'] = strtolower($_category->getStore()->getName());
						    #$row2['store'] = strtolower($store->getName());
						    $row2['store'] = strtolower($store->getCode());
							if($this->getVar('export_categories_for_transfer') == "true") {
							$row2['category_id'] = $_category->getId();
							$row2['name'] = $_category->getName();
							}
							$row2['categories'] = $_categorytop->getName() . $category_delimiter . $_category->getName();
							$row2['description'] = $_category->getDescription();
							$row2['url_key'] = $_category->getUrlKey();
							$row2['is_active'] = $_category->getIsActive();
							$row2['meta_title'] = $_category->getMetaTitle();
							$row2['url_path'] = $_category->getUrlPath();
							$row2['is_anchor'] = $_category->getIsAnchor();
							$row2['meta_keywords'] = $_category->getMetaKeywords();
							$row2['meta_description'] = $_category->getMetaDescription();
							$row2['display_mode'] = $_category->getDisplayMode();
							$row2['page_layout'] = $_category->getPageLayout();
							$row2['cms_block'] = $_category->getLandingPage();
							$row2['custom_layout_update'] = $_category->getCustomLayoutUpdate();
							$row2['custom_design'] = $_category->getCustomDesign();
							$row2['category_image'] = $_category->getImage();
							$row2['category_thumb_image'] = $_category->getThumbnail();
							$row2['include_in_menu'] = $_category->getIncludeInMenu();
							$verChecksplit = explode(".",Mage::getVersion());
							// 1.7.x ONLY
							if ($verChecksplit[1] >= 7) {
							$row2['custom_apply_to_products'] = $_category->getCustomApplyToProducts();
							$row2['custom_use_parent_settings'] = $_category->getCustomUseParentSettings();
							}
							$row2['position'] = $_category->getPosition();
							
							//START CUSTOM CODE CATEGORY PRODUCT EXPORT
							if($this->getVar('export_products_for_categories') == "true") {
								$category_products_export = "";
								$resource = Mage::getSingleton('core/resource');
								$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
								$read = $resource->getConnection('core_read');
								
								$select_qry = "SELECT category_id,product_id,position FROM `".$prefix."catalog_category_product` WHERE category_id ='".$_category->getId()."'";
								$catrows = $read->fetchAll($select_qry);
								foreach($catrows as $catproductdata)
								{ 
									$product = Mage::getModel('catalog/product')->load($catproductdata['product_id']);
									if($this->getVar('export_product_position') == "true") {
										$category_products_export .= $product->getSku() . ":" . $catproductdata['position'] . ",";
									} else {
										$category_products_export .= $product->getSku() . ",";
									}
								}
								$row2['category_products'] = substr_replace($category_products_export,"",-1);
							}
							//END CUSTOM CODE CATEGORY PRODUCT EXPORT
							
							if($_category->getImageUrl())
							{
								$catimg = $_category->getImageUrl();
							}
							
							$batchExport = $this->getBatchExportModel()
									->setId(null)
									->setBatchId($this->getBatchModel()->getId())
									->setBatchData($row2)
									->setStatus(1)
									->save();
							#}
						#}
						
						#echo "CAT ID: " . $_category->getId();
						#$subcategoriesmodel = Mage::getModel('catalog/category')->load($_category->getId());
						#$subcategories = $subcategoriesmodel->getChildren();
						$subcategories = Mage::getModel('catalog/category')->getCollection()
								->setStore($store)//sets store ID
								->addAttributeToFilter(array(array('attribute'=>'parent_id', 'eq'=>$_category->getId())))//first level from the tree
								->addAttributeToSelect('*')//or any other attributes you need
								->setOrder('position'); 
						#echo "SUB CAT ID: " . $subcategories;
						#foreach(explode(',',$subcategories) as $subcategoriesid)
						foreach ($subcategories as $_sub_category)
						{
							$subcategoriesid=1;
							if($subcategoriesid > 0) {
								#$_sub_category = Mage::getModel('catalog/category')->load($subcategoriesid);
								$row3['rootid'] = $rootId;
								#$row3['store'] = strtolower($_sub_category->getStore()->getName());
						        #$row3['store'] = strtolower($store->getName());
						        $row3['store'] = strtolower($store->getCode());
								if($this->getVar('export_categories_for_transfer') == "true") {
								$row3['category_id'] = $_sub_category->getId();
								$row3['name'] = $_sub_category->getName();
								}
								$row3['categories'] = $_categorytop->getName() . $category_delimiter . $_category->getName() . $category_delimiter . $_sub_category->getName();
								$row3['description'] = $_sub_category->getDescription();
								$row3['url_key'] = $_sub_category->getUrlKey();
								$row3['is_active'] = $_sub_category->getIsActive();
								$row3['meta_title'] = $_sub_category->getMetaTitle();
								$row3['url_path'] = $_sub_category->getUrlPath();
								$row3['is_anchor'] = $_sub_category->getIsAnchor();
								$row3['meta_keywords'] = $_sub_category->getMetaKeywords();
								$row3['meta_description'] = $_sub_category->getMetaDescription();
								$row3['display_mode'] = $_sub_category->getDisplayMode();
								$row3['page_layout'] = $_sub_category->getPageLayout();
								$row3['cms_block'] = $_sub_category->getLandingPage();
								$row3['custom_layout_update'] = $_sub_category->getCustomLayoutUpdate();
								$row3['custom_design'] = $_sub_category->getCustomDesign();
							  	$row3['category_image'] = $_sub_category->getImage();
								$row3['category_thumb_image'] = $_sub_category->getThumbnail();
								$row3['include_in_menu'] = $_sub_category->getIncludeInMenu();
								$verChecksplit = explode(".",Mage::getVersion());
								// 1.7.x ONLY
								if ($verChecksplit[1] >= 7) {
								$row3['custom_apply_to_products'] = $_sub_category->getCustomApplyToProducts();
								$row3['custom_use_parent_settings'] = $_sub_category->getCustomUseParentSettings();
								}
								$row3['position'] = $_sub_category->getPosition();
								
								//START CUSTOM CODE CATEGORY PRODUCT EXPORT
								if($this->getVar('export_products_for_categories') == "true") {
									$category_products_export = "";
									$resource = Mage::getSingleton('core/resource');
									$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
									$read = $resource->getConnection('core_read');
									
									$select_qry = "SELECT category_id,product_id,position FROM `".$prefix."catalog_category_product` WHERE category_id ='".$_sub_category->getId()."'";
									$catrows = $read->fetchAll($select_qry);
									foreach($catrows as $catproductdata)
									{ 
										$product = Mage::getModel('catalog/product')->load($catproductdata['product_id']);
										if($this->getVar('export_product_position') == "true") {
											$category_products_export .= $product->getSku() . ":" . $catproductdata['position'] . ",";
										} else {
											$category_products_export .= $product->getSku() . ",";
										}
									}
									$row3['category_products'] = substr_replace($category_products_export,"",-1);
								}
								//END CUSTOM CODE CATEGORY PRODUCT EXPORT
								
								$batchExport = $this->getBatchExportModel()
										->setId(null)
										->setBatchId($this->getBatchModel()->getId())
										->setBatchData($row3)
										->setStatus(1)
										->save();
										
								 /* START OF 3rd LEVEL CATEGORY EXPORT */
							  #echo "CAT ID: " . $_category->getId();
								$subsubcategoriesmodel = Mage::getModel('catalog/category')->load($_sub_category->getId());
								$subsubcategories = $subsubcategoriesmodel->getChildren();
								#echo "SUB CAT ID: " . $subcategories;
								foreach(explode(',',$subsubcategories) as $subsubcategoriesid)
								{
									if($subsubcategoriesid > 0) {
										$_sub_sub_category = Mage::getModel('catalog/category')->setStoreId($store->getId())->load($subsubcategoriesid);
										$row4['rootid'] = $rootId;
										#$row4['store'] = strtolower($_sub_sub_category->getStore()->getName());
						       			#$row4['store'] = strtolower($store->getName());
						       			$row4['store'] = strtolower($store->getCode());
										if($this->getVar('export_categories_for_transfer') == "true") {
										$row4['category_id'] = $_sub_sub_category->getId();
										$row4['name'] = $_sub_sub_category->getName();
										}
										$row4['categories'] = $_categorytop->getName() . $category_delimiter . $_category->getName() . $category_delimiter . $_sub_category->getName(). $category_delimiter . $_sub_sub_category->getName();
										$row4['description'] = $_sub_sub_category->getDescription();
										$row4['url_key'] = $_sub_sub_category->getUrlKey();
										$row4['is_active'] = $_sub_sub_category->getIsActive();
										$row4['meta_title'] = $_sub_sub_category->getMetaTitle();
										$row4['url_path'] = $_sub_sub_category->getUrlPath();
										$row4['is_anchor'] = $_sub_sub_category->getIsAnchor();
										$row4['meta_keywords'] = $_sub_sub_category->getMetaKeywords();
										$row4['meta_description'] = $_sub_sub_category->getMetaDescription();
										$row4['display_mode'] = $_sub_sub_category->getDisplayMode();
										$row4['page_layout'] = $_sub_sub_category->getPageLayout();
										$row4['cms_block'] = $_sub_sub_category->getLandingPage();
										$row4['custom_layout_update'] = $_sub_sub_category->getCustomLayoutUpdate();
										$row4['custom_design'] = $_sub_sub_category->getCustomDesign();
							      		$row4['category_image'] = $_sub_sub_category->getImage();
										$row4['category_thumb_image'] = $_sub_sub_category->getThumbnail();
										$row4['include_in_menu'] = $_sub_sub_category->getIncludeInMenu();
										$verChecksplit = explode(".",Mage::getVersion());
										// 1.7.x ONLY
										if ($verChecksplit[1] >= 7) {
										$row4['custom_apply_to_products'] = $_sub_sub_category->getCustomApplyToProducts();
										$row4['custom_use_parent_settings'] = $_sub_sub_category->getCustomUseParentSettings();
										}
										$row4['position'] = $_sub_sub_category->getPosition();
										
										//START CUSTOM CODE CATEGORY PRODUCT EXPORT
										if($this->getVar('export_products_for_categories') == "true") {
											$category_products_export = "";
											$resource = Mage::getSingleton('core/resource');
											$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
											$read = $resource->getConnection('core_read');
											
											$select_qry = "SELECT category_id,product_id,position FROM `".$prefix."catalog_category_product` WHERE category_id ='".$_sub_sub_category->getId()."'";
											$catrows = $read->fetchAll($select_qry);
											foreach($catrows as $catproductdata)
											{ 
												$product = Mage::getModel('catalog/product')->load($catproductdata['product_id']);
												if($this->getVar('export_product_position') == "true") {
													$category_products_export .= $product->getSku() . ":" . $catproductdata['position'] . ",";
												} else {
													$category_products_export .= $product->getSku() . ",";
												}
											}
											$row4['category_products'] = substr_replace($category_products_export,"",-1);
										}
										//END CUSTOM CODE CATEGORY PRODUCT EXPORT
										
										$batchExport = $this->getBatchExportModel()
												->setId(null)
												->setBatchId($this->getBatchModel()->getId())
												->setBatchData($row4)
												->setStatus(1)
												->save();
												
												
												 /* START OF 4th LEVEL CATEGORY EXPORT */
												#echo "CAT ID: " . $_category->getId();
												$subsubsubcategoriesmodel = Mage::getModel('catalog/category')->load($_sub_sub_category->getId());
												$subsubsubcategories = $subsubsubcategoriesmodel->getChildren();
												#echo "SUB CAT ID: " . $subcategories;
												foreach(explode(',',$subsubsubcategories) as $subsubsubcategoriesid)
												{
													if($subsubsubcategoriesid > 0) {
														$_sub_sub_sub_category = Mage::getModel('catalog/category')->setStoreId($store->getId())->load($subsubsubcategoriesid);
														$row5['rootid'] = $rootId;
														#$row5['store'] = strtolower($_sub_sub_sub_category->getStore()->getName());
						        						#$row5['store'] = strtolower($store->getName());
						        						$row5['store'] = strtolower($store->getCode());
														if($this->getVar('export_categories_for_transfer') == "true") {
														$row5['category_id'] = $_sub_sub_sub_category->getId();
														$row5['name'] = $_sub_sub_sub_category->getName();
														}
														$row5['categories'] = $_categorytop->getName() . $category_delimiter . $_category->getName() . $category_delimiter . $_sub_category->getName(). $category_delimiter . $_sub_sub_category->getName(). $category_delimiter . $_sub_sub_sub_category->getName();
														$row5['description'] = $_sub_sub_sub_category->getDescription();
														$row5['url_key'] = $_sub_sub_sub_category->getUrlKey();
														$row5['is_active'] = $_sub_sub_sub_category->getIsActive();
														$row5['meta_title'] = $_sub_sub_sub_category->getMetaTitle();
														$row5['url_path'] = $_sub_sub_sub_category->getUrlPath();
														$row5['is_anchor'] = $_sub_sub_sub_category->getIsAnchor();
														$row5['meta_keywords'] = $_sub_sub_sub_category->getMetaKeywords();
														$row5['meta_description'] = $_sub_sub_sub_category->getMetaDescription();
														$row5['display_mode'] = $_sub_sub_sub_category->getDisplayMode();
														$row5['page_layout'] = $_sub_sub_sub_category->getPageLayout();
														$row5['cms_block'] = $_sub_sub_sub_category->getLandingPage();
														$row5['custom_layout_update'] = $_sub_sub_sub_category->getCustomLayoutUpdate();
														$row5['custom_design'] = $_sub_sub_sub_category->getCustomDesign();
														$row5['category_image'] = $_sub_sub_sub_category->getImage();
														$row5['category_thumb_image'] = $_sub_sub_sub_category->getThumbnail();
														$row5['include_in_menu'] = $_sub_sub_sub_category->getIncludeInMenu();
														$verChecksplit = explode(".",Mage::getVersion());
														// 1.7.x ONLY
														if ($verChecksplit[1] >= 7) {
														$row5['custom_apply_to_products'] = $_sub_sub_sub_category->getCustomApplyToProducts();
														$row5['custom_use_parent_settings'] = $_sub_sub_sub_category->getCustomUseParentSettings();
														}
														$row5['position'] = $_sub_sub_sub_category->getPosition();
														//START CUSTOM CODE CATEGORY PRODUCT EXPORT
														if($this->getVar('export_products_for_categories') == "true") {
															$category_products_export = "";
															$resource = Mage::getSingleton('core/resource');
															$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
															$read = $resource->getConnection('core_read');
															
															$select_qry = "SELECT category_id,product_id,position FROM `".$prefix."catalog_category_product` WHERE category_id ='".$_sub_sub_sub_category->getId()."'";
															$catrows = $read->fetchAll($select_qry);
															foreach($catrows as $catproductdata)
															{ 
																$product = Mage::getModel('catalog/product')->load($catproductdata['product_id']);
																if($this->getVar('export_product_position') == "true") {
																	$category_products_export .= $product->getSku() . ":" . $catproductdata['position'] . ",";
																} else {
																	$category_products_export .= $product->getSku() . ",";
																}
															}
															$row5['category_products'] = substr_replace($category_products_export,"",-1);
														}
														//END CUSTOM CODE CATEGORY PRODUCT EXPORT
														
														$batchExport = $this->getBatchExportModel()
																->setId(null)
																->setBatchId($this->getBatchModel()->getId())
																->setBatchData($row5)
																->setStatus(1)
																->save();
																
																  /* START OF 5th LEVEL CATEGORY EXPORT */
																	#echo "CAT ID: " . $_category->getId();
																	$subsubsubsubcategoriesmodel = Mage::getModel('catalog/category')->load($_sub_sub_sub_category->getId());
																	$subsubsubsubcategories = $subsubsubsubcategoriesmodel->getChildren();
																	#echo "SUB CAT ID: " . $subcategories;
																	foreach(explode(',',$subsubsubsubcategories) as $subsubsubsubcategoriesid)
																	{
																		if($subsubsubsubcategoriesid > 0) {
																			$_sub_sub_sub_sub_category = Mage::getModel('catalog/category')->setStoreId($store->getId())->load($subsubsubsubcategoriesid);
																			$row6['rootid'] = $rootId;
																			#$row6['store'] = strtolower($_sub_sub_sub_sub_category->getStore()->getName());
						        											#$row6['store'] = strtolower($store->getName());
						        											$row6['store'] = strtolower($store->getCode());
																			if($this->getVar('export_categories_for_transfer') == "true") {
																			$row6['category_id'] = $_sub_sub_sub_sub_category->getId();
																			$row6['name'] = $_sub_sub_sub_sub_category->getName();
																			}
																			$row6['categories'] = $_categorytop->getName() . $category_delimiter . $_category->getName() . $category_delimiter . $_sub_category->getName(). $category_delimiter . $_sub_sub_category->getName(). $category_delimiter . $_sub_sub_sub_category->getName(). $category_delimiter . $_sub_sub_sub_sub_category->getName();
																			$row6['description'] = $_sub_sub_sub_sub_category->getDescription();
																			$row6['url_key'] = $_sub_sub_sub_sub_category->getUrlKey();
																			$row6['is_active'] = $_sub_sub_sub_sub_category->getIsActive();
																			$row6['meta_title'] = $_sub_sub_sub_sub_category->getMetaTitle();
																			$row6['url_path'] = $_sub_sub_sub_sub_category->getUrlPath();
																			$row6['is_anchor'] = $_sub_sub_sub_sub_category->getIsAnchor();
																			$row6['meta_keywords'] = $_sub_sub_sub_sub_category->getMetaKeywords();
																			$row6['meta_description'] = $_sub_sub_sub_sub_category->getMetaDescription();
																			$row6['display_mode'] = $_sub_sub_sub_sub_category->getDisplayMode();
																			$row6['page_layout'] = $_sub_sub_sub_sub_category->getPageLayout();
																			$row6['cms_block'] = $_sub_sub_sub_sub_category->getLandingPage();
																			$row6['custom_layout_update'] = $_sub_sub_sub_sub_category->getCustomLayoutUpdate();
																			$row6['custom_design'] = $_sub_sub_sub_sub_category->getCustomDesign();
																			$row6['category_image'] = $_sub_sub_sub_sub_category->getImage();
																			$row6['category_thumb_image'] = $_sub_sub_sub_sub_category->getThumbnail();
																			$row6['include_in_menu'] = $_sub_sub_sub_sub_category->getIncludeInMenu();
																			$verChecksplit = explode(".",Mage::getVersion());
																			// 1.7.x ONLY
																			if ($verChecksplit[1] >= 7) {
																			$row6['custom_apply_to_products'] = $_sub_sub_sub_sub_category->getCustomApplyToProducts();
																			$row6['custom_use_parent_settings'] = $_sub_sub_sub_sub_category->getCustomUseParentSettings();
																			}
																			$row6['position'] = $_sub_sub_sub_sub_category->getPosition();
														//START CUSTOM CODE CATEGORY PRODUCT EXPORT
														if($this->getVar('export_products_for_categories') == "true") {
															$category_products_export = "";
															$resource = Mage::getSingleton('core/resource');
															$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
															$read = $resource->getConnection('core_read');
															
															$select_qry = "SELECT category_id,product_id,position FROM `".$prefix."catalog_category_product` WHERE category_id ='".$_sub_sub_sub_sub_category->getId()."'";
															$catrows = $read->fetchAll($select_qry);
															foreach($catrows as $catproductdata)
															{ 
																$product = Mage::getModel('catalog/product')->load($catproductdata['product_id']);
																if($this->getVar('export_product_position') == "true") {
																	$category_products_export .= $product->getSku() . ":" . $catproductdata['position'] . ",";
																} else {
																	$category_products_export .= $product->getSku() . ",";
																}
															}
															$row6['category_products'] = substr_replace($category_products_export,"",-1);
														}
														//END CUSTOM CODE CATEGORY PRODUCT EXPORT
																			
																			$batchExport = $this->getBatchExportModel()
																					->setId(null)
																					->setBatchId($this->getBatchModel()->getId())
																					->setBatchData($row6)
																					->setStatus(1)
																					->save();
																					
																					/* START OF 6th LEVEL CATEGORY EXPORT */
																					#echo "CAT ID: " . $_category->getId();
																					$subsubsubsubsubcategoriesmodel = Mage::getModel('catalog/category')->load($_sub_sub_sub_sub_category->getId());
																					$subsubsubsubsubcategories = $subsubsubsubsubcategoriesmodel->getChildren();
																					#echo "SUB CAT ID: " . $subcategories;
																					foreach(explode(',',$subsubsubsubsubcategories) as $subsubsubsubsubcategoriesid)
																					{
																						if($subsubsubsubsubcategoriesid > 0) {
																							$_sub_sub_sub_sub_sub_category = Mage::getModel('catalog/category')->setStoreId($store->getId())->load($subsubsubsubsubcategoriesid);
																							$row7['rootid'] = $rootId;
																							#$row7['store'] = strtolower($_sub_sub_sub_sub_category->getStore()->getName());
																							#$row7['store'] = strtolower($store->getName());
																							$row7['store'] = strtolower($store->getCode());
																							if($this->getVar('export_categories_for_transfer') == "true") {
																							$row7['category_id'] = $_sub_sub_sub_sub_sub_category->getId();
																							$row7['name'] = $_sub_sub_sub_sub_sub_category->getName();
																							}
																							$row7['categories'] = $_categorytop->getName() . $category_delimiter . $_category->getName() . $category_delimiter . $_sub_category->getName(). $category_delimiter . $_sub_sub_category->getName(). $category_delimiter . $_sub_sub_sub_category->getName(). $category_delimiter . $_sub_sub_sub_sub_category->getName(). $category_delimiter . $_sub_sub_sub_sub_sub_category->getName();
																							$row7['description'] = $_sub_sub_sub_sub_sub_category->getDescription();
																							$row7['url_key'] = $_sub_sub_sub_sub_sub_category->getUrlKey();
																							$row7['is_active'] = $_sub_sub_sub_sub_sub_category->getIsActive();
																							$row7['meta_title'] = $_sub_sub_sub_sub_sub_category->getMetaTitle();
																							$row7['url_path'] = $_sub_sub_sub_sub_sub_category->getUrlPath();
																							$row7['is_anchor'] = $_sub_sub_sub_sub_sub_category->getIsAnchor();
																							$row7['meta_keywords'] = $_sub_sub_sub_sub_sub_category->getMetaKeywords();
																							$row7['meta_description'] = $_sub_sub_sub_sub_sub_category->getMetaDescription();
																							$row7['display_mode'] = $_sub_sub_sub_sub_sub_category->getDisplayMode();
																							$row7['page_layout'] = $_sub_sub_sub_sub_sub_category->getPageLayout();
																							$row7['cms_block'] = $_sub_sub_sub_sub_sub_category->getLandingPage();
																							$row7['custom_layout_update'] = $_sub_sub_sub_sub_sub_category->getCustomLayoutUpdate();
																							$row7['custom_design'] = $_sub_sub_sub_sub_sub_category->getCustomDesign();
																							$row7['category_image'] = $_sub_sub_sub_sub_sub_category->getImage();
																							$row7['category_thumb_image'] = $_sub_sub_sub_sub_sub_category->getThumbnail();
																							$row7['include_in_menu'] = $_sub_sub_sub_sub_sub_category->getIncludeInMenu();
																							$verChecksplit = explode(".",Mage::getVersion());
																							// 1.7.x ONLY
																							if ($verChecksplit[1] >= 7) {
																							$row7['custom_apply_to_products'] = $_sub_sub_sub_sub_category->getCustomApplyToProducts();
																							$row7['custom_use_parent_settings'] = $_sub_sub_sub_sub_category->getCustomUseParentSettings();
																							}
																							$row7['position'] = $_sub_sub_sub_sub_sub_category->getPosition();
																							
														//START CUSTOM CODE CATEGORY PRODUCT EXPORT
														if($this->getVar('export_products_for_categories') == "true") {
															$category_products_export = "";
															$resource = Mage::getSingleton('core/resource');
															$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
															$read = $resource->getConnection('core_read');
															
															$select_qry = "SELECT category_id,product_id,position FROM `".$prefix."catalog_category_product` WHERE category_id ='".$_sub_sub_sub_sub_sub_category->getId()."'";
															$catrows = $read->fetchAll($select_qry);
															foreach($catrows as $catproductdata)
															{ 
																$product = Mage::getModel('catalog/product')->load($catproductdata['product_id']);
																if($this->getVar('export_product_position') == "true") {
																	$category_products_export .= $product->getSku() . ":" . $catproductdata['position'] . ",";
																} else {
																	$category_products_export .= $product->getSku() . ",";
																}
															}
															$row7['category_products'] = substr_replace($category_products_export,"",-1);
														}
														//END CUSTOM CODE CATEGORY PRODUCT EXPORT
																							$batchExport = $this->getBatchExportModel()
																									->setId(null)
																									->setBatchId($this->getBatchModel()->getId())
																									->setBatchData($row7)
																									->setStatus(1)
																									->save();
																						}
																					}
																			}
																	}
														}
												}
										}
								   }
							     }
						      }
							}
						}
					   }
					 //comment by nisha }
					}
        return $this;
		}
}

?>