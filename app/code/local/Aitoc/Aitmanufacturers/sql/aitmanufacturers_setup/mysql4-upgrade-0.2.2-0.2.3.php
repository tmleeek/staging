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

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();


Mage::register('aitmanufacturers_fillout_inprogress', true);


// getting all brands for 'all store views'
$collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();
$collection->addStoreFilter(0, true)->load();

if (count($collection) > 0)
{
    $storesCollection = Mage::getModel('core/store')->getCollection()->load();
    foreach ($storesCollection as $store)
    {
        if (0 != $store->getId())
        {
            foreach ($collection as $brand)
            {
                $brandCollection_Check = Mage::getModel('aitmanufacturers/aitmanufacturers')
                                                  ->getCollection()
                                                  ->addStoreFilter($store, true)
                                                  ->addFieldToFilter('main_table.id', array('=' => $brand->getId()))
                                                  ->load();
                if (0 == count($brandCollection_Check))
                {
                    $model = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($brand->getId());
                    $model->setId(null);
                    $model->setData('store_id', null);
                    $model->setData('stores', array($store->getId()));
                    $model->setData('store_id', $store->getId());
                    try
                    {
                        $model->save();
                    } catch(Exception $e) { }
                    
                    // copying brand images
                    $path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS;
                    if ($model->getData('image') && file_exists($path . $model->getData('image')))
                    {
                        $ext = substr($model->getData('image'), strrpos($model->getData('image'), '.'));
                        @copy($path . $model->getData('image'), $path . $model->getId() . $ext);
                        $model->setData('image', $model->getId() . $ext);
                    }
                    $path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . 'logo' . DS;
                    if ($model->getData('small_logo') && file_exists($path . $model->getData('small_logo')))
                    {
                        $ext = substr($model->getData('small_logo'), strrpos($model->getData('small_logo'), '.'));
                        @copy($path . $model->getData('small_logo'), $path . $model->getId() . $ext);
                        $model->setData('small_logo', $model->getId() . $ext);
                    }
                    // saving images information
                    if ($brand->getData('image') || $brand->getData('small_logo'))
                    {
                        try
                        {
                            $model->save();
                        } catch(Exception $e) { }
                    }
                }
            }
        }
    }

    // deleting 'all store views' brands
    foreach ($collection as $brand)
    {
        $model = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($brand->getId());
        $model->delete();
    }
}

// loading all brands and saving again in order to generate url rewrites
$collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection()->load();
if (count($collection) > 0)
{
    foreach ($collection as $brand)
    {
        $model = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($brand->getId());
        $model->save();
    }
}


Mage::unregister('aitmanufacturers_fillout_inprogress');


$installer->endSetup();