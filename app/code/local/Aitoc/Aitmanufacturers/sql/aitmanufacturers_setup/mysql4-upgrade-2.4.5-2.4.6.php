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
 * @copyright  Copyright (c) 2010 AITOC, Inc. 
 * @author chelevich
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer->startSetup();

// make the attribute appear in the catalog flat table
$installer->updateAttribute('catalog_product', 'aitmanufacturers_sort', 'used_for_sort_by', 1);

// From the previous module version we have the attribute values for the particular store_id only 
// saved in `catalog_product_entity_int`
// There are no values for the default scope 
// It is critical to fill a product flat tables with attribute values

// if attribute is required, so default scope value row will be created into `catalog_product_entity_int` automatically
$installer->updateAttribute('catalog_product', 'aitmanufacturers_sort', 'is_required', 1);

// do resave 'aitmanufacturers_sort' attribute values

$attrEntity = 'catalog_product';
$attrCode = 'aitmanufacturers_sort';

$attr = $installer->getAttribute($attrEntity, $attrCode);
$attrId = $installer->getAttributeId($attrEntity, $attrCode); // $attr['attribute_id'];
$attrTable = $installer->getAttributeTable($attrEntity, $attrCode);

// for a some reason Mage::app() doesn't have the stores initialized still
Mage::app()->reinitStores();

foreach(Mage::app()->getStores() as $store)
{
    /* !AITOC_MARK:manufacturer_collection */
    $collection = Mage::getModel('catalog/product')->getCollection();
    /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
    
    $whereParams = array(
        'attribute_id' => $attrId,
    );
    if (!Mage::app()->isSingleStoreMode())
    {
        $whereParams['store_id'] = $store->getId();
    }
    
    // we can't use joinAttribute() method because it requires default scope values for the attribute
    $collection->joinField($attrCode, $attrTable, 'value', 'entity_id=entity_id', $whereParams, 'inner');
    
    foreach($collection as $item)
    {
        /* @var $item Mage_Catalog_Model_Product */
        $sort = $item->getData($attrCode);
        if (!is_null($sort))
        {
            // delete the value
            $item->addAttributeUpdate('aitmanufacturers_sort', null, $store->getId());
            
            // re-insert the value, so the default scope value will be inserted too
            $item->addAttributeUpdate('aitmanufacturers_sort', $sort, $store->getId());
        }
    }
}

// rebuid and refresh product flat tables

$_hlp = Mage::helper('catalog/product_flat');
/* @var $_hlp Mage_Catalog_Helper_Product_Flat */
if ($_hlp->isBuilt())
{
    $_flatIndexer = Mage::getSingleton('catalog/product_flat_indexer');
    /* @var $_flatIndexer Mage_Catalog_Model_Product_Flat_Indexer */
    $_flatIndexer->getResource()->rebuild();
}

// so the attribute is not a required any more
$installer->updateAttribute('catalog_product', 'aitmanufacturers_sort', 'is_required', 0);

$installer->endSetup();