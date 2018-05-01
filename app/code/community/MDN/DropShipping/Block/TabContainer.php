<?php

class MDN_DropShipping_Block_TabContainer extends Mage_Core_Block_Template {

    /**
     * get cron action url
     * 
     * @return string 
     */
    public function getCronUrl() {
        return Mage::Helper('adminhtml')->getUrl('DropShipping/Cron/runDropShip');
    }

    /**
     * 
     * @return type
     */
    public function getCancelDropShipUrl() {
        return Mage::Helper('adminhtml')->getUrl('DropShipping/Admin/cancelDropShip');
    }

    /**
     * 
     * @return type
     */
    public function getConfirmDropShipRequestUrl() {
        return Mage::Helper('adminhtml')->getUrl('DropShipping/Admin/confirmDropShipRequest');
    }
    
    public function getConfirmDropShipShippingUrl()
    {
        return Mage::Helper('adminhtml')->getUrl('DropShipping/Admin/confirmDropShipShipping');
    }
    
    public function getApplyDropShippableActionUrl()
    {
        return Mage::Helper('adminhtml')->getUrl('DropShipping/Admin/applyDropShippable');
    }
    
    public function getApplyPendingPriceResponseActionUrl()
    {
        return Mage::Helper('adminhtml')->getUrl('DropShipping/Admin/applyPendingPriceResponse');
    }

    /**
     * 
     * @return type
     */
    public function getSuppliers() {
        return $suppliers = Mage::getModel("Purchase/Supplier")->getCollection();
    }

}