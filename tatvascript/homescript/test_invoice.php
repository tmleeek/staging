<?php

chdir(dirname(__FILE__));
require '../../app/Mage.php';
Mage::app();

$invoicesIds = "128574";

    $invoices = Mage::getResourceModel('sales/order_invoice_collection')
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('entity_id', array('in' => $invoicesIds))
        ->load();

    $pdf = Mage::getModel('attachpdf/sales_order_pdf_temp')->getPdf($invoices);


?>