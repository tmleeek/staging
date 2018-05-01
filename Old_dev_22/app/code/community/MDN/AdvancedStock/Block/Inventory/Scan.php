<?php

class MDN_AdvancedStock_Block_Inventory_Scan extends Mage_Core_Block_Template {
    
    
    /**
     * Return Submit scanned products url
     */
    public function getSubmitUrl()
    {
        return Mage::helper('adminhtml')->getUrl('AdvancedStock/Inventory/SaveScan');
    }
    
    /**
     * Return current inventory
     */
    public function getInventory()
    {
        return Mage::registry('current_inventory');
    }
    
    public function getUrlProducts()
    {
        return Mage::helper('adminhtml')->getUrl('AdvancedStock/Inventory/LocationInformation');
    }
    
    public function getUrlResetLocation()
    {
        return Mage::helper('adminhtml')->getUrl('AdvancedStock/Inventory/ResetLocation');
    }
    
    public function getUnknownBarcodeUrl()
    {
        return Mage::helper('adminhtml')->getUrl('AdvancedStock/Inventory/UnknownBarcode');
    }
    
    public function getBackUrl()
    {
        return Mage::helper('adminhtml')->getUrl('AdvancedStock/Inventory/Edit', array('ei_id' => $this->getInventory()->getId()));
    }
    
}
