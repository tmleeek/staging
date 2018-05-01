<?php

class Tatva_Attachpdf_Model_Sales_Order_Pdf_Invoice extends Tatva_Attachpdf_Model_Sales_Order_Pdf_Abstract
{
	public function getPdf($invoices = array()) {
		try{ 
			$this->_beforeGetPdf ();
			$this->_initRenderer ( 'invoice' );

			$pdf = new Zend_Pdf ( );
			$this->_setPdf ( $pdf );
			$style = new Zend_Pdf_Style ( );
			$this->_setFontBold ( $style, 10 );

			foreach ( $invoices as $invoice ) {
				if ($invoice->getStoreId ()) {
					Mage::app ()->getLocale ()->emulate ( $invoice->getStoreId () );
				}
				$page = $pdf->newPage ( Zend_Pdf_Page::SIZE_A4 );
				$pdf->pages [] = $page;

				$order = $invoice->getOrder ();

				$this->invoice = $invoice;
				$this->order = $order;

				// initialisation axe Y
				$this->y = $page->getHeight ();

				/* Add image */
				$this->insertLogo ( $page, $this->invoice->getStore () );

				/* Add address */
				$this->insertAddress ( $page, $this->invoice->getStore () );

				/* Entête facture */
				$this->insertOrderHeader ( $page );

                if(strlen($this->order->getShippingDescription ()) > 50)
                {
        		    $this->y = $this->y - 12;
                }
                else
                {
                    $this->y = $this->y - 8;
                }

				/* Adresse de livraison */
				if (! $this->order->getIsVirtual ()) {
					$this->insertShippingAddress ( $page, $this->invoice->getStore () );
				}

				/* Adresse de facturation */
				$this->insertBillingAddress ( $page, $this->invoice->getStore () );

				$this->y = $this->y - 140;

				// Liste des items
				$this->insertItems ( $page );

				// gérer le cas de la page suivante
				if ($this->y < 300) {
					$page = $this->newPage ( array (
							'table_header' => true,
							'table_bottom' => true ) );
					$this->y = $this->y - 12;
				}

				// Tableau de TVA
				$this->insertTVA ( $page );

				// Totaux
				$this->insertTotaux ( $page );

				// Conditions de règlement
				$this->insertConditions ($page, $order);

			}

			$this->_afterGetPdf ();
		}catch(Exception $e){
			Mage::logException($e);
			throw $e;
		}
		return $pdf;
	}

    protected function insertTotaux(&$page) {
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$page->setFillColor ( new Zend_Pdf_Color_Html ( '#F9E6ED' ) );
		$page->drawRectangle ( 387, $this->y - 90, self::COORD_X_RIGHT_MARGIN, $this->y );
		$page = $this->insertTotals ( $page, $this->invoice );
		$this->y = $this->y - 90 - 15;
	}

	protected function insertTotals($page, $source) {
	//echo '<pre>';print_r($this->invoice);exit;
		$order = $source->getOrder ();
        $ordercurrency = $source->getBaseCurrencyCode();
		$totals = $this->_getTotalsList ( $source );
		$this->_setContentFont ( $page );

		$xLabel = 400;
		$xValue = 500;
		$lineHeight = 14;
		$discountAmount = 0;
		$totalDue = 0;
		$taxAmount = false;

		foreach ( $totals as $total ) { 
			if($total ['source_field'] == 'subtotal') {
				//comment by nisha $amount = $source->getDataUsingMethod ( $total ['source_field'] ) + $this->invoice->getShippingAmount();
				$amount = $this->invoice->getBaseSubtotal() + $this->invoice->getBaseShippingAmount();
				if($this->invoice->getBaseDiscountAmount()){
					$discount = 0;
					foreach ( $this->invoice->getAllItems () as $item ) {
						$discount += $item->getBaseDiscountAmount();
					}
					$amount -= $discount;
				}

			} else if($total ['source_field'] == 'tax_amount') {
				$amount = $this->invoice->getBaseTaxAmount();
			}
			else if($total ['source_field'] == 'grand_total') {
				$amount = $this->invoice->getBaseGrandTotal();
			}
			


			//$amountText = $order->formatPriceTxt ( $amount );
            $amountText = Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($amount);

			if (isset ( $total ['amount_prefix'] ) && $total ['amount_prefix']) {
				$amountText = "{$total['amount_prefix']}{$amountText}";
			}
                        
			if (in_array ( $total ['source_field'], array (
					'subtotal',
					'tax_amount',
					'discount_amount',
					'grand_total' ) )) {

				$label = "";
				$write = true;
				switch ($total ['source_field']) {
					case 'subtotal' :
						$this->_setContentFont ( $page );
						$label = Mage::helper ( 'tatvasales' )->__ ( 'Total excl. VAT' ) . ' :';
						$y = $this->y - $lineHeight;
						break;
					case 'tax_amount' :
						if($amount){
							$this->_setContentFont ( $page );
							$label = Mage::helper ( 'tatvasales' )->__ ( 'VAT' ) . ' :';
							$y = $this->y - ($lineHeight * 2);
							//$taxAmount = $order->getDataUsingMethod ( $total ['source_field'] );
							$taxAmount = $this->invoice->getBaseTaxAmount();
							
						}else{
							$write = false;
						}
						break;
					case 'discount_amount' :
						if($this->invoice->getCartFixed()){
							$couponCode = $this->order->getCouponCode();
							if($couponCode && preg_match('/^'.Sqli_ChequeCadeau_Model_ChequeCadeau::PREFIX_CHQ.'/', $couponCode )){
								$label = Mage::helper('chequecadeau')->__('Gift Check');
							}else{
								$label = Mage::helper ( 'tatvasales' )->__ ( 'Discount' ) . $couponCode . ' :';
							}
							$this->_setContentFont ( $page );
							$y = $this->y - ($lineHeight * 4);
							//$discountAmount = $order->getDataUsingMethod ( $total ['source_field'] );
							$discountAmount = $this->invoice->getBaseDiscountAmount();
						}else{
							$write = false;
						}
						break;
					case 'grand_total' :
						$this->_setBigBoldRedContentFont ( $page );
						$label = Mage::helper ( 'tatvasales' )->__ ( 'Total due' ) . ' :';
						$y = $this->y - ($lineHeight * 6);
						//$totalDue = $order->getDataUsingMethod ( $total ['source_field'] );
						$totalDue = $this->invoice->getBaseGrandTotal();

						break;
					default :
						continue;
				}
				if($write){
					$value = $amountText;
					$pos = $this->getAlignRight ( $value, 490, 60, $page->getFont (), $page->getFontSize () );
					$page->drawText ( $value, $pos, $y, 'UTF-8' );
					$page->drawText ( $label, $xLabel, $y, 'UTF-8' );
					//$y -= $lineHeight;
				}
			}


		}

		if($taxAmount || !$order->getShippingAddress()){
			$this->_setBoldContentFont ( $page );
			$label = Mage::helper('tatvasales')->__('Total incl. VAT') . ' :';
			$y = $this->y - ($lineHeight * 3);

            $amountText = Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($totalDue + $discountAmount);

			$pos = $this->getAlignRight ( $amountText, 490, 60, $page->getFont (), $page->getFontSize () );
			$page->drawText ( $amountText, $pos, $y, 'UTF-8' );
			$page->drawText ( $label, $xLabel, $y, 'UTF-8' );
		}

		return $page;
	}

	protected function insertTVAold(&$page) {

		// Colonne T
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = self::COORD_X_LEFT_MARGIN;
		$y1 = $this->y - 90;
		$x2 = 46;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( 'T', $x1, $x2 - $x1, $page );

		// Colonne ss-tot ht
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = 46;
		$y1 = $this->y - 90;
		$x2 = 125;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( 'Subtotal (excl. VAT)', $x1, $x2 - $x1, $page );

		// Remise
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = 125;
		$y1 = $this->y - 90;
		$x2 = 179;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( 'Discount', $x1, $x2 - $x1, $page );

		// Colonne Port
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = 179;
		$y1 = $this->y - 90;
		$x2 = 236;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( 'Shipment', $x1, $x2 - $x1, $page );

		// Colonne %TVA
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = 236;
		$y1 = $this->y - 90;
		$x2 = 273;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( '% VAT', $x1, $x2 - $x1, $page );

		// Colonne tva
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = 273;
		$y1 = $this->y - 90;
		$x2 = 310;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( 'VAT', $x1, $x2 - $x1, $page );

		// Colonne ss-tot ht
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = 310;
		$y1 = $this->y - 90;
		$x2 = 375;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( 'Total (VAT incl.)', $x1, $x2 - $x1, $page );

		// Calcul des colonnes
		$this->insertTvaCols($page);

	}

    protected function insertTVA(&$page) {

		// Colonne T
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = self::COORD_X_LEFT_MARGIN;
		$y1 = $this->y - 90;
		$x2 = 46;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( 'T', $x1, $x2 - $x1, $page );

		// Colonne ss-tot ht
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = 46;
		$y1 = $this->y - 90;
		$x2 = 125;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( 'Subtotal (excl. VAT)', $x1, $x2 - $x1, $page );



		// Remise
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = 125;
		$y1 = $this->y - 90;
		$x2 = 165;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( 'Discount', $x1, $x2 - $x1, $page );

		// Colonne Port
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = 165;
		$y1 = $this->y - 90;
		$x2 = 210;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( 'Shipment', $x1, $x2 - $x1, $page );

		//deee

		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = 210;
		$y1 = $this->y - 90;
		$x2 = 250;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( 'DEEE', $x1, $x2 - $x1, $page );

		// Colonne %TVA
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = 250;
		$y1 = $this->y - 90;
		$x2 = 280;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( '% VAT', $x1, $x2 - $x1, $page );

		// Colonne tva
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = 280;
		$y1 = $this->y - 90;
		$x2 = 328;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( 'VAT', $x1, $x2 - $x1, $page );

		// Colonne ss-tot ht
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = 328;
		$y1 = $this->y - 90;
		$x2 = 384;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( 'Total excl. VAT', $x1, $x2 - $x1, $page );

		// Calcul des colonnes
		$this->insertTvaCols($page);

	}

	protected function insertTvaCols(&$page) {
		$tvaDetail = array ();
		foreach ( $this->invoice->getAllItems () as $item ) {
			$orderItem = $item->getOrderItem ();
			$codeTva = 't' . ($orderItem->getTaxPercent () * 1);
			$tvaDetail [$codeTva] = array (
					'sstotht' => 0,
					'rem' => 0,
					'port' => 0,
					'pTva' => 0,
					'tva' => 0,
					'totht' => 0 );
		}
		if ($this->invoice->getShippingAmount () > 0) {
			$codeTva = 't' . ($this->order->getPercentTaxShipping () * 1);
			$tvaDetail [$codeTva] = array (
					'sstotht' => 0,
					'rem' => 0,
					'port' => 0,
					'pTva' => 0,
					'tva' => 0,
					'totht' => 0 );
		}

		foreach ( $this->invoice->getAllItems () as $item ) {
            $orderItem = $item->getOrderItem ();
            $tax_info = $this->order->getFullTaxInfo();

            $_product = Mage::getModel('catalog/product')->load($orderItem->getProductId());
			$weee_amt = Mage::helper('weee')->getAmountForDisplayInclTaxes($_product);
			$weee_amt_without_tax = Mage::helper('weee')->getAmount($_product);
            $weee_amt = $weee_amt * $orderItem->getQtyOrdered();
			$codeTva = 't' . ($orderItem->getTaxPercent () * 1);

			$tvaDetail [$codeTva] ['pTva'] = $orderItem->getTaxPercent ();
		   
			$tvaDetail [$codeTva] ['sstotht'] = $tvaDetail [$codeTva] ['sstotht'] + $item->getBaseRowTotal ();
            $weee_amt = $weee_amt * $orderItem->getQtyOrdered();
            $item_amt = $orderItem->getBaseRowTotal() - $weee_amt;
          
	         $tvaDetail [$codeTva] ['totht'] = $tvaDetail [$codeTva] ['totht'] + $item->getBaseRowTotal ()  - $orderItem->getBaseDiscountAmount ();
            $tvaDetail [$codeTva] ['totht'] = $tvaDetail [$codeTva] ['totht'] + $this->invoice->getBaseShippingAmount ();
			$tvaDetail [$codeTva] ['rem'] = $tvaDetail [$codeTva] ['rem'] + $item->getBaseDiscountAmount ();
            $tvaDetail [$codeTva] ['port'] = $this->invoice->getBaseShippingAmount ();
            $tvaDetail [$codeTva] ['tva'] = $tvaDetail [$codeTva] ['tva'] + $item->getBaseTaxAmount ();
			
		    $tvaShipping = $this->invoice->getBaseShippingAmount () * $orderItem->getTaxPercent () / 100;
			$tvaDetail [$codeTva] ['tva'] = $tvaDetail [$codeTva] ['tva'] + $tvaShipping;
            $tvaDetail [$codeTva] ['weee'] = $tvaDetail [$codeTva] ['weee'] + $weee_amt_without_tax;
             
		}

         $tvaDetail [$codeTva] ['totht'] = $this->invoice->getBaseSubtotal() + $this->invoice->getBaseShippingAmount();
				if($this->invoice->getBaseDiscountAmount()){
					$discount = 0;
					foreach ( $this->invoice->getAllItems () as $item ) {
						$discount += $item->getBaseDiscountAmount();
					}
					$tvaDetail [$codeTva] ['totht'] -= $discount;
				}
       $tvaDetail [$codeTva] ['tva']= $this->invoice->getBaseTaxAmount();
		// Remplissage des lignes
		$this->_setContentFont ( $page );
		$_curY = $this->y - 16;
		$lineHeight = 12;
		$cpt = 1;
        $ordercurrency = $this->invoice->getBaseCurrencyCode();
		foreach ( $tvaDetail as $tvaItem ) {
			$_curY -= $lineHeight;
			$this->insertTVAValue ( $cpt ++, self::COORD_X_LEFT_MARGIN, $_curY, 46 - self::COORD_X_LEFT_MARGIN, $page );
		   	if ($tvaItem ['sstotht'])
                $this->insertTVAValue(Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($tvaItem ['sstotht']), 46, $_curY, 125 - 46, $page );

			if ($tvaItem ['rem'])
                $this->insertTVAValue (Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($tvaItem ['rem']), 125, $_curY, 165 - 125, $page );

			if ($tvaItem ['port'])
                $this->insertTVAValue (Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($tvaItem ['port']), 165, $_curY, 210 - 165, $page );

			if ($tvaItem ['weee'])
                $this->insertTVAValue ( Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($tvaItem ['weee']), 210, $_curY, 250 - 210, $page );

			if ($tvaItem ['pTva'])
                            
				$this->insertTVAValue ( number_format ( $tvaItem ['pTva'], 2 ), 250, $_curY, 280 - 250, $page );
                            
			if ($tvaItem ['tva'])
                $this->insertTVAValue (Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($tvaItem ['tva']), 280, $_curY, 330 - 280, $page );

			if ($tvaItem ['totht'])
                $this->insertTVAValue (Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($tvaItem ['totht']), 330, $_curY, 384 - 330, $page );

                break;
		}
	}

	private function insertTVATitle($string, $x, $width, &$page) {
		$this->_setSmallTitleFont ( $page );
		$string = Mage::helper ( 'tatvasales' )->__ ( $string );
		$pos = $this->getAlignCenter ( $string, $x, $width, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $this->y - 12, 'UTF-8' );
	}

	private function insertRedTVAHeader($x1, $y1, $x2, $y2, &$page) {
		$x1 = $x1 + 0.5;
		$y1 = $this->y - 16;
		$x2 = $x2 - 0.5;
		$y2 = $y2 - 0.5;
		$page->setFillColor ( new Zend_Pdf_Color_Html ( '#C30245' ) );
		$page->drawRectangle ( $x1, $y1, $x2, $y2 );
	}

	protected function insertTVAValue($string, $x, $y, $width, &$page) {
		$pos = $this->getAlignCenter ( $string, $x, $width, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $y, 'UTF-8' );
	}

	protected function insertItems(&$page) {
		$this->insertHeaderItems ( $page );
		$heightLine = 27;
		foreach ( $this->invoice->getAllItems () as $item ) {

			if ($item->getOrderItem ()->getParentItem ()) {
				continue;
			}

			if ($this->y < $this->minY) {
				$page = $this->newPage ( array (
						'table_header' => true,
						'item_header' => true,
						'table_bottom' => true ) );
			}

			// Ajout d'une ligne d'item
			$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
			$page->setLineWidth ( 0.5 );
			$page->setLineDashingPattern ( array (
					1,
					1 ) );

			// Ajout de l'item
			$heightLine = $this->addItem ( $page, $item );
			$heightLine += 10;

			$x1 = self::COORD_X_LEFT_MARGIN;
			$y1 = $this->y - $heightLine;
			$x2 = self::COORD_X_RIGHT_MARGIN;
			$y2 = $this->y;
			$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );



			// Mise à jour de l'axe Y
			$this->y = $this->y - $heightLine;

		}
		$page->setLineDashingPattern ( Zend_Pdf_Page::LINE_DASHING_SOLID );
		$this->y = $this->y - 10;
	}

	public function newPage(array $settings = array()) {
		/* Add new table head */
		$page = $this->_getPdf ()->newPage ( Zend_Pdf_Page::SIZE_A4 );
		$this->_getPdf ()->pages [] = $page;
		$this->y = $page->getHeight ();

		$store = false;
		if($this->invoice){
			try {
				$store = $this->invoice->getStore ();
			}catch(Exception $e){
				$store = false;
			}
		}
		if(!$store){
			$store = Mage::app()->getStore();
		}

		if (! empty ( $settings ['table_header'] )) {
			//$this->insertLogo ( $page, $this->invoice->getStore () );
			$this->insertLogo ( $page, $store );
			$this->insertOrderHeader ( $page );
		}

		if (! empty ( $settings ['item_header'] )) {
			$this->y = $this->y - 8;
			$this->insertHeaderItems ( $page );
		}

		if (! empty ( $settings ['table_bottom'] )) {
			//$this->insertAddress ( $page, $this->invoice->getStore () );
			$this->insertAddress ( $page, $store );
		}

		return $page;
	}

	public function getTailCollection($prd_id) {

	  	$storeId = Mage::app()->getStore()->getId();
		$productEntityTypeId = Mage::getModel ( 'eav/entity' )->setType ( 'catalog_product' )->getTypeId ();
		$brandAttribute = Mage::getModel('eav/entity_attribute')->loadByCode($productEntityTypeId,'taille');
		$product = Mage::getModel('catalog/product')->load($prd_id);

	  $brands = Mage::getModel('eav/entity_attribute_option')->getCollection()
					->addFieldToFilter('attribute_id',array('='=>$brandAttribute->getAttributeId()))
					->setIdFilter($product->getTaille())
					->setStoreFilter($storeId, false)
					->load();

			return $brands->getFirstItem();
	}

	public function getMarqueCollection($prd_id) {

	  	$storeId = Mage::app()->getStore()->getId();
		$productEntityTypeId = Mage::getModel ( 'eav/entity' )->setType ( 'catalog_product' )->getTypeId ();
		$brandAttribute = Mage::getModel('eav/entity_attribute')->loadByCode($productEntityTypeId,'marque');
		$product = Mage::getModel('catalog/product')->load($prd_id);
	  $brands = Mage::getModel('eav/entity_attribute_option')->getCollection()
					->addFieldToFilter('attribute_id',array('='=>$brandAttribute->getAttributeId()))
					->setIdFilter($product->getMarque())
					->setStoreFilter($storeId, false)
					->load();

			return $brands->getFirstItem();
	}



	protected function addItem($page, $item) {//echo '<pre>';print_r($item->getData());exit;
		$_height = 6;
		$sep = '';
		$reference = array();
		$marqueEtCollection = array();
        $ordercurrency = $this->invoice->getBaseCurrencyCode();
		//Designation
		$name = $item->getName ();
		$name = utf8_decode($name);
		$sizeName = 40;

		if(strlen($name) > $sizeName){
			$tabName = split(" ", $name);
			$result = "";
			foreach($tabName as $value){
				$size = strlen($value) + strlen($result);
				if( $size > $sizeName ){
					$reference[] = $result;
					$result = $value . " ";
				}elseif( $size == $sizeName){
					$result .= $value;
					$reference[] = $result;
					$result = "";
				}else{
					$result .= $value . " ";
				}
			}
			if(strlen($result) > 0){
				$reference[] = $result;
			}


		}else{
			$reference[] = $name;
		}



		//Marque et collection
		/*$string = false;
		$string = utf8_decode($string);
		if($string && strlen($string) > 40){
			$deb = 0;
			$fin = 40;
			$str = substr($string, $deb, $fin);

			while($str){
				$marqueEtCollection[] = $str;

				$deb = $fin + 1;
				$fin = $fin * 2 + 1;
				$str = substr($string, $deb, $fin);
			}
		}else{
			if($string){
				$marqueEtCollection[] = $string;
			}
		}*/

		$string = false;
            $prd = Mage::getModel('catalog/product')->load($item->getProductId());

            if($prd['gamme_collection_new'])
            {
            $p = $prd['gamme_collection_new'];
			$attr = $prd->getResource()->getAttribute("gamme_collection_new");
			$colls = $attr->getSource()->getOptionText($p);
               if($string){
					$string .= " - ";
				}
               $string .= $colls;
            }

            if($prd->getManufacturer())
            {
            $p = $prd->getManufacturer();
			$attr = $prd->getResource()->getAttribute("manufacturer");
			$marque = $attr->getSource()->getOptionText($p);
               if($string){
					$string .= " - ";
				}
               $string .= $marque;
            }



		$string = utf8_decode($string);
		if($string && strlen($string) > 40){
			$deb = 0;
			$fin = 40;
			$str = substr($string, $deb, $fin);

			while($str){
				$marqueEtCollection[] = $str;

				$deb = $fin + 1;
				$fin = $fin * 2 + 1;
				$str = substr($string, $deb, $fin);
			}
		}else{
			if($string){
				$marqueEtCollection[] = $string;
			}
		}

		$sizeMarqueEtCollection = sizeof($marqueEtCollection);

		$_heightLine = (10 + (sizeof($reference) + $sizeMarqueEtCollection  ) * 6);
		$baseY = $this->y - ( ($_heightLine + ($_height * ( 1 + sizeof($reference) * 0.6 + $sizeMarqueEtCollection * 0.5) ) ) / 2 ) - 2;
		if(! sizeof($marqueEtCollection)){
			$baseY = $baseY - 1;
			if(sizeof($reference) == 1){
				$baseY = $baseY - 1.5;
			}
		}

		// Référence
		$this->_setContentFont ( $page );
		$string = $this->getSku ( $item );
		$page->drawText ( $string, 34, $baseY, 'UTF-8' );

		// Désignation
		if(! sizeof($marqueEtCollection)){
			if(sizeof($reference) == 1){
				$_y = $this->y - ($_height * ( 1.8 + sizeof($reference) ));
			}else{
				$_y = $this->y - ($_height * ( 1 + sizeof($reference) * 0.6)) - 3;
			}
		}elseif(sizeof($marqueEtCollection) == 1 && sizeof($reference) == 1){
				$_y = $this->y - $_height - 10;
		}else{
			$_y = $this->y - ($_height * ( 1 + sizeof($reference) * 0.6 + $sizeMarqueEtCollection * 0.5));
		}

		foreach($reference as $value){
			$this->_setRedContentFont ( $page );
			$page->drawText ( utf8_encode($value), 114, $_y, 'UTF-8' );
			$_y -= $_height;
		}

		//Marque et collection
		$_y -= 2;
		foreach($marqueEtCollection as $value){
			$this->_setSmallContentFont ( $page );
			$page->drawText ( utf8_encode($value) , 114, $_y, 'UTF-8' );
			$_y -= $_height;
		}

		// Options
		/*$options = $this->getItemOptions ( $item );
		if ($options) {
			$_height = 7;
			$_y = $this->y - $_height;
			switch (count ( $options )) {
				case 1 :
					$_y = $this->y - $_height * 2.5;
					break;
				case 2 :
					$_y = $this->y - $_height * 2;
					break;
				case 3 :
					$_y = $this->y - $_height * 1.5;
					break;
			}
			$this->_setSmallContentFont ( $page );

			foreach ( $options as $option ) {
				if ($option ['label']) {
					if ($option ['value']) {
						$_printValue = isset ( $option ['print_value'] ) ? $option ['print_value'] : strip_tags ( $option ['value'] );
						$values = (is_array ( $_printValue ) ? explode ( ', ', $_printValue ) : $_printValue);
					}
					$string = $option ['label'] . ($values ? ' : ' . $values : '');
					$pos = $this->getAlignCenter ( $string, 246, 332 - 246, $page->getFont (), $page->getFontSize () );
					$page->drawText ( $string, $pos, $_y, 'UTF-8' );
					$_y -= $_height;
				}
			}
		}*/

                    $string = number_format ( $item->getOrderItem ()->getTaxPercent (), 2 );
					$pos = $this->getAlignCenter ( $string, 246, 332 - 246, $page->getFont (), $page->getFontSize () );
					$page->drawText ( $string, $pos, $baseY, 'UTF-8' );

             $item_row_total='';  $item_price='';
             $_product = Mage::getModel('catalog/product')->load($item->getProductId());
             $weee_amt = Mage::helper('weee')->getAmountForDisplayInclTaxes($_product);
             
    
            $taxrated = $this->order->getFullTaxInfo();
            $taxammount = $taxrated[0]["percent"]/100;
            $finaltax = $item->getBasePrice()*$taxammount;
             //$final_bas_price = $item->getBasePrice()+$item->getBaseTaxAmount() + $weee_amt; 
             $final_bas_price = $item->getBasePrice()+$finaltax;
             $item_price = number_format($final_bas_price,2);

          	 $item_row_total_temp = number_format($item->getBaseRowTotal(),2) + $item->getBaseTaxAmount() +$weee_amt;
          	 $item_row_total = number_format($item_row_total_temp,2);


		// Tax
		$this->_setRedContentFont ( $page );
        $string = Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($item_price);
                
		$pos = $this->getAlignCenter ( $string, 332, 385 - 332, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $baseY, 'UTF-8' );
		// Unit price
        $string = Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($weee_amt);
		
		$pos = $this->getAlignCenter ( $string, 385, 456 - 385, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $baseY, 'UTF-8' );
		// qty
		$string = $item->getQty () * 1;
		$pos = $this->getAlignCenter ( $string, 456, 489 - 456, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $baseY, 'UTF-8' );
		// total ht
        $string = Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($item_row_total);
        
		$pos = $this->getAlignCenter ( $string, 489, 561 - 489, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $baseY, 'UTF-8' );

		return $_heightLine;
	}

	protected function insertHeaderItems(&$page) {
		// Entête du tableau
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setFillColor ( new Zend_Pdf_Color_Html ( '#C30245' ) );
		$page->setLineWidth ( 0.5 );
		$x1 = self::COORD_X_LEFT_MARGIN;
		$y1 = $this->y - 16;
		$x2 = self::COORD_X_RIGHT_MARGIN;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2 );
		// Labels
		$y = $this->y - 11;
		$this->_setSmallTitleFont ( $page );
		// Référence
		$string = Mage::helper ( 'tatvasales' )->__ ( 'Ref.' );
		$pos = $this->getAlignCenter ( $string, 34, 110 - 34, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $y, 'UTF-8' );
		// Désignation
		$string = Mage::helper ( 'tatvasales' )->__ ( 'Item name' );
		$pos = $this->getAlignCenter ( $string, 110, 246 - 110, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $y, 'UTF-8' );
		// Options
		$string = Mage::helper ( 'tatvasales' )->__ ( '% Tax' );
		$pos = $this->getAlignCenter ( $string, 246, 332 - 246, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $y, 'UTF-8' );
		// Tax
		$string = Mage::helper ( 'tatvasales' )->__ ( 'Unit Price (VAT incl.)' );
		$pos = $this->getAlignCenter ( $string, 332, 385 - 332, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $y, 'UTF-8' );
		// Unit price
		$string = Mage::helper ( 'tatvasales' )->__ ( 'Eco-tax (VAT incl.)' );
		$pos = $this->getAlignCenter ( $string, 385, 456 - 385, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $y, 'UTF-8' );
		// qty
		$string = Mage::helper ( 'tatvasales' )->__ ( 'Qty' );
		$pos = $this->getAlignCenter ( $string, 456, 489 - 456, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $y, 'UTF-8' );
		// total ht
		$string = Mage::helper ( 'tatvasales' )->__ ( 'Total (VAT incl.)' );
		$pos = $this->getAlignCenter ( $string, 489, 561 - 489, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $y, 'UTF-8' );
		$this->y = $this->y - 16;
	}

	protected function insertOrderHeader(&$page) {
		// Récupération du client
		$customer = Mage::getModel ( 'customer/customer' )->load ( $this->order->getCustomerId () );
		// Conteneur Grand rectangle
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = self::COORD_X_LEFT_MARGIN;
        if(strlen($this->order->getShippingDescription ()) > 50)
        {
		    $y1 = $this->y - 64;
        }
        else
        {
            $y1 = $this->y - 64;
        }
		$x2 = self::COORD_X_RIGHT_MARGIN;
		$y2 = $this->y - 8;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		// Rectangle de titre
		$x1 = self::COORD_X_LEFT_MARGIN + 0.5;
		$y1 = $this->y - 22;
		$x2 = self::COORD_X_RIGHT_MARGIN - 0.5;
		$y2 = $this->y - 8.5;
		$page->setFillColor ( new Zend_Pdf_Color_Html ( '#C30245' ) );
		$page->drawRectangle ( $x1, $y1, $x2, $y2 );
		// Titre
		$this->_setTitleFont ( $page );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'INVOICE %s', $this->invoice->getIncrementId () ), 34, $this->y - 19, 'UTF-8' );
		// Contenu
		$this->_setBoldContentFont ( $page );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Invoice date : %s', Mage::helper ( 'core' )->formatDate ( $this->invoice->getCreatedAtStoreDate (), 'medium', false ) ), 34, $this->y - 33, 'UTF-8' );
		if (!$this->order->getCustomerIsGuest()) {
			$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Customer %s', $customer->getIncrementId () ), 215, $this->y - 33, 'UTF-8' );
		}
		$this->_setContentFont ( $page );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Order %s', $this->order->getIncrementId () ), 34, $this->y - 45, 'UTF-8' );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Order date : %s', Mage::helper ( 'core' )->formatDate ( $this->order->getCreatedAtStoreDate (), 'medium', false ) ), 34, $this->y - 57, 'UTF-8' );
        //if(strlen($this->order->getShippingDescription ()) > 50)
        //{
          //  $page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Shipping mode : %s', $this->order->getShippingDescription () ), 200, $this->y - 45, 'UTF-8' );
           // $page->drawText ( substr($this->order->getShippingDescription (),50) , 380, $this->y - 57, 'UTF-8' );
        //}
        //else
       // {
            $page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Shipping mode : %s', html_entity_decode(utf8_encode($this->order->getShippingDescription ())) ), 215, $this->y - 45);
       // }
        $this->_getInfoPayment ( $page );
		$this->y = $this->y - 64;
	}

    protected function insertConditions($page, $order)
    {
		//Add tax sentence for PDF invoice
		$store = $order->getStoreId ();
		$taxSentenceConfig = unserialize(Mage::getStoreConfig ('sales/tax_sentences/tax_sentences', $store ));
		if ($order->getShippingAddress())
        {
			$countryId = $order->getShippingAddress()->getCountry();
			$address = $order->getShippingAddress();
		}
        else
        {
			$countryId = $order->getBillingAddress()->getCountry();
			$address = $order->getBillingAddress();
		}

		$area = Mage::getModel('tatvashipping/area')->getCollection()
					->addCountriesToResult()
					->joinCountries(array("'".$countryId."'"))
					->clear();
					//->getFirstItem();

        //echo $countryId;
        //echo $area->getSelect();
        //exit;

        $tax_amount = $order->getTaxAmount();

		if ($area && is_array($taxSentenceConfig) && $tax_amount<=0)
        {
			$sentence = '';
             //echo "4242424";
			//Si on a une adresse
			if(is_object($address))
            {
              //echo "12323";
				foreach ($taxSentenceConfig as $elmt)
                {
				    foreach($area as $subarea)
                    {
                        //var_dump($elmt['areas']);

                    //echo $subarea->getAreaId();
                    //echo "<br />";
                        if (in_array($subarea->getAreaId(),$elmt['areas']))
                        {
					        $sentence = $elmt['sentence'];
						    break;
					    }
                    }
				}
            }
		}
        else
        {
			$sentence = '';
		}
        //echo $sentence;
        //exit;
                
		if($sentence) {
			$this->_setItalicContentFont ( $page );
			$this->y = $this->y + 3;

			$page->drawText ($sentence, self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );
			$this->y = $this->y -20;
		} else {
			$this->y = $this->y - 8;
		}

		$this->_setBoldContentFont ( $page );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( "PAYMENT RULES :" ), self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );
		$this->y = $this->y - 8;
		// Mode de paiement
		$lineHeight = 10;
		$this->_setContentFont ( $page );
		$this->y = $this->y - $lineHeight;
		$payment = $this->_getInfoValues ();
		foreach ( $payment as $value ) {
			if (trim ( $value ) !== '') {
				if(strtoupper ( rtrim ($value) ) == "PARTENAIRE"){
					$value = $this->order->getPayment ()->getMarketplacesPartnerCode();
				}

				$page->drawText ( strip_tags ( strtoupper(trim ( $value )) ), self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );
				$this->y = $this->y - $lineHeight;
			}
		}
		//$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( "Date of payment to date of billing" ), self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( "MATURITY AT BILLING DATE" ), self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );

		$this->y = $this->y - $lineHeight;

		// Retard
		$this->_setSmallContentFont($page);
		$lineHeight = 8;
		$store = $this->order->getStore ();
		$rules = Mage::getStoreConfig ( 'sales/identity/payment_rules', $store );
		$rules ? trim($rules) : '';
		if ($rules) {
			$tb = explode ( "\n", $rules );
			foreach ($tb as $itemTb) {
				$this->y = $this->y - $lineHeight;
				$page->drawText ( $itemTb, self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );
			}
		}
		$this->y = $this->y - 5;

		// Conditions
		$this->_setSmallSilverContentFont($page);
		$lineHeight = 8;
		$store = $this->order->getStore ();
		$rules = Mage::getStoreConfig ( 'sales/identity/conditions', $store );
		$rules ? trim($rules) : '';
		if ($rules) {
			$tb = explode ( "\n", $rules );
			foreach ($tb as $itemTb) {
				$this->y = $this->y - $lineHeight;
				$page->drawText ( $itemTb, self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );
			}
		}
		$this->y = $this->y - 5;

	}
}