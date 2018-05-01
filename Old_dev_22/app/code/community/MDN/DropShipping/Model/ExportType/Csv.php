<?php

class MDN_DropShipping_Model_ExportType_Csv extends Mage_Core_Model_Abstract {

    const lineReturn = "\r\n";
    
    /**
     * 
     */
    public function getContent($po, $shipments) {

        $csv = '';
        
        //load templates
        $orderTemplate = $this->getOrderTemplate($po->getSupplier());
        $orderItemTemplate = $this->getOrderItemTemplate($po->getSupplier());

        foreach ($shipments as $shipment) {

            $codes = $this->getCodes($shipment, $po->getSupplier());
            
            if ($orderTemplate)
                $csv .= $this->runTemplate($orderTemplate, $codes).self::lineReturn;
            
            if ($orderItemTemplate)
            {
                foreach ($shipment->getAllItems() as $item) {
                    $csv .= $this->runTemplate($orderItemTemplate, $codes).self::lineReturn;
                }
            }
        }

        return $csv;
    }

    /**
     * 
     */
    public function getMimeType() {
        return 'text/csv';
    }

    /**
     *
     * @return type 
     */
    public function getFileName() {
        return 'orders_to_dropship_' . date('Y-m-d') . '.csv';
    }

    /**
     * 
     * @param type $supplier
     */
    protected function getOrderTemplate($supplier) {
        return $supplier->getsup_dropshipping_tpl_order();
    }

    /**
     * 
     * @param type $supplier
     */
    protected function getOrderItemTemplate($supplier) {
        return $supplier->getsup_dropshipping_tpl_order_item();
    }

    /**
     * 
     */
    public function getCodes($shipment, $supplier) {
        $codes = array();
        $order = $shipment->getOrder();
        $collections = array();
        $collections['shipment.'] = $shipment->getData();
        $collections['order.'] = $order->getData();
        $collections['customer.'] = Mage::getModel('customer/customer')->load($order->getCustomerId())->getData();
        if ($order->getShippingAddress())
            $collections['shipping_address.'] = $order->getShippingAddress()->getData();
        if ($order->getBillingAddress())
            $collections['billing_address.'] = $order->getBillingAddress()->getData();
        if ($order->getPayment())
            $collections['payment.'] = $order->getPayment()->getData();
        $i = 1;
        foreach ($shipment->getAllItems() as $item) {
            $collections['order_item.'] = $item->getData();
            $product = Mage::getModel('catalog/product')->load($item->getproduct_id());
            $collections['order_item.product.'] = $product->getData();
            $pps = Mage::getModel('Purchase/ProductSupplier')->loadForProductSupplier($product->getId(), $supplier->getId());
            $collections['supplier_product.'] = $pps->getData();
        }

        //create code array
        if ($order->getCustomerId())
        {
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            foreach ($collections as $prefix => $datas) {
                foreach ($datas as $k => $v) {
                    if ((!is_array($v)))
                        $codes[$prefix . $k] = $v;
                }
            }
        }
        
        return $codes;
    }
    
    protected function runTemplate($template, $codes)
    {
        $result = $template;
        foreach($codes as $k => $v)
        {
            if (is_object($v))
                continue;
            $result = str_replace('{'.$k.'}', $v, $result);
        }
        return $result;
    }

}