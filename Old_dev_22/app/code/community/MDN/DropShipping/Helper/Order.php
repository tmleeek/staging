<?php

class MDN_DropShipping_Helper_Order extends Mage_Core_Helper_Abstract {
    
    /**
     * Return purchase orders associated to an order
     * @param type $orderId
     * @return type 
     */
    public function getAssociatedPurchaseOrders($orderId)
    {
        $poIds = array();
        
        $orderItems = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id', $orderId);
        foreach($orderItems as $orderItem)
        {
            if ($orderItem->getpurchase_order_id())
                $poIds[] = $orderItem->getpurchase_order_id();
        }
        
        $purchaseOrders = Mage::getModel('Purchase/Order')
                                ->getCollection()
                                ->addFieldToFilter('po_num', array('in' => $poIds));
        return $purchaseOrders;
    }
    
    /**
     * Return sales order items associated to purchase order
     */
    public function getAssociatedOrderItems($poId)
    {
        $orderItems = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('purchase_order_id', $poId);
        return $orderItems;
    }
    
    /**
     * 
     */
    public function getSalesOrderForPurchaseOrder($poId)
    {
        $orderItems = $this->getAssociatedOrderItems($poId);
        foreach($orderItems as $orderItem)
        {
            return Mage::getModel('sales/order')->load($orderItem->getorder_id());
        }
        
        throw new Exception('Unable to load sales order for PO #'.$poId);
    }
    
}
