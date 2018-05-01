<?php
 
    require_once('app/Mage.php');
    Mage::app('default');
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    
    $setup          = new Mage_Eav_Model_Entity_Setup('core_setup');
    $entityTypeId   = $setup->getEntityTypeId('catalog_category');
    $attributeCode  = 'caticon';
 
    $setup->removeAttribute($entityTypeId, $attributeCode);
 
?>