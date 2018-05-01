<?php
define('MAGENTOROOT', dirname(__FILE__));
require_once(MAGENTOROOT.'/app/Mage.php');
$exporter = new HtmltoPdf();


class HtmltoPdf
{
	function __construct()
	{
		ini_set('max_execution_time', "-1");
		ini_set('memory_limit', "-1");
		ini_set('auto_detect_line_endings',TRUE);
		chdir(MAGENTOROOT);
		umask(0);
		Mage::app('admin');
		$orderIncrementId = 100047386;
		$order = Mage::getModel("sales/order")->loadByIncrementId($orderIncrementId);
		
	
		// echo "<pre>";
		// foreach($order->getAllItems() as $item){
			// $product = Mage::getModel("catalog/product")->load($item->getProductId());
			// print_r($item->getData());
		// }
		
		// exit;
		
		
		$html = '';
		$html .= '<div style="border:3px solid #111;">';
		$html .= '<h3 align="center">COMMERCIAL INVOICE #'.$order->getIncrementId().'</h3>';
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
							
						$html .= '<tr>';
							$html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Company Name</b> </span><span style="font-size:11px;">'.$order->getShippingAddress()->getCompany().'</span></td>';
						$html .= '</tr>';
							
						$html .= '<tr>';
							$html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Contact Name</b> </span><span style="font-size:11px;">'.$order->getShippingAddress()->getFirstname().' '.$order->getShippingAddress()->getLastname().'</span></td>';
						$html .= '</tr>';
						
						$html .= '<tr>';
							$html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Street Address</b> </span><span style="font-size:11px;">';
							foreach($order->getShippingAddress()->getStreet() as $address){
								$html .= $address;
							}
							$html .= '</span></td>';
						$html .= '</tr>';
						
						$html .= '<tr>';
							$html .= '<td height="25" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>City </b></span><span style="font-size:11px;">'.$order->getShippingAddress()->getCity().'</span></td>';
							$html .= '<td height="25" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Prov </b></span><span style="font-size:11px;">'.$order->getShippingAddress()->getRegion().'</span></td>';
						$html .= '</tr>';
						
						$html .= '<tr>';
							$html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Postal/ZIP Code</b> </span><span style="font-size:11px;">'.$order->getShippingAddress()->getPostcode().'</span></td>';
						$html .= '</tr>';
						
						$html .= '<tr>';
							$html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Country</b> </span><span style="font-size:11px;">'. Mage::app()->getLocale()->getCountryTranslation($order->getShippingAddress()->getCountryId()) .'</span></td>';
						$html .= '</tr>';
						
						$html .= '<tr>';
							$html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Telephone No</b> </span><span style="font-size:11px;">'.$order->getShippingAddress()->getTelephone().'</span></td>';
						$html .= '</tr>';
						
						$html .= '<tr>';
							$html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Email Address</b> </span><span style="font-size:11px;">'.$order->getShippingAddress()->getEmail().'</span></td>';
						$html .= '</tr>';
						
						$html .= '<tr>';
							$html .= '<td height="25" colspan="2" style="border-bottom:1px solid #333; vertical-align:bottom;"><span style="font-weight:900; font-size:12px;"><b>Tax ID#</b> </span><span style="font-size:11px;">'.$order->getShippingAddress()->getVatId().'</span></td>';
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
								$html .= '<th style="border:1px solid #333;">MAFTA Y/N</th>';
								$html .= '<th style="border:1px solid #333;">Unit</th>';
								$html .= '<th style="border:1px solid #333;">Unit Value</th>';
								$html .= '<th style="border:1px solid #333;">Total price</th>';
							$html .= '</tr>';						
						$html .= '</thead>';
						$html .= '<tbody>';
							foreach($order->getAllItems() as $item){
								$Product = Mage::getModel("catalog/product")->load($item->getProductId());
								$manufacturer = ($Product->getCountryOfManufacture()) ? $Product->getCountryOfManufacture() : "France";
								$Total = ($item->getQtyOrdered()*$item->getPrice());
								$html .= '<tr>';
									$html .= '<td style="border:1px solid #333;">'.$Product->getName().'</td>';
									$html .= '<td style="border:1px solid #333;">'.$manufacturer.'</td>';
									$html .= '<td style="border:1px solid #333;">N</td>';
									$html .= '<td style="border:1px solid #333;">'.$item->getQtyOrdered().'</td>';
									$html .= '<td style="border:1px solid #333;">'.$item->getPrice().'</td>';
									$html .= '<td style="border:1px solid #333;">'.$Total.'</td>';
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
							$html .= '<td><span style="font-weight:900;">Reason fo export </span><span style="border-bottom:1px solid #111;">Export reason</span></td>';
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
							
							if($order->getOrderCurrencyCode() == "USD"){
								$USD = "Y";
							}elseif($order->getOrderCurrencyCode() == "CHF"){
								$CHF = "Y";
							}elseif($order->getOrderCurrencyCode() == "GBP"){
								$GBP = "Y";
							}else{
								$EUR = "Y";
							}
							
							
							$html .= '<td>
						
							<table>
								<tr>
									<td style="padding:4px">EUR</td>
									<td style="padding:4px 8px; border:1px solid #333;">'.$EUR.'</td>
									<td style="padding:4px">USD</td>
									<td style="padding:4px 8px; border:1px solid #333;">'.$USD.'</td>
									<td style="padding:4px">CHF</td>
									<td style="padding:4px 8px; border:1px solid #333;">'.$CHF.'</td>
									<td style="padding:4px">GBP</td>
									<td style="padding:4px 8px; border:1px solid #333;">'.$GBP.'</td>
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
				$html .= '<td style="border-bottom:1px solid #333;"><span style="font-weight:900;">Date </span><span style="border-bottom:1px solid; width:200px;"></span></td>';
			$html .= '</tr>';
		$html .= '</table>';		
		$html .= '</div>';
		
		// echo $html;
		// exit;
		$this->_HtmltoPdf($html);
		
	}
	
	public function _HtmltoPdf($html = null){
		require Mage::getBaseDir("lib") . DS . "mpdf" . DS ."mpdf.php";
		
		$mpdf = new Mpdf();
		$mpdf->WriteHTML($html);
		// $mpdf->Output('filename.pdf','D');      // make it to DOWNLOAD
		$mpdf->Output();      

	}
}
?>
