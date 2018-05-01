<?php

class MDN_DropShipping_TestController extends Mage_Adminhtml_Controller_Action {
    
    public function TestAction()
    { 
        $shipment = Mage::getModel('sales/order_shipment')->load(3851);
        $content = Mage::getModel('DropShipping/ExportType_SimpleXml')->getContent(array($shipment));
        die($content);
    }
    
}
