<?php
/**
 * created : 9 sept. 2009
 * 
 * @category SQLI
 * @package Sqli_Customer
 * @author emchaabelasri
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * Description of the class
 * @package Sqli_Customer
 */
class Tatva_Sales_Block_Order_History  extends  Mage_Sales_Block_Order_History {
	
	const SALES_PDF_INVOICE_CODE = 'I';
		
    protected function _prepareLayout(){
        parent::_prepareLayout();
    	$this->getLayout()
    	     ->getBlock('sales.order.history.pager')
    		 ->setTemplate('sales/order/html/pager.phtml');
        return $this;
    }
	
	/**
	 * Get all pdf files informations for this order and its attached documents
	 * @REG CLI-701
	 * @return array
	 */
	public function getPdfInvoiceFile($order) {
		$invoices = $order->getInvoiceCollection()->addAttributeToSelect( 'pdf_file' );
		$pdfFiles = array();
		
		foreach($invoices as $invoice) {
			$pdfFiles[($invoice->getCreatedAt())] = array('type'=>self::SALES_PDF_INVOICE_CODE,'id'=>$invoice->getId(),'increment_id'=>$invoice->getIncrementId(),'file'=>$invoice->getPdfFile());
		}
		$pdfFiles = $this->sortPdfFiles($pdfFiles);
		return $pdfFiles;
	}
	
	
	/**
	 * Sort pdf files by date (desc)
	 * @param $pdfFiles array
	 * @return array
	 */
	public function sortPdfFiles($pdfFiles) {
		krsort($pdfFiles);
		
		return $pdfFiles;
	}
	
	/**
	 * Check if file can be displaid or not
	 * @REG CLI-702
	 * @param $pdfFileDate datetime (sql format)
	 * @param $pdfFile array
	 * @return bool
	 */
	public function isDisplayable($pdfFileDate,$pdfFile) {
		$time = new Zend_Date;
		$time->subYear(1);
		if($pdfFileDate > $time->toString('Y-M-d H:m:s')) {
			$pdfPath = Mage::getStoreConfig ( 'sales/pdf/path_invoice' ) . $pdfFile['file'];
			return file_exists($pdfPath);
		} else {
			return false;
		}
	}
	
	/**
	 * Get download file url
	 * @param $pdfFile array
	 * @return string
	 */
	public function getFileUrl($pdfFile) {
		return $this->getUrl('tatvasales/download/invoice',array('id'=>$pdfFile['id']));
	}
	
}
