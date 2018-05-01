<?php

class MDN_DropShipping_Block_Supplier_Widget_Grid_Column_Renderer_FileName
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    /**
     * provide a link to download the file
     * 
     * @param Varien_Object $row
     * @return type 
     */
    public function render(Varien_Object $row )
    {
        $html = '';
        
        $url = Mage::Helper('adminhtml')->getUrl('DropShipping/SupplierStockImport/DownloadImportedFile', array( 'dssl_id' => $row->getId()));
        $title = Mage::Helper('DropShipping')->__("Click here to download the imported file.");
        $html = '<span><a href="'.$url.'" title="'.$title.'" id="file_'.$row->getdssl_file_name().'" name="file_'.$row->getdssl_file_name().'">'.$this->__('Download file').'</span>';
           
        return $html;
    }
    
}


/*
 * 
 * array(6) { 
 *  ["dssl_id"]=> string(1) "6"
 *  ["dssl_supplier_id"]=> string(1) "1"
 *  ["dssl_supplier_date"]=> string(19) "2012-10-25 08:47:45"
 *  ["dssl_supplier_log"]=> string(102) "Unable to connect to ftp server, please check ftp logins in system > configuration > External logistic"
 *  ["dssl_duration"]=> string(4) "0.00"
 *  ["dssl_is_error"]=> string(1) "1"
 *  ["dssl_file_name"]=> string (9) "file9.csv"} 
 */