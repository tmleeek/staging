<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
require_once Mage::getBaseDir() . DS . 'lib/mpdf/mpdf.php';

class MDN_Orderpreparation_Helper_CommercialInvoice extends Mage_Core_Helper_Abstract
{

    public function _HtmltoPdf($orderIncrmentId,$html = null)
    {
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $filename = "commercialinvoice_" . $orderIncrmentId . ".pdf";
         $mpdf->Output($filename,'D');      // make it to DOWNLOAD
        //$mpdf->Output();
    }

    public function createPdf($order, $shipment)
    {
        
        $html = '';
        $html .= '<div style="border:3px solid #111;">';
        $html .= '<h3 align="center">COMMERCIAL INVOICE #' . $order->getIncrementId() . '</h3>';
        $html .= '<table style="width:100%" border="0" style="font-size:13px;">';
        $html .= '<tr>';
        $html .= '<td width="48%" valign="top" style="padding-right:15px">';
        $html .= '<table style="width:100%" border="0" class="table-border">';
        $html .= '<tr>';
        $html .= '<td height="25" style="border-bottom:1px solid #333; vertical-align:top;" colspan="2"> <strong>SHIPPER</strong> </td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Company Name</b> </span><span style="font-size:11px;"> AZ-Boutique</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Contact Name</b> </span><span style="font-size:11px;">AZ-boutique.fr</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Street Address</b> </span><span style="font-size:11px;">Address Line 1</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>City </b></span><span style="font-size:11px;">France</span></td>';
        $html .= '<td height="25" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Prov </b></span><span style="font-size:11px;">France</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Postal Code</b> </span><span style="font-size:11px;">0123456</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Country</b> </span><span style="font-size:11px;">France</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Telephone No</b> </span><span style="font-size:11px;">0811696929</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Email Address</b> </span><span style="font-size:11px;">info@az-boutique.fr</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Tax ID#</b> </span><span style="font-size:11px;">FR84513706689</span></td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</td>';

        $html .= '<td width="48%" valign="top" style="padding-left:15px">';
        $html .= '<table style="width:100%" border="0">';
        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:top;"> <strong>SHIP TO</strong> </td>';
        $html .= '</tr>';
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        if (!$shippingAddress) {
            $shippingAddress = $billingAddress;
        }
        $companyName = $shippingAddress->getCompany();
        if (!$companyName) {
            $companyName = $billingAddress->getCompany();
        }

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Company Name</b> </span><span style="font-size:11px;">' . $companyName . '</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Contact Name</b> </span><span style="font-size:11px;">' . $shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname() . '</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Street Address</b> </span><span style="font-size:11px;">';
        foreach ($shippingAddress->getStreet() as $address) {
            $html .= $address;
        }
        $html .= '</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>City </b></span><span style="font-size:11px;">' . $shippingAddress->getCity() . '</span></td>';
        $html .= '<td height="25" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Prov </b></span><span style="font-size:11px;">' . $shippingAddress->getRegion() . '</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Postal/ZIP Code</b> </span><span style="font-size:11px;">' . $shippingAddress->getPostcode() . '</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Country</b> </span><span style="font-size:11px;">' . Mage::app()->getLocale()->getCountryTranslation($shippingAddress->getCountryId()) . '</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Telephone No</b> </span><span style="font-size:11px;">' . $shippingAddress->getTelephone() . '</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Email Address</b> </span><span style="font-size:11px;">' . $shippingAddress->getEmail() . '</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Tax ID#</b> </span><span style="font-size:11px;">' . $shippingAddress->getVatId() . '</span></td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</td>';
        $html .= '</tr>';

        $html .= '<tr><td height="20px" colspan="2"></td></tr>';

        $html .= '<tr>';
        $html .= '<td width="100%" colspan="2">';
        $html .= '<table style="width:100%; border-collapse: collapse;"  border="0" >';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th style="border:1px solid #333;">Description of Goods</th>';
        $html .= '<th style="border:1px solid #333;">Country of Origin</th>';
        $html .= '<th style="border:1px solid #333;">NAFTA Y/N</th>';
        $html .= '<th style="border:1px solid #333;">Unit</th>';
        $html .= '<th style="border:1px solid #333;">Unit Value</th>';
        $html .= '<th style="border:1px solid #333;">Total price</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        foreach ($order->getAllItems() as $item) {
            $Product = Mage::getModel("catalog/product")->load($item->getProductId());
            $manufacturer = ($Product->getCountryOfManufacture()) ? $Product->getCountryOfManufacture() : "France";
            $Total = ($item->getQtyOrdered() * $item->getPrice());
            $html .= '<tr>';
            $html .= '<td style="border:1px solid #333;">' . $Product->getName()."<br/>". $Product->getAttributeText('manufacturer') ."<br/>". $Product->getAttributeText('gamme_collection_new') .'</td>';
            $html .= '<td style="border:1px solid #333;">' . $manufacturer . '</td>';
            $html .= '<td style="border:1px solid #333;">N</td>';
            $html .= '<td style="border:1px solid #333;">' . $item->getQtyOrdered() . '</td>';
            $html .= '<td style="border:1px solid #333;">' . $item->getPrice() . '</td>';
            $html .= '<td style="border:1px solid #333;">' . $Total . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</td>';
        $html .= '</tr>';

        $html .= '<tr><td height="20px" colspan="2"></td></tr>';

        $html .= '<tr>';
        $html .= '<td width="55%"> ';
        $html .= '<table style="width:100%">';
        $html .= '<tr>';
        $reason = Mage::getStoreConfig('commercial_invoice/reason_for_export/com_reason', $order->getStoreId());        
        $html .= '<td><span style="font-weight:900;">Reason for export </span><span style="border-bottom:1px solid #111;">'.$reason.'</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td>I hearby certify that the information on this Invoice is true and correct to the best of my knowledge.</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</td> ';

        $html .= '<td width="45%"> ';
        $html .= '<table style="width:100%">';
        $html .= '<tr>';
        $html .= '<td>Currency</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $USD = $CHF = $GBP = $EUR = '&nbsp;';

        if ($order->getOrderCurrencyCode() == "USD") {
            $USD = "X";
        } elseif ($order->getOrderCurrencyCode() == "CHF") {
            $CHF = "X";
        } elseif ($order->getOrderCurrencyCode() == "GBP") {
            $GBP = "X";
        } else {
            $EUR = "X";
        }


        $html .= '<td>
						
							<table>
								<tr>
									<td style="padding:4px">EUR</td>
									<td style="padding:4px 8px; border:1px solid #333;">' . $EUR . '</td>
									<td style="padding:4px">USD</td>
									<td style="padding:4px 8px; border:1px solid #333;">' . $USD . '</td>
									<td style="padding:4px">CHF</td>
									<td style="padding:4px 8px; border:1px solid #333;">' . $CHF . '</td>
									<td style="padding:4px">GBP</td>
									<td style="padding:4px 8px; border:1px solid #333;">' . $GBP . '</td>
								</tr>
							</table>
							
							</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</td> ';
        $html .= '</tr>';

        $html .= '<tr><td height="20px" colspan="2"></td></tr>';

        $html .= '<tr>';
        $html .= '<td style="border-bottom:1px solid #333;"><span style="font-weight:900;">Signature </span><span style="border-bottom:1px solid; width:200px;"></span></td>';
        $invoiceCollection = $order->getInvoiceCollection();
        $invoiceDate = '';
        foreach($invoiceCollection as $invoice){    
           $invoiceDate = date('M j, Y',strtotime($invoice->getCreatedAt()));
        }
        $html .= '<td style="border-bottom:1px solid #333;"><span style="font-weight:900;">Date </span><span style="border-bottom:1px solid; width:200px;">'.$invoiceDate.'</span></td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';

        //echo $html;
        //exit;
        $this->_HtmltoPdf($order->getIncrementId(),$html);
    }
}
