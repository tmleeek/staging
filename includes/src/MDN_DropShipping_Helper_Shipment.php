<?php

class MDN_DropShipping_Helper_Shipment extends Mage_Core_Helper_Abstract {

    /**
     * Create shipment
     *
     */
    public function createShipment($new_order, $orderItemIds) {

        $convertor = Mage::getModel('sales/convert_order');
        $shipment = $convertor->toShipment($new_order);

        foreach ($new_order->getAllItems() as $orderItem) {
            if (!$orderItem->isDummy(true) && !$orderItem->getQtyToShip()) {
                continue;
            }
            if ($orderItem->getIsVirtual()) {
                continue;
            }

            if (!in_array($orderItem->getId(), $orderItemIds))
                continue;

            $ShipmentItem = $convertor->itemToShipmentItem($orderItem);
            $ShipmentItem->setQty($orderItem->getqty_ordered());
            $shipment->addItem($ShipmentItem);
        }

        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($shipment)
                        ->addObject($shipment->getOrder())
                        ->save();

        return $shipment;
    }

}