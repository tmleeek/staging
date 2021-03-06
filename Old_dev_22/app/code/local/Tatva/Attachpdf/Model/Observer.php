<?php
/**
 * created : 01 sept. 2009
 * 
 * @category SQLI
 * @package Sqli_Sales
 * @author lbourrel
 * @copyright SQLI - 2009 - http://www.tatva.com
 */

/**
 * 
 * @package Sqli_Sales
 */
class Tatva_Attachpdf_Model_Observer {
        
       public function __contruct()
        {

        }

	public function getUrl($route = '', $params = array()) {
		return Mage::helper ( 'adminhtml' )->getUrl ( $route, $params );
	}
	
	public function checkConfig($observer) {
		    
		if (Mage::getSingleton('admin/session')->getUser()) {
		
			$defaultInvoiceDir = ( string ) Mage::getConfig ()->getNode ( 'default/sales/pdf/path_invoice' );
			$defaultOrderDir = ( string ) Mage::getConfig ()->getNode ( 'default/sales/pdf/path_order' );
			$defaultShipmentDir = ( string ) Mage::getConfig ()->getNode ( 'default/sales/pdf/path_shipment' );
			$defaultCreditmemoDir = ( string ) Mage::getConfig ()->getNode ( 'default/sales/pdf/path_creditmemo' );
			
			if (! $defaultInvoiceDir || ! $defaultOrderDir || ! $defaultShipmentDir || !$defaultCreditmemoDir ) {
				Mage::getSingleton ( 'adminhtml/session' )->addNotice ( 
						Mage::helper ( 'tatvasales' )->__ ( 'Veuillez vérifier la configuration des répertoires de stockage des fichiers PDF : <a href="%s">configuration</a>.', $this->getUrl ( 'adminhtml/system_config/edit', array (
								'section' => 'sales' ) ) ) );
			}
		
		}
	
	}
	
	public function checkNewInvoice($observer) {
		$invoice = $observer->getEvent ()->getInvoice ();
		if (! $invoice->getId ()) {
			$invoice->setCreatePdf ( true );
		}
	}
	
	public function checkNewShipment($observer) {
		$shipment = $observer->getEvent ()->getShipment ();
		if (! $shipment->getId ()) {
			$shipment->setCreatePdf ( true );
		}
	}
	
	
	public function checkNewOrder($observer) {    
		$order = $observer->getEvent ()->getOrder ();
		if (! $order->getId ()) {
			$order->setCreatePdf ( true );
		}
	}
	
	public function checkNewCreditmemo( $observer ){
		$creditmemo = $observer->getEvent ()->getCreditmemo ();
		if (! $creditmemo->getId ()) {
			$creditmemo->setCreatePdf ( true );
		}			
	}
	
	public function createNewInvoice($observer) {
		$invoice = $observer->getEvent ()->getInvoice ();
		
//		if (!$invoice->getOrder ()->getCustomerIsGuest ()) {
			if ($invoice->getCreatePdf ()) {
				// store
				$store = $invoice->getStore ();
				// Création du répertoire
				$root = Mage::getStoreConfig ( 'sales/pdf/path_invoice', $store->getId () );
				$subdir = $this->createDir ( $root, 'invoice' );
				$dir = $root . $subdir;
				if (! $subdir)
					return;
					// Création du PDF
				if ($invoice->getStoreId ()) {
					Mage::app ()->setCurrentStore ( $invoice->getStoreId () );
				}
				$filename = '';
				if ($invoice->getOrder ()->getCustomerIsGuest ()) {
					$filename = "Guest-O{$invoice->getOrder()->getIncrementId()}-I{$invoice->getIncrementId()}-Invoice.pdf";
				} else {
					$customer = Mage::getModel ( 'customer/customer' )->load ( $invoice->getOrder ()->getCustomerId () );
					$filename = "C{$customer->getIncrementId()}-O{$invoice->getOrder()->getIncrementId()}-I{$invoice->getIncrementId()}-Invoice.pdf";
				}
				$pdf = Mage::getModel ( 'attachpdf/sales_order_pdf_invoice' )->getPdf ( array (
						$invoice ) );
				$pdf->save ( "$dir/$filename" );
				// enregistrement du nom de fichier
				$invoice->setPdfFile ( "$subdir/$filename" );
				if($filename!='')
               {
                 $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                 if($invoice->getIncrementId())
                 {
                  $collection_add_sql="UPDATE `sales_flat_invoice` SET `pdf_file` = '".$subdir."/".$filename."' WHERE `increment_id` =".$invoice->getIncrementId();
                  $write->query($collection_add_sql);
                 }
               }
			}
//		}
	}
	
	public function createNewShipment($observer) {
		$shipment = $observer->getEvent ()->getShipment ();
//		if (!$shipment->getOrder ()->getCustomerIsGuest ()) {
			if ($shipment->getCreatePdf ()) {
				// store
				$store = $shipment->getStore ();
				// Création du répertoire
				$root = Mage::getStoreConfig ( 'sales/pdf/path_shipment', $store->getId () );
				$subdir = $this->createDir ( $root, 'shipment' );
				$dir = $root . $subdir;
				if (! $subdir)
					return;
					// Création du PDF
				if ($shipment->getStoreId ()) {
					Mage::app ()->setCurrentStore ( $shipment->getStoreId () );
				}
				$filename = '';
				if ($shipment->getOrder ()->getCustomerIsGuest ()) {
					$filename = "Guest-O{$shipment->getOrder()->getIncrementId()}-S{$shipment->getIncrementId()}-Shipment.pdf";
				} else {
					$customer = Mage::getModel ( 'customer/customer' )->load ( $shipment->getOrder ()->getCustomerId () );
					$filename = "C{$customer->getIncrementId()}-O{$shipment->getOrder()->getIncrementId()}-S{$shipment->getIncrementId()}-Shipment.pdf";
				}
				$pdf = Mage::getModel ( 'attachpdf/sales_order_pdf_shipment' )->getPdf ( array (
						$shipment ) );
				$pdf->save ( "$dir/$filename" );
				// enregistrement du nom de fichier

				$shipment->setPdfFile ( "$subdir/$filename" );
				if($filename!='')
               {
                 $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                 if($shipment->getIncrementId())
                 {
                  $collection_add_sql="UPDATE `sales_flat_shipment` SET `pdf_file` = '".$subdir."/".$filename."' WHERE `increment_id` =".$shipment->getIncrementId();
                  $write->query($collection_add_sql);
                 }
               }
//			}
		}
	}

	public function createNewOrder($observer) {
		$order = $observer->getEvent ()->getOrder ();
//		if (!$order->getCustomerIsGuest ()) {
			if ($order->getCreatePdf ()) {
				// store
				$store = $order->getStore ();
				// Création du répertoire
				$root = Mage::getStoreConfig ( 'sales/pdf/path_order', $store->getId () );
				$subdir = $this->createDir ( $root, 'order' );
				$dir = $root . $subdir;
				if (! $subdir)
					return;
					// Création du PDF
				if ($order->getStoreId ()) {
					Mage::app ()->setCurrentStore ( $order->getStoreId () );
				}
				$filename = '';
				if ($order->getCustomerIsGuest ()) {
					$filename = "Guest-O{$order->getIncrementId()}-Order.pdf";
				} else {
					$customer = Mage::getModel ( 'customer/customer' )->load ( $order->getCustomerId () );
					$filename = "C{$customer->getIncrementId()}-O{$order->getIncrementId()}-Order.pdf";
				}
                //echo "mauli"; exit;
				$pdf = Mage::getModel ('attachpdf/sales_order_pdf_order' )->getPdf ( array (
						$order ) );

				$pdf->save ( "$dir/$filename" );
				// enregistrement du nom de fichier echo
				$order->setPdfFile ( "$subdir/$filename" );
			   if($filename!='')
               {
                 $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                 if($order->getIncrementId())
                 {
                  $collection_add_sql="UPDATE `sales_flat_order` SET `pdf_file` = '".$subdir."/".$filename."' WHERE `increment_id` =".$order->getIncrementId();
                  $write->query($collection_add_sql);
                 }
               }
			}
//		}
		
	}
	
	public function createNewCreditmemo($observer) {
		$creditmemo = $observer->getEvent ()->getCreditmemo ();
		if ($creditmemo->getCreatePdf ()) {
			// store
			$store = $creditmemo->getStore ();
			// Création du répertoire
			$root = Mage::getStoreConfig ( 'sales/pdf/path_creditmemo', $store->getId () );
			$subdir = $this->createDir ( $root, 'creditmemo' );
			$dir = $root . $subdir;
			if (! $subdir)
				return;
				// Création du PDF
			if ($creditmemo->getStoreId ()) {
				Mage::app ()->setCurrentStore ( $creditmemo->getStoreId () );
			}
			$filename = '';
			if ($creditmemo->getOrder ()->getCustomerIsGuest ()) {
				$filename = "Guest-O{$creditmemo->getOrder()->getIncrementId()}-A{$creditmemo->getIncrementId()}-Creditmemo.pdf";
			} else {
				$customer = Mage::getModel ( 'customer/customer' )->load ( $creditmemo->getOrder ()->getCustomerId () );
				$filename = "C{$customer->getIncrementId()}-O{$creditmemo->getOrder()->getIncrementId()}-A{$creditmemo->getIncrementId()}-Creditmemo.pdf";
			}
			$pdf = Mage::getModel ( 'attachpdf/sales_order_pdf_creditmemo' )->getPdf ( array (
					$creditmemo ) );
			$pdf->save ( "$dir/$filename" );
			// enregistrement du nom de fichier
			$creditmemo->setPdfFile ( "$subdir/$filename" );
			   if($filename!='')
               {
                 $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                 if($creditmemo->getIncrementId())
                 {
                  $collection_add_sql="UPDATE `sales_flat_creditmemo` SET `pdf_file` = '".$subdir."/".$filename."' WHERE `increment_id` =".$creditmemo->getIncrementId();
                  $write->query($collection_add_sql);
                 }
               }
		}		
		
	}
	
	protected function createDir($root, $type) {
		if (! $root || ! is_dir ( $root )) {
			Mage::log ( "Le chemin de stockage \"$type\" est mal configuré", Zend_Log::ERR );
			return false;
		}
		$subdir = date ( "Y/m/d" );
		$dir = $root . $subdir;
		if (! is_dir ( $dir )) {
			if (! mkdir ( $dir, 0755, true )) {
				Mage::log ( "Erreur lors de la création du répertoire de stockage \"$type\"", Zend_Log::ERR );
				return false;
			}
		}
		return $subdir;
	}
}
    

