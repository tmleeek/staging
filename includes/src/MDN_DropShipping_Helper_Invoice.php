<?php

class MDN_DropShipping_Helper_Invoice extends Mage_Core_Helper_Abstract {

    
    /**
     * Create invoice
     *
     */
    public function createInvoice($new_order) {
        
        Mage::helper('DropShipping')->log('createInvoice for order #'.$new_order->getincrment_id());

        //parcourt les elements de la commande
        $hasProducts = false;
        $itemsToInvoice = array();
        foreach ($new_order->getAllItems() as $orderItem) {
            //ajout au invoice
            //$InvoiceItem = $convertor->itemToInvoiceItem($orderItem);
            $qty = $orderItem->getqty_ordered() - $orderItem->getqty_invoiced();
            if ($qty > 0) {
                //$InvoiceItem->setQty($qty);
                //$invoice->addItem($InvoiceItem);
                $hasProducts = true;
                $itemsToInvoice[$orderItem->getId()] = $qty;
            }
        }

        Mage::helper('DropShipping')->log(count($itemsToInvoice).' items to invoice');
        
        if (!$hasProducts)
            return null;


        try {
            
            if (!$new_order->canInvoice()) {
                Mage::helper('DropShipping')->log('canInvoice = false');
                return false;
            }
            
            $invoice = Mage::getModel('sales/service_order', $new_order)->prepareInvoice($itemsToInvoice);
            if ($invoice->canCapture())
            {
                Mage::helper('DropShipping')->log('online capture');
                $captureMode = 'online';              
                $debug .= ', capture invoice '.$captureMode;
                $invoice->setRequestedCaptureCase($captureMode);
            }
            else
                Mage::helper('DropShipping')->log('do not capture invoice');
            
            //save invoice
            $invoice->register();
            $invoice->getOrder()->setIsInProcess(true);
            $transactionSave = Mage::getModel('core/resource_transaction')
                            ->addObject($invoice)
                            ->addObject($invoice->getOrder())
                            ->save();
            //$invoice->save();


        } catch (Exception $ex) {
            Mage::helper('DropShipping')->logException($ex);
            throw new Exception('Error while creating Invoice for Order ' . $new_order->getincrement_id() . ': ' . $ex->getMessage());
        }
        

        return $invoice;
    }

}