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
		$html .='
		<style>
			td{ min-height:20px; }
		</style>		
		';
		$html .= '<table style="width:100%; font-size:100px;" border="1">';
			$html .= '<tr>';
				$html .= '<td height="20px" colspan="2"></td>';
			$html .= '</tr>';

			$html .= '<tr>';
				$html .= '<td width="50%" style="text-align:center;"> FRANCE </td>';
				$html .= '<td width="50%" style="text-align:center;"> Declaration on Deu CN 23 </td>';
			$html .= '</tr>';

			$html .= '<tr>';
				$html .= '<td height="5px" colspan="2"></td>';
			$html .= '</tr>';

			$html .= '<tr>';
				$html .= '<td width="50%">';
					$html .= '<table border="1">';
						$html .= '<tr>';
							$html .= '<td rowspan="6" width="5%" style="transform: rotate(-90deg);">Expediteur</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td> Name </td>';
							$html .= '<td rowspan="2"> Procedure Simplify Postal code </td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td> Soiciety  </td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td colspan="2"> Address </td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td> Code Postal </td><td> Ville </td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td colspan="2"> France </td>';
						$html .= '</tr>';


						$html .= '<tr>';
							$html .= '<td rowspan="6" width="5%" style="transform: rotate(-90deg);">Expediteur</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td colspan="2"> Name </td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td colspan="2"> Soiciety  </td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td colspan="2"> Address </td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td> Code Postal </td><td> Ville </td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td colspan="2"> France </td>';
						$html .= '</tr>';
					$html .= '</table>';
				$html .= '</td>';

				$html .= '<td width="50%">';

					$html .= '<table border="1">';
						$html .= '<tr>';
							$html .= '<td>Expediteur</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td>Reference de</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td>N de telep</td>';
						$html .= '</tr>';
					$html .= '</table>';
				$html .= '</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td colspan="5">';
					$html .= '<table border="1" width="100%">';
						$html .= '<tr>';
							$html .= '<td rowspan="14" width="5%" style="transform: rotate(-90deg);">Expediteur</td>';
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

						$html .= '<tr>';
							$html .= '<td> desc 1</td>';
							$html .= '<td> Qty  1</td>';
							$html .= '<td> poids  1</td>';
							$html .= '<td> value   1</td>';
							$html .= '<td> - </td>';
							$html .= '<td> - </td>';
						$html .= '</tr>';

						$html .= '<tr>';
							$html .= '<td> desc 1</td>';
							$html .= '<td> Qty  1</td>';
							$html .= '<td> poids  1</td>';
							$html .= '<td> value   1</td>';
							$html .= '<td> - </td>';
							$html .= '<td> - </td>';
						$html .= '</tr>';

						$html .= '<tr>';
							$html .= '<td> desc 1</td>';
							$html .= '<td> Qty  1</td>';
							$html .= '<td> poids  1</td>';
							$html .= '<td> value   1</td>';
							$html .= '<td> - </td>';
							$html .= '<td> - </td>';
						$html .= '</tr>';

						$html .= '<tr>';
							$html .= '<td> desc 1</td>';
							$html .= '<td> Qty  1</td>';
							$html .= '<td> poids  1</td>';
							$html .= '<td> value   1</td>';
							$html .= '<td> - </td>';
							$html .= '<td> - </td>';
						$html .= '</tr>';

						$html .= '<tr>';
							$html .= '<td> Total desc 1</td>';
							$html .= '<td> TotalQty  1</td>';
							$html .= '<td> Total poids  1</td>';
							$html .= '<td> Total value   1</td>';
							$html .= '<td colspan="2"> - </td>';
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
		 
		$mpdf->WriteHTML($html);
		// $mpdf->Output('filename.pdf','D');      // make it to DOWNLOAD
		$mpdf->Output();      

	}
}
?>
