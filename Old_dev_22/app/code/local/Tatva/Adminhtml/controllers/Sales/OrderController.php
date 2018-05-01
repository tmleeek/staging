<?php

require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';
class Tatva_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{
	 public function deleteorderAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
    	if (Mage::getStoreConfig(Asperience_Deleteallorders_Model_Order::XML_PATH_SALES_IS_ACTIVE)) {
	        $countDeleteOrder = 0;
	        $countDeleteOrderGrid = 0;
	        $countDeleteOrderGridException = 0;
	        $countDeleteInvoice = 0;
	        $countDeleteInvoiceGrid = 0;
	        $countDeleteInvoiceGridException = 0;
	        $countDeleteShipment = 0;
	        $countDeleteShipmentGrid = 0;
	        $countDeleteShipmentGridException = 0;
	        $countDeleteCreditmemo = 0;
	        $countDeleteCreditmemoGrid = 0;
	        $countDeleteCreditmemoGridException = 0;
			$orders_delete = array();
			$invoices_delete = array();
			$creditmemos_delete = array();
			$shipments_delete = array();
			$orders_undelete = array();
	        try {
				Mage::log($orderIds);
		        foreach ($orderIds as $orderId) {
		        	$order = Mage::getModel('deleteallorders/order')->load($orderId);
		            /*if ($order->canDelete()) {*/
		            
			            if ($order->hasInvoices()) {
			            	$invoices = Mage::getResourceModel('sales/order_invoice_collection')->setOrderFilter($orderId)->load();
			            	foreach($invoices as $invoice){
			            		$id = $invoice->getId();
			            		$invoice = Mage::getModel('sales/order_invoice')->load($id);
								$invoices_delete[] = $invoice->getIncrementId();
			            		$invoice->delete();
			            		$countDeleteInvoice++;
								/*try {
									Mage::log(get_class(Mage::getModel('sales/order_invoice_grid_collection')));
									$invoice = Mage::getModel('sales/order_invoice_grid')->load($id);
									$invoice->delete();
									$countDeleteInvoiceGrid++;
								} catch (Exception $e){
									Mage::log($e->getMessage());
									$countDeleteInvoiceGridException++;
			            		}*/
			            	}
			            }
			            
			        	if ($order->hasShipments()) {
			            	$shipments = Mage::getResourceModel('sales/order_shipment_collection')->setOrderFilter($orderId)->load();
			            	foreach($shipments as $shipment){
			            		$id = $shipment->getId();
			            		$shipment = Mage::getModel('sales/order_shipment')->load($id);
								$shipments_delete[] = $shipment->getIncrementId();
			            		$shipment->delete();
			            		$countDeleteShipment++;
								/*try {
									$shipment = Mage::getModel('sales/order_shipment_grid')->load($id);
									$shipment->delete();
									$countDeleteShipmentGrid++;
								} catch (Exception $e){
									Mage::log($e->getMessage());
									$countDeleteShipmentGridException++;
			            		}*/
			            	}
			            }
			            
			        	if ($order->hasCreditmemos()) {
			            	$creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')->setOrderFilter($orderId)->load();
			            	foreach($creditmemos as $creditmemo){
			            		$id = $creditmemo->getId();
			            		$creditmemo = Mage::getModel('sales/order_creditmemo')->load($id);
								$creditmemos_delete[] = $creditmemo->getIncrementId();
			            		$creditmemo->delete();
			            		$countDeleteCreditmemo++;
								/*try {
									$creditmemo = Mage::getModel('sales/order_creditmemo_grid')->load($id);
									$creditmemo->delete();
									$countDeleteCreditmemoGrid++;
								} catch (Exception $e){
									Mage::log($e->getMessage());
									$countDeleteCreditmemoGridException++;
			            		}*/
			            	}
			            }
			            $order = Mage::getModel('sales/order')->load($orderId);
						$orders_delete[] = $order->getIncrementId();
			            $order->delete();
			            $countDeleteOrder++;
						/*try {
							$order = Mage::getModel('sales/order_grid')->load($orderId);
							$order->delete();
							$countDeleteOrderGrid++;
						} catch (Exception $e){
							Mage::log($e->getMessage());
							$countDeleteOrderGridException++;
						}*/
			        /*} else {
						$orders_undelete[] = Mage::getModel('sales/order')->load($orderId)->getIncrementId();
					}*/
		        }
		        
		        if ($countDeleteOrder > 0) {
		            $this->_getSession()->addSuccess($this->__('%s sale(s) order(s) was/were successfully deleted.', $countDeleteOrder));
		            $this->_getSession()->addSuccess(implode(" ",$orders_delete));
			        /*if ($countDeleteOrderGrid > 0) {
			            $this->_getSession()->addSuccess($this->__('%s sale(s) order(s) was/were successfully deleted in grid.', $countDeleteOrderGrid));
			        }*/
			        if ($countDeleteInvoice > 0) {
			            $this->_getSession()->addSuccess($this->__('%s invoice(s) order(s) was/were successfully deleted.', $countDeleteInvoice));
						$this->_getSession()->addSuccess(implode(" ",$invoices_delete));
			        }
			        /*if ($countDeleteInvoiceGrid > 0) {
			            $this->_getSession()->addSuccess($this->__('%s invoice(s) order(s) was/were successfully deleted in grid.', $countDeleteInvoiceGrid));
			        }*/
			        if ($countDeleteShipment > 0) {
			            $this->_getSession()->addSuccess($this->__('%s shipment(s) order(s) was/were successfully deleted.', $countDeleteShipment));
						$this->_getSession()->addSuccess(implode(" ",$shipments_delete));
			        }
			        /*if ($countDeleteShipmentGrid > 0) {
			            $this->_getSession()->addSuccess($this->__('%s shipment(s) order(s) was/were successfully deleted in grid.', $countDeleteShipmentGrid));
			        }*/
		        	if ($countDeleteCreditmemo > 0) {
			            $this->_getSession()->addSuccess($this->__('%s credit memo(s) order(s) was/were successfully deleted.', $countDeleteCreditmemo));
						$this->_getSession()->addSuccess(implode(" ",$creditmemos_delete));
			        }
		        	/*if ($countDeleteCreditmemoGrid > 0) {
			            $this->_getSession()->addSuccess($this->__('%s credit memo(s) order(s) was/were successfully deleted in grid.', $countDeleteCreditmemoGrid));
			        }*/
					Mage::log($orders_undelete);
			        if(count($orders_undelete) > 0) {
						$this->_getSession()->addWarning($this->__('Selected order(s) can not be deleted:').implode(" ",$orders_undelete));
					}

		        } else {
		            $this->_getSession()->addError($this->__('Selected order(s) can not be deleted.'));
		        }
	        } catch (Exception $e){
	        	$this->_getSession()->addError($this->__('An error arose during the deletion. %s', $e));
	        }
        } else {
        	$this->_getSession()->addError($this->__('This feature was deactivated.'));
        }
	$this->_redirect('adminhtml/sales_order/', array());	
    }
	
	
	
	
		public function pdfordersAction()
   { 
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds))
		{
            foreach ($orderIds as $orderId)
			{
            	$order = Mage::getModel('sales/order')->load($orderId);

                $orders = Mage::getResourceModel('sales/order_collection')
                    ->addAttributeToSelect('*')
					->addAttributeToFilter('entity_id', $orderId)
					->load();


				if ($orders->getSize() > 0) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('attachpdf/sales_order_pdf_order')->getPdf($orders);
                    } else {
                        $pages = Mage::getModel('attachpdf/sales_order_pdf_order')->getPdf($orders);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse('order'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders'));
                $this->_redirect('*/*/');
            }

        }
        $this->_redirect('*/*/');

    }


     public function undoOrderAction() {

        $orderIds = $this->getRequest()->getPost('order_ids', array());

        $countCancelOrder = 0;
        $countNonCancelOrder = 0;
        foreach ($orderIds as $orderId) {
               $order = Mage::getModel('sales/order')->load($orderId);
               $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
               $order->setStatus('pending');

              $order->setBaseDiscountCanceled(0);
              $order->setBaseShippingCanceled(0);
              $order->setBaseSubtotalCanceled(0);
              $order->setBaseTaxCanceled(0);
              $order->setBaseTotalCanceled(0);
              $order->setDiscountCanceled(0);
              $order->setShippingCanceled(0);
              $order->setSubtotalCanceled(0);
              $order->setTaxCanceled(0);
              $order->setTotalCanceled(0);

              foreach($order->getAllItems() as $item){
                  $item->setQtyCanceled(0);
                  $item->setTaxCanceled(0);
                  $item->setHiddenTaxCanceled(0);
                  $item->save();
              }
               $countCancelOrder++;
               $order->save();
            }

        if ($countNonCancelOrder) {
            if ($countCancelOrder) {
                $this->_getSession()->addError($this->__('%s order(s) could not be updated', $countNonCancelOrder));
                //$this->_redirect('adminhtml/sales_order/');
            } else {
                $this->_getSession()->addError($this->__('Order(s) can not be updated'));
                // $this->_redirect('adminhtml/sales_order/');
            }
        }
        if ($countCancelOrder) {
            $this->_getSession()->addSuccess($this->__('%s order(s) successfully updated', $countCancelOrder));
            //$this->_redirect('adminhtml/sales_order/');
        }
         $this->_redirect('adminhtml/sales_order/');   
    }
}


?>