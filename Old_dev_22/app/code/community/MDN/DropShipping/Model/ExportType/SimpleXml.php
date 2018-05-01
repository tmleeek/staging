<?php

class MDN_DropShipping_Model_ExportType_SimpleXml extends Mage_Core_Model_Abstract {

    /**
     * 
     */
    public function getContent($po, $shipments) {

        $xml = Mage::helper('DropShipping/XmlWriter');
        $xml->init();


        //root element
        $xml->push('orders');

        foreach ($shipments as $shipment) {


            $order = $shipment->getOrder();

            $shippingAddress = $order->getShippingAddress();
            if (!$shippingAddress)
                $shippingAddress = $order->getBillingAddress();

            $xml->push('order');

            //order id
            $xml->element('id', $order->getincrement_id());

            //items
            foreach ($shipment->getAllItems() as $item) {
                $xml->push('item');
                $xml->element('id', $item->getsku());
                $xml->element('title', $item->getName());
                $xml->element('count', (int) $item->getQty());
                $xml->element('barcode', Mage::helper('AdvancedStock/Product_Barcode')->getBarcodeForProduct($item->getproduct_id()));
                $xml->pop();
            }

            //shipping address
            $xml->push('delivery_address');
            $xml->element('name', $shippingAddress->getName());
            $xml->element('street_address1', $shippingAddress->getStreet(1));
            $xml->element('street_address2', $shippingAddress->getStreet(2));
            $xml->element('town_city', $shippingAddress->getCity());
            $xml->element('post_code', $shippingAddress->getPostcode());
            $xml->element('country', $shippingAddress->getCountryModel()->getName());
            $xml->element('telephone', $shippingAddress->getTelephone());
            $xml->element('email', $order->getcustomer_email());
            $xml->pop();

            //end order
            $xml->pop();
        }

        //end orderS    
        $xml->pop();

        return $xml->getXml();
    }

    /**
     * 
     */
    public function getMimeType() {
        return 'application/xml';
    }

    /**
     *
     * @return type 
     */
    public function getFileName() {
        return 'orders_to_dropship_' . date('Y-m-d') . '.xml';
    }

}