<?php

class MDN_DropShipping_Helper_Attachment extends Mage_Core_Helper_Abstract {

    /**
     * Return possible attachment types
     */
    public function getAttachmentTypes() {
        $types = array();

        $types['pdf'] = $this->__('Pdf');
        $types['simple_xml'] = $this->__('Simple XML');
        $types['csv'] = $this->__('CSV');
        //$types['nothing'] = $this->__('Nothing');

        return $types;
    }

    /**
     * Return attachment based on the type
     * @param type $type
     * @param type $po
     * @param type $order
     */
    public function getAttachment($type, $po, $order) {
        
        switch ($type) {
            case 'simple_xml':

                //simple xml content
                $fakeShipment = $this->createFakeShipments($order, $po);
                $xml = Mage::getModel('DropShipping/ExportType_SimpleXml')->getContent($po, array($fakeShipment));
                $Attachment = array();
                $Attachment['name'] = mage::helper('purchase')->__('Order #%s', $order->getincrement_id()) . '.xml';
                $Attachment['content'] = $xml;
                return $Attachment;

                break;
            case 'pdf':

                
                //Fake Packing slip
                $fakeShipment = $this->createFakeShipments($order, $po);
                $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf(array($fakeShipment));
                $Attachment = array();
                $Attachment['name'] = mage::helper('purchase')->__('Packing slips #%s', $order->getincrement_id()) . '.pdf';
                $Attachment['content'] = $pdf->render();
                return $Attachment;

            case 'csv':

                //csv content
                $fakeShipment = $this->createFakeShipments($order, $po);
                $xml = Mage::getModel('DropShipping/ExportType_Csv')->getContent($po, array($fakeShipment));
                $Attachment = array();
                $Attachment['name'] = mage::helper('purchase')->__('Order #%s', $order->getincrement_id()) . '.csv';
                $Attachment['content'] = $xml;
                return $Attachment;

                break;
            case 'nothing':
                //nothing
                break;
        }
    }

    /**
     * Create fake shipments
     * @param type $orders
     */
    protected function createFakeShipments($order, $po) {


        $convertor = Mage::getModel('sales/convert_order');
        $shipment = $convertor->toShipment($order);

        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->isDummy(true) && !$orderItem->getQtyToShip()) {
                continue;
            }
            if ($orderItem->getIsVirtual()) {
                continue;
            }

            foreach ($po->GetProducts() as $pop) {
                if ($pop->getpop_product_id() == $orderItem->getproduct_id()) {
                    $ShipmentItem = $convertor->itemToShipmentItem($orderItem);
                    $ShipmentItem->setQty($orderItem->getqty_ordered());
                    $shipment->addItem($ShipmentItem);
                }
            }
        }

        $shipment->setOrder($order);

        return $shipment;
    }

}