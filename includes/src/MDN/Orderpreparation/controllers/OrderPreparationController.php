<?php

class MDN_Orderpreparation_OrderPreparationController extends Mage_Adminhtml_Controller_Action {

    /**
     * Main screen (Step #1)
     *
     */
    public function indexAction() {
        $this->loadLayout();

        $this->_setActiveMenu('erp');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Order Preparation'));

        $block = $this->getLayout()->createBlock('Orderpreparation/OrderPreparationContainer');
        $this
                ->_addContent($this->getLayout()->createBlock('Orderpreparation/Header'))
                ->_addContent($this->getLayout()->createBlock('Orderpreparation/Widget_Tab_OrderPreparationTab'))
                ->renderLayout();
    }
    
    public function ShipmentAndInvoicesCreatedAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('erp');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Order Preparation'));

        $block = $this->getLayout()->createBlock('Orderpreparation/OrderPreparationContainer');
        $this
                ->_addContent($this->getLayout()->createBlock('Orderpreparation/Header'))
                ->_addContent($this->getLayout()->createBlock('Orderpreparation/Widget_Tab_OrderPreparationTab'))
                ->renderLayout();
        
    }

    /**
     * 
     *
     */
    public function editAction() {
        $this->loadLayout();
        $orderId = $this->getRequest()->getParam('order_id');
        $order = mage::getModel('sales/order')->load($orderId);
        $this->getLayout()->getBlock('ordercontentgrid')->setOrder($order);
        $this->getLayout()->getBlock('progressgraph')->setOrder($order);
        $this->renderLayout();
    }

    /**
     * Add several orders to selected orders
     *
     */
    public function massAddToSelectionAction() {

        //create task group
        $taskGroup = 'mass_add_to_selected_orders';
        mage::helper('BackgroundTask')->AddGroup($taskGroup, $this->__('Add orders to selected orders'), 'OrderPreparation/OrderPreparation/');

        //Create task to add orders
        $orderIds = $this->getRequest()->getPost('full_stock_orders_order_ids');
        if (!empty($orderIds)) {
            //Create task to add orders
            foreach ($orderIds as $orderId) {
                mage::helper('BackgroundTask')->AddTask('Add order #' . $orderId . ' to selected orders', 'Orderpreparation', 'addToSelectedOrders', $orderId, $taskGroup
                );
            }

            //execute task group
            mage::helper('BackgroundTask')->ExecuteTaskGroup($taskGroup);
        } else {
            Mage::getSingleton('adminhtml/session')->addError($this->__('No order to add'));
            $this->_redirect('OrderPreparation/OrderPreparation/');
        }
    }

    /**
     * Add one order to selected orders
     *
     */
    public function AddToSelectionAction() {
        $orderId = $this->getRequest()->getParam('order_id');
        if (Mage::getModel('Orderpreparation/ordertoprepare')->AddSelectedOrder($orderId))
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Order successfully added.'));
        else
            Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to add order'));

        //redirige sur la page de s�lection des commandes
        $this->_redirect('OrderPreparation/OrderPreparation/');
    }

    /**
     * remove several orders to selected orders
     *
     */
    public function massRemoveFromSelectionAction() {
        $orderIds = $this->getRequest()->getPost('order_ids');
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                Mage::getModel('Orderpreparation/ordertoprepare')->RemoveSelectedOrder($orderId);
            }
        }

        //confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Orders successfully removed.'));
        $this->RefreshListAction();
    }

    /**
     * Remove one order from selected orders
     *
     */
    public function RemoveFromSelectionAction() {
        $orderId = $this->getRequest()->getParam('order_id');
        Mage::getModel('Orderpreparation/ordertoprepare')->RemoveSelectedOrder($orderId);

        //confirme & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Order successfully removed.'));
        $this->_redirect('OrderPreparation/OrderPreparation/');
    }

    /**
     * Return a PDF with all invoices / packing slip for current user / warehouse
     *
     */
    public function DownloadDocumentsAction() {

        $pdf = mage::helper('Orderpreparation/Documents')->generateDocumentsPdf();
        $this->_prepareDownloadResponse('documents.pdf', $pdf->render(), 'application/pdf');
    }

    /**
     * Merge invoices & shipments in one PDF and send it to printer using Magenti Client Computer
     *
     */
    public function PrintDocumentsAction() {
        try {
            $pdf = mage::helper('Orderpreparation/Documents')->generateDocumentsPdf();
            $fileName = 'documents.pdf';
            mage::helper('ClientComputer')->printDocument($pdf->render(), $fileName, 'Order preparation : print documents');
        } catch (Exception $ex) {
            die("Erreur lors de la g�n�ration du PDF de facture: " . $ex->getMessage() . '<p>' . $ex->getTraceAsString());
        }
    }

    /**
     * Create invoices & shipments for selected orders
     *
     */
    public function CommitAction() {

        //Create task group
        $taskGroup = 'create_shipments_and_invoices';
        mage::helper('BackgroundTask')->AddGroup($taskGroup, $this->__('Create shipments and invoices'), 'OrderPreparation/OrderPreparation/ShipmentAndInvoicesCreated');

        //Browse selected orders and create tasks
        $OrdersToPrepare = Mage::getModel('Orderpreparation/ordertoprepare')->getSelectedOrders();
        foreach ($OrdersToPrepare as $OrderToPrepare) {
            //Create task for current selected order
            mage::helper('BackgroundTask')->AddTask('Create shipment & invoice for order #' . $OrderToPrepare->getId(), 'Orderpreparation', 'createShipmentAndInvoices', $OrderToPrepare->getId(), $taskGroup
            );
        }

        //Execute task group
        mage::helper('BackgroundTask')->ExecuteTaskGroup($taskGroup);
    }

    /**
     * 
     *
     * @param unknown_type $fileName
     * @param unknown_type $content
     * @param unknown_type $contentType
     */
    protected function _prepareDownloadResponse($fileName, $content, $contentType = 'application/octet-stream', $contentLength = null) {
        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', strlen($content))
                ->setHeader('Content-Disposition', 'attachment; filename=' . $fileName)
                ->setBody($content);
    }

    /**
     * Export carrier file to import in carrier software
     *
     */
    public function ExportToCarrierSoftwareAction() {
        try {
            //Get orders for selected carriers
            $CarrierType = $this->getRequest()->getParam('carrier');
            $shipments = Mage::getModel('Orderpreparation/ordertoprepare')->GetShipments($CarrierType);
            $model = mage::helper('Orderpreparation')->getCarrierModel($CarrierType);

            //generate & return file
            if ($model) {
                $content = $model->CreateExportFile($shipments);
                if (!is_array($content)) {
                    $this->_prepareDownloadResponse($model->getFileName(), $content, 'text/plain');
                } else {
                    $type = $content['mime_type'];
                    $data = $content['content'];
                    $this->_prepareDownloadResponse($model->getFileName(), $data, $type);
                }
            } else {
                die("Unable to bind carrier '" . $CarrierType . "'");
            }

            //genere le fichier
        } catch (Exception $ex) {
            die("Erreur lors de l'export : " . $ex->getMessage());
        }
    }

    /**
     * Import tracking numbers 
     */
    public function ImportTrackingAction() {
        $carrierCode = $this->getRequest()->getPost('carrier');
        $CarrierModel = mage::helper('Orderpreparation')->getCarrierModel($carrierCode);
        $uploader = null;
        $Error = false;
        try {
            $uploader = new Varien_File_Uploader('tracking_file');
            $uploader->setAllowedExtensions(array('txt', 'csv'));
        } catch (Exception $ex) {
            $Error = true;
        }

        if ($Error) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured while uploading file.'));
        } else {
            $path = Mage::app()->getConfig()->getTempVarDir() . '/import/';
            $uploader->save($path);
            if ($uploadFile = $uploader->getUploadedFileName()) {
                //lit le contenu du fichier
                $path .= $uploadFile;
                $content = file($path);

                //importe
                $nb = $CarrierModel->Importfile($content);

                //Met a jour le summary pour toutes les selected orders
                $model = mage::getModel('Orderpreparation/ordertoprepare');
                $orders = $model->getCollection();
                foreach ($orders as $order) {
                    $realOrder = mage::getModel('sales/order')->load($order->getorder_id());
                    $order->setdetails($model->getDetailsForOrder($realOrder))->save();
                }

                //confirme
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('File successfully imported: ') . $nb . ' tracking numbers imported');
            }
            else
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured while uploading file.'));
        }

        //redirige
        $this->_redirect('OrderPreparation/OrderPreparation/');
    }

    /**
     * End of order preparation
     *
     */
    public function FinishAction() {
        Mage::getModel('Orderpreparation/ordertoprepare')->Finish();

        //confirme & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Order preparation complete'));
        $this->_redirect('OrderPreparation/OrderPreparation/');
    }

    /**
     * Send shipment & invoice emails to customer
     *
     */
    public function NotifyCustomersAction() {
        $error = false;
        $msg = '';
        try {
            Mage::getModel('Orderpreparation/ordertoprepare')->NotifyCustomers();
            $msg = $this->__('Customers notified');
        } catch (Exception $ex) {
            $error = true;
            $msg = $ex->getMessage();
        }

        //return result with json
        $response = array(
            'error' => $error,
            'message' => $msg);
        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
    }

    /**
     * 
     * @param type $FullStr
     * @param type $EndStr
     * @return type 
     */
    public function EndsWith($FullStr, $EndStr) {
        // Get the length of the end string
        $StrLen = strlen($EndStr);
        // Look at the end of FullStr for the substring the size of EndStr
        $FullStrEnd = substr($FullStr, strlen($FullStr) - $StrLen);
        // If it matches, it does end with EndStr
        return $FullStrEnd == $EndStr;
    }

    /**
     * Save order data from sales order sheet
     *
     */
    public function SaveOrderAction() {
        try {

            //collect data
            $order_id = $this->getRequest()->getParam('order_id');
            $order = mage::getModel('sales/order')->load($order_id);
            $data = $this->getRequest()->getParams();

            //shipment & invoice
            $shipment_id = $this->getRequest()->getParam('shipment_id');
            $invoice_id = $this->getRequest()->getParam('invoice_id');
            $tracking_num = $this->getRequest()->getParam('tracking_num');
            if ($shipment_id || $invoice_id) {
                mage::getModel('Orderpreparation/ordertoprepare')->load($order_id, 'order_id')
                        ->setshipment_id($shipment_id)
                        ->setinvoice_id($invoice_id)
                        ->save();
            }

            //Manage tracking number
            if ($tracking_num) {
                $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipment_id);
                if ($shipment->getOrder()) {
                    $Carrier = str_replace('_', '', $order->getshipping_method());
                    $track = new Mage_Sales_Model_Order_Shipment_Track();
                    $track->setNumber($tracking_num)
                            ->setCarrierCode($Carrier)
                            ->setTitle('Shipment');
                    $shipment->addTrack($track)->save();
                }
            }

            //store comments / serials / preparation warehouse
            $preparationData = $this->getRequest()->getPost('data');
            foreach ($preparationData as $orderItemId => $values) {
                $erpOrderItem = mage::getModel('AdvancedStock/SalesFlatOrderItem')->load($orderItemId);
                foreach ($values as $key => $value) {
                    $erpOrderItem->setData($key, $value);
                }
                $erpOrderItem->save();
            }

            //update shipping method
            $newShippingMethod = $this->getRequest()->getParam('shipping_method');
            if ($newShippingMethod != '') {
                mage::helper('Orderpreparation/ShippingMethods')->changeForOrder($order_id, $newShippingMethod);
            }

            //confirm
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Changes successfully saved'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured while saving changes: ') . $ex->getMessage() . ' ' . $ex->getTraceAsString());
        }

        //redirect
        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $order_id));
    }


    /**
     * Print button from the Tab prepration of an order
     * So called with MDN_Orderpreparation_Model_Pdf_OrderPreparationCommentsPdf::MODE_ALL
     *
     */
    public function PrintCommentsAction() {
        try {
            $orderId = $this->getRequest()->getParam('order_id');
            $order = Mage::getModel('sales/order')->load($orderId);

            $obj = new MDN_Orderpreparation_Model_Pdf_OrderPreparationCommentsPdf();
            $pdf = $obj->getPdfWithMode($order,MDN_Orderpreparation_Model_Pdf_OrderPreparationCommentsPdf::MODE_ALL);
            $this->_prepareDownloadResponse(mage::helper('purchase')->__('order_comments') . '.pdf', $pdf->render(), 'application/pdf');
        } catch (Exception $ex) {
            die("Erreur lors de la g�n�ration du PDF de commentaires commande: " . $ex->getMessage() . '<p>' . $ex->getTraceAsString());
        }
    }

    /**
     * refresh fullstock, stockless & ignored orders tab content
     *
     */
    public function RefreshListAction() {
        //Truncate table
        Mage::getResourceModel('Orderpreparation/ordertopreparepending')->TruncateTable();

        //retrieve pendings orders ids
        $pendingOrderIds = mage::getModel('Orderpreparation/ordertoprepare')->getPendingOrdersIds();

        //create task group
        $taskGroup = 'dispatch_pending_orders';
        mage::helper('BackgroundTask')->AddGroup($taskGroup, $this->__('Dispatch pendings orders'), 'OrderPreparation/OrderPreparation/');

        //Create task for each orders
        $debug = '##Prepare order dispatching: ';
        for ($i = 0; $i < count($pendingOrderIds); $i++) {
            $orderId = $pendingOrderIds[$i];
            $debug .= $orderId . ', ';
            mage::helper('BackgroundTask')->AddTask('Dispatch order #' . $orderId, 'Orderpreparation', 'dispatchOrder', $orderId, $taskGroup
            );
        }

        //execute task group
        mage::helper('BackgroundTask')->ExecuteTaskGroup($taskGroup);
    }

    /**
     * Return selected orders grid
     *
     */
    public function SelectedOrderGridAction() {
        try {
            $this->loadLayout();
            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('Orderpreparation/SelectedOrders')->toHtml()
            );
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }

    /**
     * Return fullstock orders grid
     *
     */
    public function FullStockOrderGridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('Orderpreparation/FullStockOrders')->toHtml()
        );
    }

    /**
     * Return stockles orders grid
     *
     */
    public function StocklessOrderGridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('Orderpreparation/StocklessOrders')->toHtml()
        );
    }

    /**
     * Return ignored orders grid
     *
     */
    public function IgnoredOrderGridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('Orderpreparation/IgnoredOrders')->toHtml()
        );
    }

    /**
     * Download picking list for selected orders
     *
     */
    public function massDownloadPickingListAction() {

        //retrieve order ids from ordertopreparepending ids
        $orderPreparationIds = $this->getRequest()->getPost('full_stock_orders_order_ids');
        $orderIds = array();
        $collection = mage::getModel('Orderpreparation/ordertopreparepending')
                ->getCollection()
                ->addFieldToFilter('opp_num', array('in' => $orderPreparationIds));

        foreach ($collection as $item)
            $orderIds[] = $item->getopp_order_id();

        $preparationWarehouse = mage::helper('Orderpreparation')->getPreparationWarehouse();
        $data = mage::helper('Orderpreparation/PickingList')->getProductsSummaryFromOrderIds($orderIds, $preparationWarehouse);

        //build and return pdf
        $obj = mage::getModel('Orderpreparation/Pdf_PickingList');
        $pdf = $obj->getPdf($data);
        $name = 'picking_lists.pdf';
        $this->_prepareDownloadResponse($name, $pdf->render(), 'application/pdf');
    }

    /**
     * Download preparation pdf for each orders
     *
     */
    public function massDownloadPreparationPdfAction() {
        //retrieve order ids from ordertopreparepending ids
        $orderPreparationIds = $this->getRequest()->getPost('full_stock_orders_order_ids');
        $orderIds = array();
        $collection = mage::getModel('Orderpreparation/ordertopreparepending')
                ->getCollection()
                ->addFieldToFilter('opp_num', array('in' => $orderPreparationIds));
        foreach ($collection as $item)
            $orderIds[] = $item->getopp_order_id();

        //load orders collection depending of magento version
        if (mage::helper('AdvancedStock/FlatOrder')->ordersUseEavModel()) {
            $collection = mage::getModel('sales/order')
                    ->getCollection()
                    ->addFieldToFilter('entity_id', array('in' => $orderIds))
                    ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
                    ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
                    ->joinAttribute('shipping_company', 'order_address/company', 'shipping_address_id', null, 'left')
                    ->addExpressionAttributeToSelect('shipping_name', 'CONCAT({{shipping_firstname}}, " ", {{shipping_lastname}}, " (", {{shipping_company}}, ")")', array('shipping_firstname', 'shipping_lastname', 'shipping_company'));
        } else {
            $collection = mage::getModel('sales/order')
                    ->getCollection()
                    ->addFieldToFilter('entity_id', array('in' => $orderIds))
                    ->join('sales/order_address', '`sales/order_address`.entity_id=shipping_address_id', array('shipping_name' => "concat(firstname, ' ', lastname)"))
            ;
        }

        $pdf = new Zend_Pdf();
        foreach ($collection as $order) {
            $obj = mage::getModel('Orderpreparation/Pdf_OrderPreparationCommentsPdf');
            $obj->pdf = $pdf;
            $otherPdf = $obj->getPdfWithMode($order, MDN_Orderpreparation_Model_Pdf_OrderPreparationCommentsPdf::MODE_ORDER_PREPRATION_NOT_SELECTED_TAB);

            for ($i = 0; $i < count($otherPdf->pages); $i++) {
                //$pdf->pages[] = $otherPdf->pages[$i];
            }
        }
        $this->_prepareDownloadResponse('order_preparation.pdf', $pdf->render(), 'application/pdf');
    }

    /**
     * Save shipping information such as weight & custom fields from carrier template
     *
     */
    public function SaveShippingInformationAction() {
        try {
            //get Orders
            $collection = mage::getModel('Orderpreparation/ordertoprepare')->getSelectedOrders();
            $data = $this->getRequest()->getPost('data');
            foreach ($collection as $orderToPrepare) {

                $orderData = $data[$orderToPrepare->getId()];
                $orderToPrepare = mage::getModel('Orderpreparation/ordertoprepare')->load($orderToPrepare->getId(), 'order_id');

                //process custom value (set at the carrier template level)
                $customValuesString = '';
                if (isset($orderData['custom_values'])) {
                    foreach ($orderData['custom_values'] as $key => $value) {
                        $customValuesString .= $key . '=' . $value . ';';
                    }
                }

                $orderToPrepare->setreal_weight($orderData['weight'])
                        ->setcustom_values($customValuesString)
                        ->save();

                //process tracking numbers (if shipment exists)
                if ($shipment = $orderToPrepare->getShipment()) {
                    $carrier = $orderToPrepare->GetOrder()->getShippingCarrier();
                    if ($carrier)
                        $carrierCode = $carrier->getCarrierCode();
                    else
                        $carrierCode = '';

                    $trackings = $orderData['tracking'];
                    $trackings = explode(',', $trackings);
                    foreach ($trackings as $tracking) {
                        //Add tracking to shipment
                        Mage::helper('Orderpreparation/Tracking')->addTrackingToShipment($tracking, $shipment->getincrement_id(), $carrierCode, $orderToPrepare->GetOrder()->getShippingDescription()
                        );
                    }
                }
            }

            //Confirm
            $response = array('error' => false, 'message' => $this->__('Data saved'));
        } catch (Exception $ex) {
            //Return error
            $response = array('error' => true, 'message' => $ex->getMessage());
        }

        //return result
        if (is_array($response)) {
            $response = Zend_Json::encode($response);
            $this->getResponse()->setBody($response);
        }
    }

    /**
     * Mass change shipping method for orders
     */
    public function massChangeShippingMethodAction() {

        $orderPreparationIds = $this->getRequest()->getPost('full_stock_orders_order_ids');

        try {
            //get shipping method
            $shippingMethod = $this->getRequest()->getPost('method');
            mage::helper('Orderpreparation/ShippingMethods')->updateShippingMethod($orderPreparationIds, $shippingMethod);

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Shipping methods changed'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : ') . $ex->getMessage());
        }

        //redirect
        $this->_redirect('OrderPreparation/OrderPreparation/');
    }

    /**
     * Set preparation warehouse
     */
    public function setPreparationWarehouseAction() {
        $warehouseId = $this->getRequest()->getParam('warehouse_id');
        $warehouse = mage::getModel('AdvancedStock/Warehouse')->load($warehouseId);
        mage::helper('Orderpreparation')->setPreparationWarehouse($warehouseId);
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('You are now preparing orders from warehouse %s', $warehouse->getstock_name()));
        $this->_redirect('OrderPreparation/OrderPreparation/');
    }

    /**
     * Set operator
     */
    public function setOperatorAction() {
        $userId = $this->getRequest()->getParam('user_id');
        $user = mage::getModel('admin/user')->load($userId);
        mage::helper('Orderpreparation')->setOperator($userId);
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('You are now using operator %s', $user->getusername()));
        $this->_redirect('OrderPreparation/OrderPreparation/');
    }
    
    /**
     * Remove an item from an order 
     */
    public function removeItemAction()
    {
        //remove item
        $orderItemId = $this->getRequest()->getParam('order_item_id');
        $orderItem = Mage::getModel('Orderpreparation/ordertoprepareitem')->load($orderItemId, 'order_item_id');
        $orderItemObj = Mage::getModel('sales/order_item')->load($orderItemId);
        if ($orderItemObj->getparent_item_id())
        {
            $parentOrderItem = Mage::getModel('Orderpreparation/ordertoprepareitem')->load($orderItemObj->getparent_item_id(), 'order_item_id');
            $parentOrderItem->delete();
        }
        $orderItem->delete();
        
        //confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Order item removed from preparation'));
        $this->_redirect('OrderPreparation/OrderPreparation/');
    }

}