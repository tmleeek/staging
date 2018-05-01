<?php

class MDN_DropShipping_Helper_CreatePo extends Mage_Core_Helper_Abstract {

    /**
     * Create a new PO
     * @param type $supId
     * @param type $items
     */
    public function process($supId, $items) {

        //log
        Mage::helper('DropShipping')->log(' create PO');
        
        //load order
        $order = null;
        
        //create PO
        $supplier = Mage::getModel('Purchase/Supplier')->load($supId);
        $po = Mage::helper('purchase/Order')->createNewOrder($supId);
        Mage::helper('DropShipping')->log('PO #'.$po->getpo_order_id());
        $eta = Mage::getModel('core/date')->timestamp(time()) + $supplier->getsup_shipping_delay() * 3600 * 24;
        $po->setpo_supply_date(date('Y-m-d H:i:s', $eta));
        $po->save();

        //append products
        foreach ($items as $orderItemId => $itemData) {
            
            //build description
            $orderItem = mage::getModel('sales/order_item')->load($orderItemId);
            $productId = $orderItem->getproduct_id();
            $supplierSku = Mage::getModel('Purchase/ProductSupplier')->getSupplierSku($productId, $supId);
            if (!$supplierSku)
                $supplierSku = $orderItem->getsku();
            $qty = (int) $orderItem->getqty_ordered();

            $poItem = $po->addProduct($productId, $qty);
            
            //change order item dropship status
            $orderItem->setdropship_status(MDN_DropShipping_Helper_Data::STATUS_PO_CREATED)
                    ->setdropship_supplier_id($supId)
                    ->setpurchase_order_id($po->getId())
                    ->save();
            
            //load order
            if ($order == null)
                $order = Mage::getModel('sales/order')->load($orderItem->getorder_id());
            
            Mage::helper('DropShipping')->log('Append product '.$qty.'x'.$orderItem->getSku());
        }

        //change status to waiting for delivery
        $po->setpo_status(MDN_Purchase_Model_Order::STATUS_WAITING_FOR_DELIVERY)->save();
        
        //notify supplier
        Mage::helper('DropShipping')->log('Notify supplier');
        $po->notifySupplier();
        
         //add organizer to sales order
        $msg = $this->__('PO #%s created', $po->getpo_order_id());
        mage::helper('DropShipping/Organizer')->addOrganizerToOrder($order, $msg);
        
        //add organizer to PO
        $msg = $this->__('Created for order %s', $order->getincrement_id());
        mage::helper('DropShipping/Organizer')->addOrganizerToPurchaseOrder($po, $msg);
        
        //add logs
        Mage::getSingleton('DropShipping/PurchaseOrderSupplierLog')->addLog($po->getpo_sup_num(), $po->getId(), $order->getId());

        return $po;
    }

}