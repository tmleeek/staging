<?php
/**
 * Productexport.php
 * CommerceThemes @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commercethemes.com/LICENSE-M1.txt
 *
 * @category   Product
 * @package    Productexport
 * @copyright  Copyright (c) 2003-2009 CommerceThemes @ InterSEC Solutions LLC. (http://www.commercethemes.com)
 * @license    http://www.commercethemes.com/LICENSE-M1.txt
 */ 


class Mage_Catalog_Model_Convert_Parser_ProductAttributesexport
    extends Mage_Eav_Model_Convert_Parser_Abstract
{
    const MULTI_DELIMITER = ' , ';
    protected $_resource;

    /**
     * Product collections per store
     *
     * @var array
     */
    protected $_collections;

    protected $_productTypes = array(
        'simple'=>'Simple',
        'bundle'=>'Bundle',
        'configurable'=>'Configurable',
        'grouped'=>'Grouped',
        'virtual'=>'Virtual',
    );

    protected $_inventoryFields = array();

    protected $_imageFields = array();

    protected $_systemFields = array();
    protected $_internalFields = array();
    protected $_externalFields = array();

    protected $_inventoryItems = array();

    protected $_productModel;

    protected $_setInstances = array();

    protected $_store;
    protected $_storeId;
    protected $_attributes = array();

    public function __construct()
    {
        foreach (Mage::getConfig()->getFieldset('catalog_product_dataflow', 'admin') as $code=>$node) {
            if ($node->is('inventory')) {
                $this->_inventoryFields[] = $code;
                if ($node->is('use_config')) {
                    $this->_inventoryFields[] = 'use_config_'.$code;
                }
            }
            if ($node->is('internal')) {
                $this->_internalFields[] = $code;
            }
            if ($node->is('system')) {
                $this->_systemFields[] = $code;
            }
            if ($node->is('external')) {
                $this->_externalFields[$code] = $code;
            }
            if ($node->is('img')) {
                $this->_imageFields[] = $code;
            }
        }
    }

    /**
     * @return Mage_Catalog_Model_Mysql4_Convert
     */
    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Mage::getResourceSingleton('catalog_entity/convert');
                #->loadStores()
                #->loadProducts()
                #->loadAttributeSets()
                #->loadAttributeOptions();
        }
        return $this->_resource;
    }

    public function getCollection($storeId)
    {
        if (!isset($this->_collections[$storeId])) {
            $this->_collections[$storeId] = Mage::getResourceModel('catalog/product_collection');
            $this->_collections[$storeId]->getEntity()->setStore($storeId);
        }
        return $this->_collections[$storeId];
    }

    public function getProductTypeName($id)
    {
        return isset($this->_productTypes[$id]) ? $this->_productTypes[$id] : false;
    }

    public function getProductTypeId($name)
    {
        return array_search($name, $this->_productTypes);
    }

    /**
     * Retrieve product model cache
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProductModel()
    {
        if (is_null($this->_productModel)) {
            $productModel = Mage::getModel('catalog/product');
            $this->_productModel = Mage::objects()->save($productModel);
        }
        return Mage::objects()->load($this->_productModel);
    }

    /**
     * Retrieve current store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            try {
                $store = Mage::app()->getStore($this->getVar('store'));
            }
            catch (Exception $e) {
                $this->addException(Mage::helper('catalog')->__('Invalid store specified'), Varien_Convert_Exception::FATAL);
                throw $e;
            }
            $this->_store = $store;
        }
        return $this->_store;
    }

    /**
     * Retrieve store ID
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->_storeId = $this->getStore()->getId();
        }
        return $this->_storeId;
    }

    public function getAttributeSetInstance()
    {
        $productType = $this->getProductModel()->getType();
        $attributeSetId = $this->getProductModel()->getAttributeSetId();

        if (!isset($this->_setInstances[$productType][$attributeSetId])) {
            $this->_setInstances[$productType][$attributeSetId] =
                Mage::getSingleton('catalog/product_type')->factory($this->getProductModel());
        }

        return $this->_setInstances[$productType][$attributeSetId];
    }

    /**
     * Retrieve eav entity attribute model
     *
     * @param string $code
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute($code)
    {
        if (!isset($this->_attributes[$code])) {
            $this->_attributes[$code] = $this->getProductModel()->getResource()->getAttribute($code);
        }
        return $this->_attributes[$code];
    }

    /**
     * @deprecated not used anymore
     */
    public function parse()
    {
        $data = $this->getData();

        $entityTypeId = Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId();

        $result = array();
        $inventoryFields = array();
        foreach ($data as $i=>$row) {
            $this->setPosition('Line: '.($i+1));
            try {
                // validate SKU
                if (empty($row['sku'])) {
                    $this->addException(Mage::helper('catalog')->__('Missing SKU, skipping the record'), Mage_Dataflow_Model_Convert_Exception::ERROR);
                    continue;
                }
                $this->setPosition('Line: '.($i+1).', SKU: '.$row['sku']);

                // try to get entity_id by sku if not set
                if (empty($row['entity_id'])) {
                    $row['entity_id'] = $this->getResource()->getProductIdBySku($row['sku']);
                }

                // if attribute_set not set use default
                if (empty($row['attribute_set'])) {
                    $row['attribute_set'] = 'Default';
                }
                // get attribute_set_id, if not throw error
                $row['attribute_set_id'] = $this->getAttributeSetId($entityTypeId, $row['attribute_set']);
                if (!$row['attribute_set_id']) {
                    $this->addException(Mage::helper('catalog')->__("Invalid attribute set specified, skipping the record"), Mage_Dataflow_Model_Convert_Exception::ERROR);
                    continue;
                }

                if (empty($row['type'])) {
                    $row['type'] = 'Simple';
                }
                // get product type_id, if not throw error
                $row['type_id'] = $this->getProductTypeId($row['type']);
                if (!$row['type_id']) {
                    $this->addException(Mage::helper('catalog')->__("Invalid product type specified, skipping the record"), Mage_Dataflow_Model_Convert_Exception::ERROR);
                    continue;
                }

                // get store ids
                $storeIds = $this->getStoreIds(isset($row['store']) ? $row['store'] : $this->getVar('store'));
                if (!$storeIds) {
                    $this->addException(Mage::helper('catalog')->__("Invalid store specified, skipping the record"), Mage_Dataflow_Model_Convert_Exception::ERROR);
                    continue;
                }

                // import data
                $rowError = false;
                foreach ($storeIds as $storeId) {
                    $collection = $this->getCollection($storeId);
                    $entity = $collection->getEntity();

                    $model = Mage::getModel('catalog/product');
                    $model->setStoreId($storeId);
                    if (!empty($row['entity_id'])) {
                        $model->load($row['entity_id']);
                    }
                    foreach ($row as $field=>$value) {
                        $attribute = $entity->getAttribute($field);

                        if (!$attribute) {
                            //$inventoryFields[$row['sku']][$field] = $value;

                            if (in_array($field, $this->_inventoryFields)) {
                                $inventoryFields[$row['sku']][$field] = $value;
                            }
                            continue;
                            #$this->addException(Mage::helper('catalog')->__("Unknown attribute: %s", $field), Mage_Dataflow_Model_Convert_Exception::ERROR);
                        }
                        if ($attribute->usesSource()) {
                            $source = $attribute->getSource();
                            $optionId = $this->getSourceOptionId($source, $value);
                            if (is_null($optionId)) {
                                $rowError = true;
                                $this->addException(Mage::helper('catalog')->__("Invalid attribute option specified for attribute %s (%s), skipping the record", $field, $value), Mage_Dataflow_Model_Convert_Exception::ERROR);
                                continue;
                            }
                            $value = $optionId;
                        }
                        $model->setData($field, $value);

                    }//foreach ($row as $field=>$value)

                    //echo 'Before **********************<br/><pre>';
                    //print_r($model->getData());
                    if (!$rowError) {
                        $collection->addItem($model);
                    }
                    unset($model);
                } //foreach ($storeIds as $storeId)
            } catch (Exception $e) {
                if (!$e instanceof Mage_Dataflow_Model_Convert_Exception) {
                    $this->addException(Mage::helper('catalog')->__("Error during retrieval of option value: %s", $e->getMessage()), Mage_Dataflow_Model_Convert_Exception::FATAL);
                }
            }
        }

        // set importinted to adaptor
        if (sizeof($inventoryFields) > 0) {
            Mage::register('current_imported_inventory', $inventoryFields);
            //$this->setInventoryItems($inventoryFields);
        } // end setting imported to adaptor

        $this->setData($this->_collections);
        return $this;
    }

    public function setInventoryItems($items)
    {
        $this->_inventoryItems = $items;
    }

    public function getInventoryItems()
    {
        return $this->_inventoryItems;
    }

    /**
     * Unparse (prepare data) loaded products
     *
     * @return Mage_Catalog_Model_Convert_Parser_Product
     */
    public function unparse()
    {
       $storeID = $this->getVar('store');
       $EntityTypeId = $this->getVar('entitytypeid');
			 #$EntityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
       $recordlimit = $this->getVar('recordlimit');
			 $resource = Mage::getSingleton('core/resource');
			 $prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix');
			 $read = $resource->getConnection('core_read');
			 $row = array();
			 /* THIS IS 1.3.x and back SQL */
			 //SELECT m_eav_attribute.*, m_eav_attribute_set.*, m_eav_attribute_group.* FROM `m_eav_attribute` INNER JOIN m_eav_entity_attribute ON m_eav_entity_attribute.entity_type_id = m_eav_attribute.entity_type_id AND m_eav_entity_attribute.attribute_id = m_eav_attribute.attribute_id INNER JOIN m_eav_attribute_set ON m_eav_attribute_set.attribute_set_id = m_eav_entity_attribute.attribute_set_id INNER JOIN m_eav_attribute_group ON m_eav_attribute_group.attribute_group_id = m_eav_entity_attribute.attribute_group_id WHERE m_eav_attribute.entity_type_id = '10'
			 
			 /* THIS IS 1.4 SQL NOTE ADDITION OF catalog_eav_attribute */
			 //SELECT eav_attribute.*, eav_attribute_set.*, eav_attribute_group.*, catalog_eav_attribute.* FROM `eav_attribute` INNER JOIN eav_entity_attribute ON eav_entity_attribute.entity_type_id = eav_attribute.entity_type_id AND eav_entity_attribute.attribute_id = eav_attribute.attribute_id INNER JOIN catalog_eav_attribute ON catalog_eav_attribute.attribute_id = eav_attribute.attribute_id INNER JOIN eav_attribute_set ON eav_attribute_set.attribute_set_id = eav_entity_attribute.attribute_set_id INNER JOIN eav_attribute_group ON eav_attribute_group.attribute_group_id = eav_entity_attribute.attribute_group_id WHERE eav_attribute.entity_type_id = '10'
			 
			 $select_qry = "SELECT ".$prefix."eav_attribute.*, ".$prefix."eav_attribute_set.*, ".$prefix."eav_attribute_group .*, ".$prefix."catalog_eav_attribute.* FROM `".$prefix."eav_attribute` INNER JOIN ".$prefix."eav_entity_attribute ON ".$prefix."eav_entity_attribute.entity_type_id = ".$prefix."eav_attribute.entity_type_id AND ".$prefix."eav_entity_attribute.attribute_id = ".$prefix."eav_attribute.attribute_id INNER JOIN ".$prefix."catalog_eav_attribute ON ".$prefix."catalog_eav_attribute.attribute_id = ".$prefix."eav_attribute.attribute_id INNER JOIN ".$prefix."eav_attribute_set ON ".$prefix."eav_attribute_set.attribute_set_id = ".$prefix."eav_entity_attribute.attribute_set_id INNER JOIN ".$prefix."eav_attribute_group ON ".$prefix."eav_attribute_group.attribute_group_id = ".$prefix."eav_entity_attribute.attribute_group_id WHERE ".$prefix."eav_attribute.entity_type_id = '".$EntityTypeId."' LIMIT ".$recordlimit."";
			 
			 $rows = $read->fetchAll($select_qry);
					foreach($rows as $data)
					 { 
					 	 #print_r($data);
						 $row["EntityTypeId"] = $EntityTypeId;
						 $row["attribute_set"] = $data['attribute_set_name'];
						 $row["attribute_name"] = $data['attribute_code'];
						 $row["attribute_group_name"] = $data['attribute_group_name'];
						 $row["is_global"] = $data['is_global'];
						 $row["is_user_defined"] = $data['is_user_defined'];
						 $row["is_filterable"] = $data['is_filterable'];
						 $row["is_visible"] = $data['is_visible'];
						 $row["is_required"] = $data['is_required'];
						 $row["is_visible_on_front"] = $data['is_visible_on_front'];
						 $row["is_searchable"] = $data['is_searchable'];
						 $row["is_unique"] = $data['is_unique'];
						 $row["is_configurable"] = $data['is_configurable'];
						 
						 //latest additional fields (values = 0: NO / 1: YES)
						 $row["frontend_class"] = $data['frontend_class'];
						 $row["is_visible_in_advanced_search"] = $data['is_visible_in_advanced_search'];
						 $row["is_comparable"] = $data['is_comparable'];
						 $row["is_filterable_in_search"] = $data['is_filterable_in_search'];
						 $row["is_used_for_price_rules"] = $data['is_used_for_price_rules'];
						 $row["position"] = $data['position'];
						 if(isset($data['is_html_allowed_on_front'])) {
						 $row["is_html_allowed_on_front"] = $data['is_html_allowed_on_front'];
						 }
						 if(isset($data['used_in_product_listing'])) {
						 $row["used_in_product_listing"] = $data['used_in_product_listing'];
						 }
						 if(isset($data['used_for_sort_by'])) {
						 $row["used_for_sort_by"] = $data['used_for_sort_by'];
						 }
										 
						 //frontend_input and backend_type #[useable types:]# decimal,int,select,text
						 $row["frontend_input"] = $data['frontend_input'];
						 $row["backend_type"] = $data['backend_type'];
						 if($data['frontend_label']!="") {	
								  $finalproductlabelattributes="";
						 		  $finalproductlabelattributes .=  "0:".$data['frontend_label'] . "|";
						 			$select_attribute_labels_qry = "SELECT store_id, value FROM ".$prefix."eav_attribute_label WHERE attribute_id = '".$data['attribute_id']."'";
						 			$attributelabelrows = $read->fetchAll($select_attribute_labels_qry);
									foreach($attributelabelrows as $attributelabeldata) { 
								 		 $finalproductlabelattributes .= $attributelabeldata["store_id"] . ":" . $attributelabeldata["value"] . "|";
									}		
						 		$row["frontend_label"] = substr_replace($finalproductlabelattributes,"",-1);		
									
						 } else {
						 		$row["frontend_label"] = $data['frontend_label'];
						 }
						 $row["default_value"] = $data['default_value'];
						 
						 //apply_to #[OPTIONAL usable types:]# simple,grouped,configurable,virtual,downloadable,bundle
						 $row["apply_to"] = $data['apply_to'];
							
						 //this will get all options for a attribute (dropdown/multi select/etc)
						 $finalproductattributes="";
						 $select_attribute_options_qry = "SELECT ".$prefix."eav_attribute.*, ".$prefix."eav_attribute_option_value.* FROM `".$prefix."eav_attribute` INNER JOIN ".$prefix."eav_attribute_option ON ".$prefix."eav_attribute_option.attribute_id = ".$prefix."eav_attribute.attribute_id INNER JOIN ".$prefix."eav_attribute_option_value ON ".$prefix."eav_attribute_option_value.option_id = ".$prefix."eav_attribute_option.option_id WHERE ".$prefix."eav_attribute.attribute_id = '".$data['attribute_id']."'";
			 				/*
						 $attributeoptionrows = $read->fetchAll($select_attribute_options_qry);
								foreach($attributeoptionrows as $attributeoptiondata)
								 { 						
								 	$finalproductattributes .= $attributeoptiondata["store_id"] . ":" . $attributeoptiondata["value"] . "|";
						 		 }
						 $row["attribute_options"] = substr_replace($finalproductattributes,"",-1);
						 			
							*/
							$attributeoptionrows = $read->fetchAll($select_attribute_options_qry);
								foreach($attributeoptionrows as $attributeoptiondata)
								 {
								  if(!isset($temp) || $temp == $attributeoptiondata["option_id"]) {		 
								 	#if(isset($temp)) { echo "TEMP " . $temp; }
								 	#echo "OPTID " . $attributeoptiondata["option_id"];
								  		$finalproductattributes .= $attributeoptiondata["store_id"] . ":" . $attributeoptiondata["value"] . ",";	
									}	else { 
								 	#echo "TEMP1 " . $temp;
								 	#echo "OPTID1 " . $attributeoptiondata["option_id"];
											$finalproductattributes = substr_replace($finalproductattributes,"",-1);
											$finalproductattributes .= "|";
								 			$finalproductattributes .= $attributeoptiondata["store_id"] . ":" . $attributeoptiondata["value"] . ",";	
									}
								  $temp = $attributeoptiondata["option_id"];
						 		 }
								 $finalproductattributes = substr_replace($finalproductattributes,"",-1);
								 if($finalproductattributes !="") {
								 	$finalproductattributes .= "|";
								 }
								 $finalproductattributes = ltrim($finalproductattributes, "|");
								 $finalproductattributes = rtrim($finalproductattributes, "|");
								 #echo "FULL: " . $finalproductattributes . "<br/>";
						 $row["attribute_options"] = $finalproductattributes;					
						 
            $batchExport = $this->getBatchExportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($row)
                ->setStatus(1)
                ->save();
				 }
					
        return $this;
    }

    /**
     * Retrieve accessible external product attributes
     *
     * @return array
     */
    public function getExternalAttributes()
    {
        $entityTypeId = Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId();
        $productAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entityTypeId)
            ->load();

            var_dump($this->_externalFields);

        $attributes = $this->_externalFields;

        foreach ($productAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $this->_internalFields) || $attr->getFrontendInput() == 'hidden') {
                continue;
            }
            $attributes[$code] = $code;
        }

        foreach ($this->_inventoryFields as $field) {
            $attributes[$field] = $field;
        }

        return $attributes;
    }
}