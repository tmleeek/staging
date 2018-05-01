<?php

class MDN_DropShipping_Block_Supplier_Widget_Grid_Column_Renderer_IsError
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    /**
     *  display is error status
     * 
     * @param Varien_Object $row
     * @return type 
     */
    public function render(Varien_Object $row )
    {
    	
        $html = '';

        // if error red alert
        if( $row->getdssl_is_error() == true){
            $html = '<span style="color : red;">'.$this->__("Yes").'</span>';
        } else {
            $html = '<span style="color : green;">'.$this->__("No").'</span>';
        }
        

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
 *  ["dssl_is_error"]=> string(1) "1" } 
 */