<?php
/**
 * created : 1 oct. 2009
 * Override of order view block
 * @REG CLI-701, CLI-702
 * 
 * updated by <user> : <date>
 * Description of the update
 * 
 * @category SQLI
 * @package Sqli_Sales
 * @author ysanchez
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * Override of order view block
 * 
 * @package Sqli_Sales
 */
class Tatva_Sales_Block_Order_View extends Mage_Sales_Block_Order_View {
	const SALES_PDF_ORDER_CODE = 'O';
	const SALES_PDF_INVOICE_CODE = 'I';
	const SALES_PDF_SHIPMENT_CODE = 'S';
	const SALES_PDF_CREDITMEMO_CODE = 'C';
	
	/**
	 * Get all pdf files informations for this order and its attached documents
	 * @REG CLI-701
	 * @return array
	 */
	public function getPdfFiles() {

		$order = $this->getOrder();
         $pdfFiles = array();

        if(strtotime($order->getCreatedAt()) < strtotime(Mage::getStoreConfig('sales_email/order/lastdata')))
        {
           
          $read= Mage::getSingleton('core/resource')->getConnection('core_read');
          if($order->getIncrementId())
          {
            $row=array();
            $result_data='select * from tatva_order_pdf_file_new where increment_id='.$order->getIncrementId().' Limit 1';
            $row[]=$read->fetchAll($result_data);

            foreach($row[0] as $tmp)
            {
              //echo "<pre>"; print_r($tmp); exit;
                  $pdfFiles[($order->getCreatedAt())] = array('type'=>self::SALES_PDF_ORDER_CODE,'id'=>$order->getId(),'increment_id'=>$tmp['increment_id'],'file'=>$tmp['order_pdf_file']);

        		  $pdfFiles[(date('Y-m-d H:i:s', strtotime($order->getCreatedAt() . ' + 1 day')))] = array('type'=>self::SALES_PDF_INVOICE_CODE,'id'=>'','increment_id'=>$tmp['invoice_increment_id'],'file'=>$tmp['invoice_pdf_file']);


        		  $pdfFiles[(date('Y-m-d H:i:s', strtotime($order->getCreatedAt() . ' + 2 day')))] = array('type'=>self::SALES_PDF_SHIPMENT_CODE,'id'=>'','increment_id'=>$tmp['shipment_increment_id'],'file'=>$tmp['shipment_pdf_file']);


        		  $pdfFiles[(date('Y-m-d H:i:s', strtotime($order->getCreatedAt() . ' + 3 day')))] = array('type'=>self::SALES_PDF_CREDITMEMO_CODE,'id'=>'','increment_id'=>$tmp['credit_increment_id'],'file'=>$tmp['credit_pdf_file']);
           }
          }
        }
        else
        {
		$invoices = $order->getInvoiceCollection();
		$shipments = $order->getShipmentsCollection();
		$credits = $order->getCreditmemosCollection();
		$pdfFiles = array();

		$pdfFiles[($order->getCreatedAt())] = array('type'=>self::SALES_PDF_ORDER_CODE,'id'=>$order->getId(),'increment_id'=>$order->getIncrementId(),'file'=>$order->getPdfFile());
		foreach($invoices as $invoice) {
			$pdfFiles[($invoice->getCreatedAt())] = array('type'=>self::SALES_PDF_INVOICE_CODE,'id'=>$invoice->getId(),'increment_id'=>$invoice->getIncrementId(),'file'=>$invoice->getPdfFile());
		}
		foreach($shipments as $shipment) {
			$pdfFiles[($shipment->getCreatedAt())] = array('type'=>self::SALES_PDF_SHIPMENT_CODE,'id'=>$shipment->getId(),'increment_id'=>$shipment->getIncrementId(),'file'=>$shipment->getPdfFile());
		}
		foreach($credits as $credit) {
			$pdfFiles[($credit->getCreatedAt())] = array('type'=>self::SALES_PDF_CREDITMEMO_CODE,'id'=>$credit->getId(),'increment_id'=>$credit->getIncrementId(),'file'=>$credit->getPdfFile());
		}
		
         //$pdfFiles = $this->sortPdfFiles($pdfFiles);
		}
        //echo "<pre>"; print_r($pdfFiles); exit;
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
	 * Is file an order bill ?
	 * @param $pdfFile array
	 * @return bool
	 */
	public function isOrderPdfFile($pdfFile) {
		return $pdfFile['type'] == self::SALES_PDF_ORDER_CODE;
	}
	
	/**
	 * Is file an invoice ?
	 * @param $pdfFile array
	 * @return bool
	 */
	public function isInvoicePdfFile($pdfFile) {
		return $pdfFile['type'] == self::SALES_PDF_INVOICE_CODE;
	}
	
	/**
	 * Is file a shipment bill ?
	 * @param $pdfFile array
	 * @return bool
	 */
	public function isShipmentPdfFile($pdfFile) {
		return $pdfFile['type'] == self::SALES_PDF_SHIPMENT_CODE;
	}
	
	/**
	 * Is file a creditmemo bill ?
	 * @param $pdfFile array
	 * @return bool
	 */
	public function isCreditPdfFile($pdfFile) {
		return $pdfFile['type'] == self::SALES_PDF_CREDITMEMO_CODE;
	}
	
	/**
	 * Get file title for this file
	 * @param $pdfFile array
	 * @return string
	 */
	public function getFileTitle($pdfFile) {
		if (($this->isOrderPdfFile($pdfFile)) && ($pdfFile['increment_id']!='')) {
			return $this->__('Download order bill N°%s',$pdfFile['increment_id']);
		} elseif(($this->isInvoicePdfFile($pdfFile)) && ($pdfFile['increment_id']!='')) {
			return $this->__('Download invoice bill N°%s',$pdfFile['increment_id']);
		} elseif(($this->isShipmentPdfFile($pdfFile)) && ($pdfFile['increment_id']!='')) {
			return $this->__('Download shipment bill N°%s',$pdfFile['increment_id']);
		} elseif(($this->isCreditPdfFile($pdfFile)) && ($pdfFile['increment_id']!='')){
			return $this->__('Download credit bill N°%s',$pdfFile['increment_id']);
		} else {
			return "";
		}
	}
	
	/**
	 * Get file title for this unabled file
	 * @param $pdfFile array
	 * @return string
	 */
	public function getUnabledFileTitle($pdfFile) {
		if ($this->isOrderPdfFile($pdfFile)) {
			return $this->__('Order bill N°%s not available',$pdfFile['increment_id']);
		} elseif($this->isInvoicePdfFile($pdfFile)) {
			return $this->__('Invoice bill N°%s not available',$pdfFile['increment_id']);
		} elseif($this->isShipmentPdfFile($pdfFile)) {
			return $this->__('Shipment bill N°%s not available',$pdfFile['increment_id']);
		} elseif($this->isCreditPdfFile($pdfFile)) {
			return $this->__('Credit bill N°%s not available',$pdfFile['increment_id']);
		} else {
			return "";
		}
	}
	
	/**
	 * Get download file url
	 * @param $pdfFile array
	 * @return string
	 */
	public function getFileUrl($pdfFile,$pdfFileDate) {
        $time = new Zend_Date;
        
        if(strtotime($pdfFileDate) < strtotime(Mage::getStoreConfig('sales_email/order/lastdata')))
        {
           if ($this->isOrderPdfFile($pdfFile)) {
      			return $this->getUrl('tatvasales/download/orders',array('id'=>$pdfFile['increment_id']));
      		} elseif($this->isInvoicePdfFile($pdfFile)) {
      			return $this->getUrl('tatvasales/download/invoices',array('id'=>$pdfFile['increment_id']));
      		} elseif($this->isShipmentPdfFile($pdfFile)) { 
      			return $this->getUrl('tatvasales/download/shipments',array('id'=>$pdfFile['increment_id']));
      		} elseif($this->isCreditPdfFile($pdfFile)) {
      			return $this->getUrl('tatvasales/download/creditmemos',array('id'=>$pdfFile['increment_id']));
      		} else {
      			return "";
      		}
        }
        else
        {
           if ($this->isOrderPdfFile($pdfFile)) {
      			return $this->getUrl('tatvasales/download/order',array('id'=>$pdfFile['id']));
      		} elseif($this->isInvoicePdfFile($pdfFile)) {
      			return $this->getUrl('tatvasales/download/invoice',array('id'=>$pdfFile['id']));
      		} elseif($this->isShipmentPdfFile($pdfFile)) {
      			return $this->getUrl('tatvasales/download/shipment',array('id'=>$pdfFile['id']));
      		} elseif($this->isCreditPdfFile($pdfFile)) {
      			return $this->getUrl('tatvasales/download/creditmemo',array('id'=>$pdfFile['id']));
      		} else {
      			return "";
      		}
        }
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
		//$time->subYear(1);

		if(strtotime($pdfFileDate) < strtotime($time->toString('Y-M-d H:m:s'))) {
			if ($this->isOrderPdfFile($pdfFile)) {
				$pdfPath = Mage::getStoreConfig ( 'sales/pdf/path_order' ) . $pdfFile['file'];
			} elseif($this->isInvoicePdfFile($pdfFile)) {
				$pdfPath = Mage::getStoreConfig ( 'sales/pdf/path_invoice' ) . $pdfFile['file'];
			} elseif($this->isShipmentPdfFile($pdfFile)) {
				$pdfPath = Mage::getStoreConfig ( 'sales/pdf/path_shipment' ) . $pdfFile['file'];
			} elseif($this->isCreditPdfFile($pdfFile)) {
				$pdfPath = Mage::getStoreConfig ( 'sales/pdf/path_creditmemo' ) . $pdfFile['file'];
			} else {
				return false;
			}

            return $pdfPath;
		} else {
			return false;
		}
	}
	
	/**
	 * Retourne l'image d'un mode de transport
	 * @param string $carrierCode
	 * @return String
	 */
	public function getShippingPicture($carrierCode){
		Mage::log($carrierCode);
		$model = Mage::getStoreConfig('carriers/'.$carrierCode.'/model');
		$shippingMethod = Mage::getModel($model);
		if($shippingMethod){
			return $shippingMethod->getLittlePicture();
		}else{
			return "";
		}
	}
	
	public function getUrlTracking ($track){
		$carrierCode = $track->getCarrierCode();
		if($carrierCode == "colissimo" || $carrierCode == 'chronopost' || $carrierCode ='ups'){
			$carrierModel = Mage::getStoreConfig('carriers/'.$carrierCode.'/model');
			$model = Mage::getModel($carrierModel);
			return $model->getTrackingUrl($track->getNumber());
		}else{
			return $this->helper('shipping')->getTrackingPopUpUrlByTrackID($track->getEntityId());
		}
	}
}

?>