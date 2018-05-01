<?php

class MDN_DropShipping_Helper_PriceRequest extends Mage_Core_Helper_Abstract {
    
    /**
     * 
     * @param type $orderItemIds
     */
    public function sendRequest($orderItemIds)
    {
        //load items
        $orderItems = Mage::getModel('sales/order_item')
                        ->getCollection()
                        ->addFieldToFilter('item_id', array('in' => $orderItemIds));
        
        //first loop to dispatch products per supplier
        $products = $this->getProductFromOrderItem($orderItems);
        $suppliers = array();
        foreach($products as $product)
        {
            $productSuppliers = Mage::getSingleton('Purchase/ProductSupplier')->getSuppliersForProduct($product['product']);
            foreach($productSuppliers as $pps)
            {
                if (!isset($suppliers[$pps->getpps_supplier_num()]))
                    $suppliers[$pps->getpps_supplier_num()] = array();
                $suppliers[$pps->getpps_supplier_num()][] = $product;
            }
        }
        
        //
        foreach($suppliers as $supplierId => $products)
        {
            $this->sendPriceRequestEmail($supplierId, $products);
        }
        
        //update information in orderItem
        foreach($orderItems as $orderItem)
        {
            $orderItem->setdropship_status(MDN_DropShipping_Helper_Data::STATUS_DROPSHIP_PRICE_REQUEST_SENT)
                    ->setdropship_comments($this->__('Price request sent the '.date('Y-m-d H:i:s')))
                    ->save();
        }

        return count($orderItems);
    }
    
    /**
     * Extract product id list from order items
     * 
     * @param type $orderItemIds
     */
    protected function getProductFromOrderItem($orderItems)
    {
        $products = array();
        
        foreach($orderItems as $item)
        {
            $data = array();
            $data['qty'] = $item->getRemainToShipQty();
            $data['product'] = Mage::getModel('catalog/product')->load($item->getProductId());
            $products[] = $data;
            
        }
        
        return $products;
    }

    /**
     * Send price request email to every suppliers associated to the product
     */
    protected function sendPriceRequestEmail($supplierId, $products)
    {
        //build generic data
        $identity = Mage::getStoreConfig('dropshipping/price_request/email_identity');
        $emailTemplate = Mage::getStoreConfig('dropshipping/price_request/email_template');
        $cc = Mage::getStoreConfig('dropshipping/price_request/cc');
        
        //build text
        $txt = '';
        foreach($products as $p)
        {
            $supplierReference = Mage::getSingleton('Purchase/ProductSupplier')->getSupplierSku($p['product']->getId(), $supplierId);;
            $txt .= '<br>'.$p['qty'].'x '.$p['product']->getName().' ('.$supplierReference.')';
        }
        
        $emailData = array();
        $emailData['txt'] = $txt;
        
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
            
        $supplier = Mage::getSingleton('Purchase/Supplier')->load($supplierId);
        $emails = array($supplier->getsup_mail());
        if ($cc)
            $emails[] = $cc;
        foreach($emails as $email)
        {

            Mage::getModel('core/email_template')
                    ->setDesignConfig(array('area' => 'adminhtml'))
                    ->sendTransactional(
                            $emailTemplate, $identity, $email, '', $emailData);
        }            
        
        $translate->setTranslateInline(true);
    }
    
    /**
     * 
     * @param type $orderItemId
     */
    public function cancelRequest($orderItemId)
    {
        $orderItem = Mage::getModel('sales/order_item')->load($orderItemId);
        $orderItem->setdropship_status('')
                    ->setdropship_comments('')
                    ->save();
    }

    /**
     * Confirm price request
     * @param type $supplierId
     * @param type $orderId
     * @param type $items
     */
    public function confirmRequest($supplierId, $orderId, $items)
    {
        //process depending of the mode
        switch(Mage::getStoreConfig('dropshipping/price_response_confirmation/action'))
        {
            case 'dropship':
                $po = mage::helper('DropShipping/DropShipRequest')->sendRequest($supplierId, $orderId, $items);
                mage::helper('DropShipping/DropShipRequest')->confirmDropShipRequest($po);
                break;
            case 'dropship_request':
                mage::helper('DropShipping/DropShipRequest')->sendRequest($supplierId, $orderId, $items);
                break;
        }
    }
}
