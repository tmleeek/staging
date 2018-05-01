<?php
/**
 * created : 3 sept. 2009
 * 
 * @category SQLI
 * @package Sqli_Sales
 * @author lbourrel
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * 
 * @package Sqli_Sales
 */
class Tatva_Sales_DownloadController extends Mage_Core_Controller_Front_Action {

	public function preDispatch() {
		parent::preDispatch ();
		$loginUrl = Mage::helper ( 'customer' )->getLoginUrl ();
		if (! Mage::getSingleton ( 'customer/session' )->authenticate ( $this, $loginUrl )) {
			$this->setFlag ( '', self::FLAG_NO_DISPATCH, true );
		}
	}
	
	protected function _initObject($modelClass) {
		$id = ( int ) $this->getRequest ()->getParam ( 'id' );
		$object = Mage::getModel ( $modelClass )->load ( $id );
		return $object;
	}
	
	protected function _sendFile($pdfPath) {
		if (! is_file ( $pdfPath ) || ! is_readable ( $pdfPath )) {
			throw new Exception ( );
		}
		$this->getResponse ()
					->setHttpResponseCode ( 200 )
					->setHeader ( 'Pragma', 'public', true )
					->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true )
					->setHeader ( 'Content-type', 'application/pdf', true )
					->setHeader ( 'Content-Length', filesize($pdfPath) )
					->setHeader ('Content-Disposition', 'inline' . '; filename=' . basename($pdfPath) );
		$this->getResponse ()->clearBody ();
		$this->getResponse ()->sendHeaders ();
		readfile ( $pdfPath );
		exit(0);
	}
	
	public function orderAction() {
		try {
			$order = $this->_initObject ( 'sales/order' );
            //echo "mauli";exit;

			// download
			if (!$order->getPdfFile ()) {
				throw new Exception ( );
			}
		    $pdfPath = Mage::getStoreConfig ( 'sales/pdf/path_order' ) . $order->getPdfFile ();
			$this->_sendFile($pdfPath);

		} catch (Exception $e) {
			$this->_forward('noRoute');
        }
        return $this;
	}



    public function ordersAction() {
		try {
            $ids=''; $data='';
		    $id=$this->getRequest()->getparams();
            if($id)
            {
               $ids=$id['id'];
            }

            $read= Mage::getSingleton('core/resource')->getConnection('core_read');
            $result_data='select order_pdf_file from tatva_order_pdf_file_new where increment_id='.$ids.' Limit 1';
            $data=$read->FetchOne($result_data);

			if ($data=='') {
				throw new Exception ( );
			}

			$pdfPath = Mage::getStoreConfig ( 'sales/pdf/path_order' ) . $data;
			$this->_sendFile($pdfPath);

		} catch (Exception $e) {
			$this->_forward('noRoute');
        }
        return $this;
	}


    public function invoicesAction() {
		try {
            $ids=''; $data='';
		    $id=$this->getRequest()->getparams();
            if($id)
            {
               $ids=$id['id'];
            }

            $read= Mage::getSingleton('core/resource')->getConnection('core_read');
            $result_data='select invoice_pdf_file from tatva_order_pdf_file_new where invoice_increment_id='.$ids.' Limit 1';
            $data=$read->FetchOne($result_data);

			if ($data=='') {
				throw new Exception ( );
			}

			$pdfPath = Mage::getStoreConfig ( 'sales/pdf/path_invoice' ) . $data;
			$this->_sendFile($pdfPath);

		} catch (Exception $e) {
			$this->_forward('noRoute');
        }
        return $this;
	}



    public function shipmentsAction() {
		try {
            $ids=''; $data='';
		    $id=$this->getRequest()->getparams();
            if($id)
            {
               $ids=$id['id'];
            }

            $read= Mage::getSingleton('core/resource')->getConnection('core_read');
            $result_data='select shipment_pdf_file from tatva_order_pdf_file_new where shipment_increment_id='.$ids.' Limit 1';
            $data=$read->FetchOne($result_data);

			if ($data=='') {
				throw new Exception ( );
			}

			$pdfPath = Mage::getStoreConfig ( 'sales/pdf/path_shipment' ) . $data;
			$this->_sendFile($pdfPath);

		} catch (Exception $e) {
			$this->_forward('noRoute');
        }
        return $this;
	}

     public function creditmemosAction() {
		try {
            $ids=''; $data='';
		    $id=$this->getRequest()->getparams();
            if($id)
            {
               $ids=$id['id'];
            }

            $read= Mage::getSingleton('core/resource')->getConnection('core_read');
            $result_data='select credit_pdf_file from tatva_order_pdf_file_new where credit_increment_id='.$ids.' Limit 1';
            $data=$read->FetchOne($result_data);

			if ($data=='') {
				throw new Exception ( );
			}

			$pdfPath = Mage::getStoreConfig ( 'sales/pdf/path_creditmemo' ) . $data;
			$this->_sendFile($pdfPath);

		} catch (Exception $e) {
			$this->_forward('noRoute');
        }
        return $this;
	}


    public function invoiceAction() {
		try {
			$invoice = $this->_initObject ( 'sales/order_invoice' );


			if (!$invoice->getPdfFile ()) {
				throw new Exception ( );
			}
			$pdfPath = Mage::getStoreConfig ( 'sales/pdf/path_invoice' ) . $invoice->getPdfFile ();
			$this->_sendFile($pdfPath);
		} catch (Exception $e) {
			$this->_forward('noRoute');
        }
        return $this;
	}

	public function shipmentAction() {
		try {
			$shipment = $this->_initObject ( 'sales/order_shipment' );
			/*if (! $shipment->getId () || $shipment->getEntityTypeId()!=Mage::getSingleton('eav/config')->getEntityType('shipment')->getId()) {
				return $this->_redirect ( 'customer/account' );
			}

			$ownerId = $shipment->getOrder()->getCustomerId ();
			$customerId = Mage::getSingleton ( 'customer/session' )->getCustomerId ();*/

			// download
			if (!$shipment->getPdfFile ()) {
				throw new Exception ( );
			}
			$pdfPath = Mage::getStoreConfig ( 'sales/pdf/path_shipment' ) . $shipment->getPdfFile ();
			$this->_sendFile($pdfPath);
		} catch (Exception $e) {
			$this->_forward('noRoute');
        }
        return $this;
	}
	public function creditmemoAction() {
		try {
			$creditmemo = $this->_initObject ( 'sales/order_creditmemo' );
			if (! $creditmemo->getId () || $creditmemo->getEntityTypeId()!=Mage::getSingleton('eav/config')->getEntityType('creditmemo')->getId()) {
				return $this->_redirect ( 'customer/account' );
			}
			// check user
			$ownerId = $creditmemo->getOrder()->getCustomerId ();
			$customerId = Mage::getSingleton ( 'customer/session' )->getCustomerId ();

			// download
			if (!$creditmemo->getPdfFile ()) {
				throw new Exception ( );
			}
			$pdfPath = Mage::getStoreConfig ( 'sales/pdf/path_creditmemo' ) . $creditmemo->getPdfFile();
			$this->_sendFile($pdfPath);
		} catch (Exception $e) {
			$this->_forward('noRoute');
        }		
        return $this;
	}	
	

}

