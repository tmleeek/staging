<?php


class Tatva_Sales_DownloadController extends Mage_Core_Controller_Front_Action
{

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

  	public function invoiceAction() {
		try {
			$invoice = $this->_initObject ( 'sales/order_invoice' );
			if (! $invoice->getId () || $invoice->getEntityTypeId()!=Mage::getSingleton('eav/config')->getEntityType('invoice')->getId()) {
				return $this->_redirect ( 'customer/account' );
			}
			// check user
			$ownerId = $invoice->getOrder()->getCustomerId ();
			$customerId = Mage::getSingleton ( 'customer/session' )->getCustomerId ();
			if ($ownerId != $customerId) {
				Mage::log ( "Accounting element asked by bad owner (Invoice / id={$invoice->getId ()} / owner $ownerId");
				return $this->_redirect ( 'customer/account' );
			}
			// download
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

			$pdfPath = Mage::getStoreConfig ( 'sales/pdf/path_shipment' ) . $shipment->getPdfFile ();
			$this->_sendFile($pdfPath);
		} catch (Exception $e) {
			$this->_forward('noRoute');
        }
        return $this;
	}
}