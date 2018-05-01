<?php

/* **************************************************************************************************************************
* Tabs: add a tab for ftp supplier account, overload the purchase tab.php
*************************************************************************************************************************** */
class MDN_DropShipping_Block_Supplier_Edit_Tabs extends MDN_Purchase_Block_Supplier_Edit_Tabs {

    /**
     * create new tab
     * @return type 
     */
    public function _beforeToHtml(){
        
        parent::_beforeToHtml(); // to get the others tabs before the news.
        
        $this->addTab('tab_dropship', array(
            'label'     => Mage::helper('DropShipping')->__('Drop Shipping'),
            'content'   => $this->getLayout()
                                ->createBlock('DropShipping/Supplier_Edit_Tabs_DropShipping')
                                ->toHtml(),
        ));
        
        $this->addTab('tab_stock_price_import', array(
            'label'     => Mage::helper('DropShipping')->__('Stock / Price import'),
            'content'   => $this->getLayout()
                                ->createBlock('DropShipping/Supplier_Edit_Tabs_StockPriceImport')
                                ->toHtml(),
        ));
       
        
        $this->addTab('tab_log', array(
            'label'     => Mage::helper('DropShipping')->__('Stock file import Log'),
            'content'   => $this->getLayout()
                                ->createBlock('DropShipping/Supplier_Edit_Tabs_Log')
                                ->toHtml(),
        ));
        
        return parent::_beforeToHtml();
        
    }
     
}

