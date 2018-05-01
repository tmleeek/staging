<?php

class MDN_DropShipping_Helper_DropShipRequest extends Mage_Core_Helper_Abstract {

    /**
     * Send a drop ship request :
     * -> Create the PO
     * -> Add and Associate order items to the PO
     * -> Send email to supplier
     * 
     * @param type $supId
     * @param type $orderId
     * @param type $items
     */
    public function sendRequest($supId, $orderId, $items, $additionalData = array()) {

        //load sales order
        $order = mage::getModel('sales/order')->load($orderId);
        
        Mage::helper('DropShipping')->log($order->getincrement_id().' Send request');
        
        //create the purchase order
        $po = $this->createPoForRequest($order, $supId);
        
        //force shipping if set in additional data
        if (isset($additionalData['shipping']) && ($additionalData['shipping'] != ''))
            $po->setpo_shipping_cost($additionalData['shipping'])->save();

        //browse order items
        foreach ($items as $orderItemId => $itemData) {

            //build description
            $orderItem = mage::getModel('sales/order_item')->load($orderItemId);
            $itemComment = isset($itemData['comments']) ? $itemData['comments'] : '';
            $productId = $orderItem->getproduct_id();
            $supplierSku = Mage::getModel('Purchase/ProductSupplier')->getSupplierSku($productId, $supId);
            if (!$supplierSku)
                $supplierSku = $orderItem->getsku();
            $qty = (int) $orderItem->getqty_ordered();

            //append product to the PO
            $poItem = $po->addProduct($productId, $qty);
            
            //update po item price
            if (isset($itemData['price']))
                $poItem->setpop_price_ht($itemData['price'])->save();

            //change order item dropship status
            $orderItem->setdropship_status(MDN_DropShipping_Helper_Data::STATUS_DROPSHIP_REQUEST_SENT)
                    ->setdropship_supplier_id($supId)
                    ->setdropship_comments($itemComment)
                    ->setpurchase_order_id($po->getId())
                    ->save();
        }

        //send email to supplier
        if (!Mage::getStoreConfig('dropshipping/dropship_request/disable_supplier_notification'))
            $this->sendEmailToSupplier($supId, $po, $order);
        
        //append organizer to sales order
        $msg = $this->__('Drop ship request sent (PO #%s)', $po->getpo_order_id());
        mage::helper('DropShipping/Organizer')->addOrganizerToOrder($order, $msg);
        
        return $po;
    }


    /**
     * Confirm a drop ship request
     * 
     * @param type $po
     * @param type $shipping
     * @param type $products
     */
    public function confirmDropShipRequest($po, $shipping = null, $products = null, $supplierRef = null)
    {
        Mage::helper('DropShipping')->log($po->getpo_order_id().' : confirm drop ship request');
        
        //update PO product prices
        if ($products)
        {
            foreach($po->GetProducts() as $pop)
            {
                if (isset($products[$pop->getId()]['price']))
                    $pop->setpop_price_ht($products[$pop->getId()]['price'])->save();
            }
        }
        
        //update order item statuses
        $orderItems = Mage::helper('DropShipping/Order')->getAssociatedOrderItems($po->getId());
        foreach($orderItems as $orderItem)
        {
            $orderItem->setdropship_status(MDN_DropShipping_Helper_Data::STATUS_DROPSHIP_REQUEST_CONFIRMED)->save();
        }
        
        //update shipping price & status & $supplierRef
        if ($shipping)
            $po->setpo_shipping_cost($shipping);
        $po->setpo_status(MDN_Purchase_Model_Order::STATUS_WAITING_FOR_DELIVERY);
        $po->setpo_supplier_order_ref($supplierRef);
        $po->save();     
        
        //add organizer to sales order
        $order = Mage::helper('DropShipping/Order')->getSalesOrderForPurchaseOrder($po->getId());
        $msg = $this->__('Drop ship request confirmed by supplier for PO #%s', $po->getpo_order_id());
        mage::helper('DropShipping/Organizer')->addOrganizerToOrder($order, $msg);
        
        //add organizer to PO
        $msg = $this->__('Drop ship request confirmed by supplier');
        mage::helper('DropShipping/Organizer')->addOrganizerToPurchaseOrder($po, $msg);
        
    }
    
    
    /**
     * Confirm the dropship delivery : 
     * -> Create PO delivery
     * -> Create sales order shipment (and append tracking), create invoice
     * -> Notify customer
     * -> add to logs
     * @param type $po
     * @param type $tracking
     */
    public function confirmDropShipShipping($po, $tracking)
    {
        Mage::helper('DropShipping')->log($po->getpo_order_id().' : confirm drop ship shipping');
        
        //if po is already complete, raise an exception
        if ($po->getpo_status() == MDN_Purchase_Model_Order::STATUS_COMPLETE)
            throw new Exception($this->__('This dropship has already been confirmed !'));
        
        //Create an array with order items warehouses
        $warehouses = array();
        $orderItems = Mage::helper('DropShipping/Order')->getAssociatedOrderItems($po->getId());
        $order = Mage::helper('DropShipping/Order')->getSalesOrderForPurchaseOrder($po->getId());
        $orderItemIds = array();
        foreach($orderItems as $orderItem)
        {
            $warehouses[$orderItem->getProductId()] = $orderItem->getpreparation_warehouse();
            $orderItemIds[] = $orderItem->getId();
        }
        
        //create PO delivery
        $purchaseOrderUpdater = mage::getModel('Purchase/Order_Updater')->init($po);
        $deliveryDate = date('Y-m-d');
        $deliveryDescription = $this->__('Drop shipping, purchase order #%s', $po->getpo_order_id());
        foreach ($po->getProducts() as $item) {
            $qty = $item->getpop_qty();
            $productId = $item->getpop_product_id();
            $warehouseId = $warehouses[$productId];
            $po->createDelivery($item, $qty, $deliveryDate, $deliveryDescription, $warehouseId);
        }
        $po->resetProducts();
        $po->setpo_status(MDN_Purchase_Model_Order::STATUS_COMPLETE);
        $po->setpo_missing_price($po->hasMissingPrices());
        $po->save();
        $purchaseOrderUpdater->checkForChangesAndLaunchUpdates($po);
        
        //create sales order shipment
        $shipment = mage::helper('DropShipping/Shipment')->createShipment($order, $orderItemIds);
        
        //reload order and items to prevent order item to erase shipment changes
        $order = Mage::getModel('sales/order')->load($order->getId());
        $orderItems = Mage::helper('DropShipping/Order')->getAssociatedOrderItems($po->getId());
        
        //append tracking to sales order shipment
        if ($tracking)
        {
            Mage::helper('DropShipping/Tracking')->addToShipment($shipment, $tracking);
        }

        //Invoice order
        if (Mage::getStoreConfig('dropshipping/misc/create_invoice_on_dropship'))
        {
            if ($order->IsCompletelyShipped()) {
                $invoice = mage::helper('DropShipping/Invoice')->createInvoice($order);
            }
            else
                Mage::helper('DropShipping')->log('do not create invoice (order not completely shipped)');
        }
        else
            Mage::helper('DropShipping')->log('do nbt create invoice (disabled in configuration)');
        
        //notify customer
        if (Mage::getStoreConfig('dropshipping/misc/notify_customer_on_drop_ship'))
            $this->notifyCustomer($shipment, $order);
        else
            Mage::helper('DropShipping')->log($po->getpo_order_id().' : supplier notificaiton is DISABLED');
        
        //confirm order items
        foreach($orderItems as $orderItem)
        {
            $orderItem->setdropship_status(MDN_DropShipping_Helper_Data::STATUS_DROPSHIPPED)->save();
        }
        
         //add organizer to sales order
        $msg = $this->__('Drop ship shipping confirmed by supplier for PO #%s', $po->getpo_order_id());
        mage::helper('DropShipping/Organizer')->addOrganizerToOrder($order, $msg);
        
        //add organizer to PO
        $msg = $this->__('Drop ship shipping confirmed by supplier');
        mage::helper('DropShipping/Organizer')->addOrganizerToPurchaseOrder($po, $msg);
        
        //add logs
        Mage::getSingleton('DropShipping/PurchaseOrderSupplierLog')->addLog($po->getpo_sup_num(), $po->getId(), $order->getId());
       
        
    }
    
    
    /**
     * Cancel a drop ship
     * 
     * @param type $poId
     */
    public function cancel($poId)
    {
        //load po & sales oder
        $po = Mage::getModel('Purchase/Order')->load($poId);
        $order = Mage::helper('DropShipping/Order')->getSalesOrderForPurchaseOrder($poId);
        
        Mage::helper('DropShipping')->log($order->getincrement_id().' cancel PO #'.$poId);
        
        //find order items and "reset" them
        $orderItems = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('purchase_order_id', $poId);
        foreach($orderItems as $item)
        {
            $item->setdropship_status('')
                    ->setdropship_comments('')
                    ->setpurchase_order_id(0)
                    ->save();
        }
        
        //add organizer to purchase order
        mage::helper('DropShipping/Organizer')->addOrganizerToPurchaseOrder($po, $this->__('Drop ship cancelled by user'));
        
        
        //add organizer to sales order
        $msg = $this->__('Drop ship cancelled for PO #%s', $po->getpo_order_id());
        mage::helper('DropShipping/Organizer')->addOrganizerToOrder($order, $msg);
        
        //delete PO (to keep tracks)
        $po->delete();
        
    }

    
    /**
     * Create "empty" purchase order
     * @param type $order
     * @param type $supId
     */
    protected function createPoForRequest($order, $supId) {
        
        Mage::helper('DropShipping')->log($order->getincrement_id().' create PO for request');
        
        //create PO
        $po = Mage::helper('purchase/Order')->createNewOrder($supId);
        $po->setis_drop_ship(1);

        //set default shipping fees
        $supplier = Mage::getModel('Purchase/Supplier')->load($supId);

        //add organizer
        $msg = 'Drop ship request sent for order #' . $order->getincrement_id();
        mage::helper('DropShipping/Organizer')->addOrganizerToPurchaseOrder($po, $msg);

        //set shipping fees and PO status
        $po->setpo_shipping_cost($supplier->getsup_dropshipping_default_shipping_fees());
        $po->setpo_status(MDN_Purchase_Model_Order::STATUS_WAITING_FOR_SUPPLIER);
        
        $eta = Mage::getModel('core/date')->timestamp(time()) + $supplier->getsup_shipping_delay() * 3600 * 24;
        $po->setpo_supply_date(date('Y-m-d H:i:s', $eta));
        
        $po->save();

        return $po;
    }

    /**
     * Send email to supplier
     */
    protected function sendEmailToSupplier($supId, $po, $order) {
        
        Mage::helper('DropShipping')->log($order->getincrement_id().' Send email to supplier');
        
        //retrieve information
        $supplier = mage::getModel('Purchase/Supplier')->load($supId);
        $email = $supplier->getsup_mail();
        if ($email == '')
            Mage::log('Supplier mail is not set for supplier '.$supplier->getsup_name().' !');
        $emails = explode(',', $email);
        
        $cc = Mage::getStoreConfig('dropshipping/dropship_request/cc_to');
        $identity = Mage::getStoreConfig('dropshipping/dropship_request/email_identity');
        $emailTemplate = Mage::getStoreConfig('dropshipping/dropship_request/email_template');

        //build text for items
        $description = '<p>Request reference : '.$po->getpo_order_id().'</p>';
        $description .= '<p>Order information : ' . mage::helper('DropShipping')->__('Order #%s (%s - %s) :', $order->getincrement_id(), $order->getcreated_at(), $order->getCustomerName());
        foreach($po->GetProducts() as $pop)
        {
            $description .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $pop->getpop_qty() . 'x ' . $pop->getpop_supplier_reference() . ' : ' . $pop->getpop_product_name();
        }
        
        $data = array
            (
            'subject' => Mage::helper('DropShipping')->__('DropShip Order %s - %s', $po->getpo_order_id(), $order->getincrement_id()),
            'company_name' => Mage::getStoreConfig('purchase/notify_supplier/company_name'),
            'description' => $description,
            'po' => $po->getData()
        );

        //manage attachments
        $Attachments = array();
        
        //attach packing slip ?
        if (Mage::getStoreConfig('dropshipping/dropship_request/attach_packing_slip')) {
            $supplierAttachmentMode = $supplier->getsup_dropshipping_export_type();
            
            $att = Mage::helper('DropShipping/Attachment')->getAttachment($supplierAttachmentMode, $po, $order);
            if ($att)
                $Attachments[] = $att;
            
        }
        
        //attach PO PDF
        if (Mage::getStoreConfig('dropshipping/dropship_request/attach_po_pdf')) {
                //Fake Packing slip
                $pdf = Mage::getModel('DropShipping/Pdf_PurchaseOrder')->getPdf(array($po));
                $Attachment = array();
                $Attachment['name'] = mage::helper('purchase')->__('Drop Ship #%s', $po->getpo_order_id()) . '.pdf';
                $Attachment['content'] = $pdf->render();
                $Attachments[] = $Attachment;
        }

        //send email
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        foreach($emails as $email)
        {
            $email = trim($email);
            Mage::getModel('Purchase/Core_Email_Template')
                    ->setDesignConfig(array('area' => 'adminhtml'))
                    ->sendTransactional(
                            $emailTemplate, $identity, $email, '', $data, null, $Attachments
            );
        }

        //send email to cc
        if ($cc != '') {
            Mage::getModel('Purchase/Core_Email_Template')
                    ->setDesignConfig(array('area' => 'adminhtml'))
                    ->sendTransactional(
                            $emailTemplate, $identity, $cc, '', $data, null, $Attachments);
        }

        $translate->setTranslateInline(true);
    }

    /**
     * Notify customer for shipment
     * @param <type> $shipment
     * @param <type> $order
     */
    public function notifyCustomer($shipment, $order) {
        
        Mage::helper('DropShipping')->log($order->getincrement_id().' notify customer for shipment #'.$shipment->getId());
        
        if (!$shipment->getEmailSent()) {
            $shipment->sendEmail(true);
            $shipment->setEmailSent(true)->save();
        }
    }

}