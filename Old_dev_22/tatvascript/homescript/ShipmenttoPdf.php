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
		// $shipmentIncrementId = 100047385;
		$shipmentIncrementId = 100040201;		
		
		$shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);
		$orderIncrementId = $shipment->getOrder()->getIncrementId();		
		$order = Mage::getModel("sales/order")->loadByIncrementId($orderIncrementId);
				
		// echo "<pre>";
		// foreach($order->getAllItems() as $item){
			// $product = Mage::getModel("catalog/product")->load($item->getProductId());
			// print_r($item->getData());
		// }
		
		// exit;
		
		
		$html = '';
	
		$html .= '<table style="width:100%; font-size:40px;" border="1">';
			$html .= '<tr>';
				$html .= '<td height="20px" colspan="2"></td>';
			$html .= '</tr>';

			$html .= '<tr>';
				$html .= '<td width="50%" style="text-align:center; font-size:80px; "> FRANCE </td>';
				$html .= '<td width="50%" style="text-align:center; font-size:80px;"> Declaration on Deu CN 23 </td>';
			$html .= '</tr>';

			$html .= '<tr>';
				$html .= '<td height="5px" colspan="2"></td>';
			$html .= '</tr>';

			$html .= '<tr>';
				$html .= '<td width="50%">';
					$html .= '<table border="1">';
						$html .= '<tr>';
							$html .= '<td rowspan="6" style="width:5%;">Expediteur</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td><span style="font-weight:900; "><b>Name</b> </span><span style="">AZ-Boutique</span> </td>';
							$html .= '<td rowspan="2"> Procedure Simplify Postal code </td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td><span style="font-weight:900; "><b>Soiciety</b> </span><span style="">AZ-Boutique</span></td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td colspan="2"><span style="font-weight:900; "><b>Address</b> </span><span style="">45 RUE GILLES ROBERVAL</span></td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td><span style="font-weight:900; "><b>Code Postal</b> </span><span style="">30900</span></td>';
							$html .= '<td><span style="font-weight:900; "><b>Ville</b> </span><span style="">NIMES</span></td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td colspan="2"> France </td>';
						$html .= '</tr>';


						$html .= '<tr>';
							$html .= '<td rowspan="6" text-rotate="90">Expediteur</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td colspan="2"><span style="font-weight:900; "><b>Nom</b> </span><span style="">'.$order->getShippingAddress()->getFirstname().' '.$order->getShippingAddress()->getLastname().'</span></td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td colspan="2"><span style="font-weight:900; "><b>Soiciety</b> </span><span style="">'.$order->getShippingAddress()->getCompany().'</span></td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td colspan="2"><span style="font-weight:900; "><b>Address</b> </span><span style="">';
								foreach($order->getShippingAddress()->getStreet() as $address){
									$html .= $address;
								}								
							$html .= '</span></td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td><span style="font-weight:900; "><b>Code Postal</b> </span><span style="">'.$order->getShippingAddress()->getPostcode().'</span></td>';
							$html .= '<td><span style="font-weight:900; "><b>Ville</b> </span><span style="">'.$order->getShippingAddress()->getCity().'</span></td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td colspan="2"><span style="font-weight:900; "><b>PAYS</b> </span><span style="">'. Mage::app()->getLocale()->getCountryTranslation($order->getShippingAddress()->getCountryId()) .'</span></td>';
						$html .= '</tr>';
					$html .= '</table>';
				$html .= '</td>';

				$html .= '<td width="50%" valign="bottom">';

					$html .= '<table border="1" width="100%">';
						$html .= '<tr>';
							$html .= '<td>Expediteur</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td>Reference de</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td><p>N de telep</p>';
								$html .= '<span style="font-weight:900; "><b>Tel</b> </span><span style="">'.$order->getShippingAddress()->getTelephone().'</span>';
								$html .= '<span style="font-weight:900; "><b>Email</b> </span><span style="">'.$order->getShippingAddress()->getEmail().'</span>';
							$html .= '</td>';
						$html .= '</tr>';
					$html .= '</table>';
				$html .= '</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td colspan="5">';
					$html .= '<table border="1" width="100%">';
						$html .= '<tr>';
							$html .= '<td rowspan="14" colspan="5" text-rotate="90">Expediteur</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td rowspan="2">Description</td>';
							$html .= '<td rowspan="2">Qnty</td>';
							$html .= '<td rowspan="2">Poids</td>';
							$html .= '<td rowspan="2">Value</td>';
							$html .= '<td colspan="2">por tes invoice</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td> 1 </td>';
							$html .= '<td> 1 </td>';
						$html .= '</tr>';
						$weight = 0;
						foreach($order->getAllItems() as $item){
							$Product = Mage::getModel("catalog/product")->load($item->getProductId());
							$manufacturer = ($Product->getCountryOfManufacture()) ? $Product->getCountryOfManufacture() : "France";
							$Total = ($item->getQtyOrdered()*$item->getPrice());							
							$weight += $Product->getweight();
							
							$html .= '<tr>';
								$html .= '<td>'.$Product->getName().'</td>';
								$html .= '<td>'.$item->getQtyOrdered().'</td>';
								$html .= '<td>'.$Product->getweight().'</td>';
								$html .= '<td>'.$item->getPrice().'</td>';
								$html .= '<td> - </td>';
								$html .= '<td>'.$manufacturer.'</td>';
							$html .= '</tr>';
						}
						
						$html .= '<tr>';
							$html .= '<td> Total desc 1</td>';
							$html .= '<td> TotalQty  1</td>';
							$html .= '<td>Total Poids '.$weight.'</td>';
							$html .= '<td>Total Value '.$order->getSubtotal().'</td>';
							$html .= '<td colspan="2">Shipping cost '.$order->getShippingAmount().'</td>';
						$html .= '</tr>';

						$html .= '<tr>';
							$html .= '<td colspan="4">';
								$html .= '<table border="1"  width="100%">';
									$html .= '<tr>';
										$html .= '<td width="33.33%"> 1 </td>';
										$html .= '<td width="33.33%"> 1</td>';
										$html .= '<td width="33.33%" rowspan="3"> 1</td>';
									$html .= '</tr>';
									
									$html .= '<tr>';
										$html .= '<td width="33.33%"> 2 </td>';
										$html .= '<td width="33.33%"> 2 </td>';
									$html .= '</tr>';
									
									$html .= '<tr>';
										$html .= '<td width="33.33%"> 3 </td>';
										$html .= '<td width="33.33%"> 3 </td>';
									$html .= '</tr>';
								$html .= '</table>';
							$html .= '</td>';
							
							$html .= '<td colspan="2"> Origin Date </td>';
						$html .= '</tr>';
						
						
						$html .= '<tr>';
							$html .= '<td colspan="4"> Observations </td>';
							$html .= '<td colspan="2" rowspan="2"> Sugnature Date </td>';
						$html .= '</tr>';

						$html .= '<tr>';
							$html .= '<td colspan="4">';
								$html .= '<table border="1"  width="100%">';
									$html .= '<td width="33.33%">  Liecence </td>';
									$html .= '<td width="33.33%"> Certificat </td>';
									$html .= '<td width="33.33%"> Facture </td>';
								$html .= '</table>';
							$html .= '</td>';
						$html .= '</tr>';
					$html .= '</table>';
				$html .= '</td>';
			$html .= '</tr>';
		$html .= '</table>';
		
		//echo $html;
		//exit;
		$this->_HtmltoPdf($html);
		
	}
	
	public function _HtmltoPdf($html = null){
		require Mage::getBaseDir("lib") . DS . "mpdf" . DS ."mpdf.php";
		
		$mpdf = new Mpdf('c', 'A4-L', 100,'',5,5,5,5,5,5,'L');
		 /*
		 $mpdf = new mPDF('',    // mode - default ''
		 '',    // format - A4, for example, default ''
		 0,     // font size - default 0
		 '',    // default font family
		 15,    // margin_left
		 15,    // margin right
		 16,     // margin top
		 16,    // margin bottom
		 9,     // margin header
		 9,     // margin footer
		 'L');  // L - landscape, P - portrait		
		*/ 
		//$mpdf->SetTitle('My title');
		$mpdf->WriteHTML($html);
		$mpdf->Output('filename.pdf','I');      // make it to DOWNLOAD
		// $mpdf->Output();      

	}
}
?>
