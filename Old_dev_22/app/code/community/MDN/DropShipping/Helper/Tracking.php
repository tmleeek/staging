<?php

class MDN_DropShipping_Helper_Tracking extends Mage_Core_Helper_Abstract {

    /**
     * Add a tracking number
     * @param type $orderId
     * @param type $trackingNumber 
     */
    public function addToOrder($orderId, $trackingNumber) {
        //get shipment
        $order = Mage::getModel('sales/order')->load($orderId);
        $shipment = null;
        foreach ($order->getShipmentsCollection() as $s)
            $shipment = $s;

        //Add tracking to shipment
        if ($shipment) {
            return $this->addToShipment($shipment, $trackingNumber);
        }
    }

    public function addToShipment($shipment, $trackingNumber) {
        $order = $shipment->getOrder();
        $carrierCode = $order->getShippingCarrier()->getCarrierCode();
        $trackingLabel = $order->getshipping_description();

        $track = new Mage_Sales_Model_Order_Shipment_Track();
        $track->setNumber($trackingNumber)
                ->setCarrierCode($carrierCode)
                ->setTitle($trackingLabel);
        $shipment->addTrack($track)->save();
        
    }
    
}
