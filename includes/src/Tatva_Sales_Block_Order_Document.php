<?php
/**
 * created : 02 oct. 2009
 * 
 * @category SQLI
 * @package Sqli_Sales
 * @author emchaabelasri
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * Description of the class
 * @package Sqli_Sales
 */
class Tatva_Sales_Block_Order_Document  extends  Mage_Sales_Block_Order_View {
	
	/**
	 * return credit memo collection of the current order
	 * @param 
	 * @return array 
	 */
	public function creditmemoCollection(){
		return $this->getOrder()->getCreditmemosCollection()->addAttributeToSelect( 'pdf_file' );
	}
	/**
	 * Build url to download credit memo pdf file 
	 * @param $id int
	 * @return url string
	 */
	public function getDownloadCreditmemoUrl( $id ){
		return $this->getUrl('tatvasales/download/creditmemo',array( 'id'=>$id ));
	}

	/**
	 * return invoice collection of the current order
	 * @param 
	 * @return array 
	 */
	public function invoiceCollection(){
		return $this->getOrder()->getInvoiceCollection()->addAttributeToSelect( 'pdf_file' );
	}
	/**
	 * Build url to download invoice pdf file 
	 * @param $id int
	 * @return url string
	 */
	public function getDownloadInvoiceUrl( $id ){
		return $this->getUrl('tatvasales/download/invoice',array( 'id'=>$id ));
	}
	/**
	 * return order object
	 * @param 
	 * @return array 
	 */
	public function getOrderLoaded(){
		return Mage::getModel('sales/order')->load($this->getOrder()->getId());
	}
	/**
	 * Build url to download order pdf file 
	 * @param $id int
	 * @return url string
	 */
	public function getDownloadOrderUrl( $id ){
		return $this->getUrl('tatvasales/download/order',array( 'id'=>$id ));
	}			

	
	
	
}