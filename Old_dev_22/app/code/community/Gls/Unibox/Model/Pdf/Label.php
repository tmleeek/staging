<?php
/**
 * Gls_Unibox extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Gls
 * @package    Gls_Unibox
 * @copyright  Copyright (c) 2013 webvisum GmbH
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Webvisum
 * @package    Gls_Unibox
 */
class Gls_Unibox_Model_Pdf_Label extends Gls_Unibox_Model_Pdf_Abstract{
	
	public function createLabel($tagdata){
		//Models Label/Gls/Express oder Label/Gls/Business holen für Daten
		foreach($tagdata as $item) {
			if ($item->getValue() != null) {
				if(get_class($item->getItem()) === 'Gls_Unibox_Model_Label_Item_Font'  ){
					$this->drawFont($item);
				}
				/*if(get_class($item->getItem()) === 'Gls_Unibox_Model_Label_Item_Barcode'  ){
					$this->drawBarcode($item);
				}*/
				if(get_class($item->getItem()) === 'Gls_Unibox_Model_Label_Item_Datamatrix'  ){
					$this->drawDatamatrix($item);
				}
			}
		}
		return $this->pdf;
	}


    public function getCustomer($order){
        return Mage::getModel('customer/customer')->load($order->getCustomerId());
    }
    
    
    public function insertFreeText(&$page, $pdf, $store = null, $text = null){
		if ( $text ) {
			foreach (explode("\n", $text ) as $value ) {
                foreach (Mage::helper('core/string')->str_split(strip_tags(ltrim($value)), 95, true) as $key => $part){
				    //$this->_setFontRegular($page);
			        $page->drawText(trim(strip_tags($part)), $this->margin_left+6, $this->y, $this->charset);

                }
			}
		}
    }
}