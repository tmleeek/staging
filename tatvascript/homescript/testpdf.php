<?php
define('MAGENTO', realpath(dirname(__FILE__))); 
require_once(MAGENTO.'/PDFMerger/PDFMerger.php');
$pdf_merge = new PDFMerger;

$pdf_merge->addPDF('/data/apache/htdocs/magento/media/pdf_bt/100028902.pdf')->addPDF('/data/apache/htdocs/magento/media/pdf_bt/packingslip_6.pdf');
$pdf_merge->merge('download', 'packingslip.pdf');
exit;
?>