<?php

class MDN_DropShipping_Block_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {

        parent::__construct();
        $this->setId('dropshipping_tab');
        $this->setDestElementId('dropshipping_tab_content');
        $this->setTemplate('widget/tabshoriz.phtml');
        
    }
    
    protected function _beforeToHtml(){

        $block = $this->getLayout()->createBlock('DropShipping/Tabs_DropShippable');
        $content = $block->toHtml();
        $caption = $this->__('This tab contains pending orders you can process');
        $this->addTab('drop_shippable', array(
            'label' => Mage::helper('DropShipping')->__('Drop Shippable - %s', '<span id="dropshipping_tab_drop_shippable_count"></span>'),
            'title' => ' - ',
            'content' => '<center><i>'.$caption.'</i></center><br>'.$content
        ));
        
        if (Mage::getStoreConfig('dropshipping/tabs/display_pending_price_response'))
        {
            $caption = $this->__('Here are the purchase orders for which you are pending a quote from supplier');
            $block = $this->getLayout()->createBlock('DropShipping/Tabs_PendingPriceResponse');
            $content = $block->toHtml();
            $this->addTab('pending_price_response', array(
                'label' => Mage::helper('DropShipping')->__('Pending Price Response - %s', '<span id="dropshipping_tab_pending_price_response_count"></span>'),
                'title' => ' - ',
                'content' => '<center><i>'.$caption.'</i></center><br>'.$content
            ));
        }

        if (Mage::getStoreConfig('dropshipping/tabs/display_pending_supplier_response'))
        {
            $caption = $this->__('Here are the purchase orders for which you made a dropship request');
            $block = $this->getLayout()->createBlock('DropShipping/Tabs_PendingSupplierResponse');
            $content = $block->toHtml();
            $this->addTab('pending_supplier_response', array(
                'label' => Mage::helper('DropShipping')->__('Pending Supplier Response - %s', '<span id="dropshipping_tab_pending_supplier_response_count"></span>'),
                'content' => '<center><i>'.$caption.'</i></center><br>'.$content,
                'title' => ' - ',
            ));
        }
        
        if (Mage::getStoreConfig('dropshipping/tabs/display_pending_supplier_delivery'))
        {        
            $caption = $this->__('Here are the purchase orders for which you are pending a delivery confirmation from supplier (%s)', $block->getCollection()->getSize());
            $block = $this->getLayout()->createBlock('DropShipping/Tabs_PendingSupplierDelivery');
            $content = $block->toHtml();
            $this->addTab('pending_supplier_delivery', array(
                'label' => Mage::helper('DropShipping')->__('Pending Shipping Confirmation - %s', '<span id="dropshipping_tab_pending_supplier_delivery_count"></span>'),
                'content' => '<center><i>'.$caption.'</i></center><br>'.$content,
                'title' => ' - ',
            ));
        }
                
        /**
        if (Mage::getStoreConfig('dropshipping/tabs/display_pending_tracking_number'))
        {
            $caption = $this->__('Here are the purchase orders for which you are pending the tracking number (%s)', $block->getCollection()->getSize());
            $block = $this->getLayout()->createBlock('DropShipping/Tabs_PendingTracking');
            $content = $block->toHtml();
            $this->addTab('pending_tracking', array(
                'label' => Mage::helper('DropShipping')->__('Pending tracking number (%s)', $block->getCollection()->getSize()),
                'content' => '<center><i>'.$caption.'</i></center><br>'.$content
            ));
        }
         * 
         */

        if (Mage::getStoreConfig('dropshipping/tabs/display_dropshipped_history'))
        {
           $caption = $this->__('Logs for drop shipped orders');
           $content = $this->getLayout()->createBlock('DropShipping/Tabs_DropshippedHistory')->toHtml();
            $this->addTab('dropshipped_history', array(
                'label' => Mage::helper('DropShipping')->__('Dropshipped History'),
                'content' => '<center><i>'.$caption.'</i></center><br>'.$content,
                'title' => ' - ',
            ));
        }
        
        return parent::_beforeToHtml();
    }

}