<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Order Creditmemo PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Tatva_Attachpdf_Model_Sales_Order_Pdf_Creditmemo extends Tatva_Attachpdf_Model_Sales_Order_Pdf_Invoice
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
        		    $this->y = $this->y - 22;
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
				$this->insertCreditConditions ($page, $order);

			}

			$this->_afterGetPdf ();
		}catch(Exception $e){
			Mage::logException($e);
			throw $e;
		}
		return $pdf;
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
		    $y1 = $this->y - 76;
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
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Credit Memo %s', $this->invoice->getIncrementId () ), 33, $this->y - 19, 'UTF-8' );
		// Contenu
		$this->_setBoldContentFont ( $page );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Credit memo date : %s', Mage::helper ( 'core' )->formatDate ( $this->invoice->getCreatedAtStoreDate(), 'medium', false ) ), 34, $this->y - 33, 'UTF-8' );
		if (!$this->order->getCustomerIsGuest()) {
			$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Customer %s', $customer->getIncrementId () ), 215, $this->y - 33, 'UTF-8' );
		}
		$this->_setContentFont ( $page );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Order %s', $this->order->getIncrementId () ), 34, $this->y - 45, 'UTF-8' );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Order date : %s', Mage::helper ( 'core' )->formatDate ( $this->order->getCreatedAtStoreDate (), 'medium', false ) ), 34, $this->y - 57, 'UTF-8' );
		//if(strlen($this->order->getShippingDescription ()) > 50)
        //{
            //$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Shipping mode : %s', substr($this->order->getShippingDescription (),0,50) )."-", 310, $this->y - 45, 'UTF-8' );
           // $page->drawText ( substr($this->order->getShippingDescription (),50) , 380, $this->y - 57, 'UTF-8' );
        //}
        //else
        //{
            $page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Shipping mode : %s', $this->order->getShippingDescription () ), 215, $this->y - 45, 'UTF-8' );
        //}
		$this->_getInfoPayment ( $page );
		$this->y = $this->y - 64;
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
                $ordercurrency = $this->order->getOrderCurrencyCode();
		foreach ( $tvaDetail as $tvaItem ) {
			$_curY -= $lineHeight;
			$this->insertTVAValue ( $cpt ++, self::COORD_X_LEFT_MARGIN, $_curY, 46 - self::COORD_X_LEFT_MARGIN, $page );
		   	if ($tvaItem ['sstotht'])
                            if($ordercurrency!="EUR")
                            {
                                $this->insertTVAValue(" - ".Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($tvaItem ['sstotht']), 46, $_curY, 125 - 46, $page ); 
                            }
                            else
                            {
                                $this->insertTVAValue ( Mage::helper ( 'core' )->formatPrice ( - $tvaItem ['sstotht'], false ), 46, $_curY, 125 - 46, $page );
                            }
				//$this->insertTVAValue ( Mage::helper ( 'core' )->formatPrice ( - $tvaItem ['sstotht'], false ), 46, $_curY, 125 - 46, $page );
			if ($tvaItem ['rem'])
                            if($ordercurrency!="EUR")
                            {
                               $this->insertTVAValue (" - ".Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($tvaItem ['rem']), 125, $_curY, 165 - 125, $page );
                            }
                            else
                            {
				$this->insertTVAValue ( Mage::helper ( 'core' )->formatPrice ( -  $tvaItem ['rem'], false ), 125, $_curY, 165 - 125, $page );
                            }
//$this->insertTVAValue ( Mage::helper ( 'core' )->formatPrice ( -  $tvaItem ['rem'], false ), 125, $_curY, 165 - 125, $page );
			if ($tvaItem ['port'])
                             if($ordercurrency!="EUR")
                            {
                               $this->insertTVAValue (" - ".Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($tvaItem ['port']), 165, $_curY, 210 - 165, $page );
                            }
                            else
                            {
				$this->insertTVAValue ( Mage::helper ( 'core' )->formatPrice (-  $tvaItem ['port'], false ), 165, $_curY, 210 - 165, $page );
                            }
//$this->insertTVAValue ( Mage::helper ( 'core' )->formatPrice (-  $tvaItem ['port'], false ), 165, $_curY, 210 - 165, $page );

			if ($tvaItem ['weee'])
                             if($ordercurrency!="EUR")
                            {
                               $this->insertTVAValue (" - ".Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($tvaItem ['weee']), 210, $_curY, 250 - 210, $page );
                            }
                            else
                            {
			    $this->insertTVAValue ( Mage::helper ( 'core' )->formatPrice ( - $tvaItem ['weee'], false ), 210, $_curY, 250 - 210, $page );
                            }
			    //$this->insertTVAValue ( Mage::helper ( 'core' )->formatPrice ( - $tvaItem ['weee'], false ), 210, $_curY, 250 - 210, $page );


			if ($tvaItem ['pTva'])
				$this->insertTVAValue ( number_format ( $tvaItem ['pTva'], 2 ), 250, $_curY, 280 - 250, $page );
			if ($tvaItem ['tva'])
                            if($ordercurrency!="EUR")
                            {
                               $this->insertTVAValue (" - ".Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($tvaItem ['tva']), 280, $_curY, 330 - 280, $page );
                            }
                            else
                            {
				$this->insertTVAValue ( Mage::helper ( 'core' )->formatPrice ( - $tvaItem ['tva'], false ), 280, $_curY, 330 - 280, $page );
                            }
//$this->insertTVAValue ( Mage::helper ( 'core' )->formatPrice ( - $tvaItem ['tva'], false ), 280, $_curY, 330 - 280, $page );
			if ($tvaItem ['totht'])
                             if($ordercurrency!="EUR")
                            {
                               $this->insertTVAValue (" - ".Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($tvaItem ['totht']), 330, $_curY, 384 - 330, $page );
                            }
                            else
                            {
				$this->insertTVAValue ( Mage::helper ( 'core' )->formatPrice ( - $tvaItem ['totht'], false ), 330, $_curY, 384 - 330, $page );
                            }
//$this->insertTVAValue ( Mage::helper ( 'core' )->formatPrice ( - $tvaItem ['totht'], false ), 330, $_curY, 384 - 330, $page );
                break;
		}
	}

	protected function insertTotals($page, $source) {
	//echo '<pre>';print_r($this->invoice);exit;
            $ordercurrency = $this->order->getOrderCurrencyCode();
		$order = $source->getOrder ();

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
			if($ordercurrency!="EUR")
                        {
                            $amountText = '-'.Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($amount);
                        }
                        else
                        {
                            $amountText = '-'.Mage::helper ( 'core' )->formatPrice  ($amount,false);
                        } 
			
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
			//$amountText = '-'.Mage::helper ( 'core' )->formatPrice  ( $totalDue + $discountAmount,false );
                        if($ordercurrency!="EUR")
                        {
                            $amountText = '-'.Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($totalDue + $discountAmount);
                        }
                        else
                        {
                            $amountText = '-'.Mage::helper ( 'core' )->formatPrice  ( $totalDue + $discountAmount,false );
                        }
           
			$pos = $this->getAlignRight ( $amountText, 490, 60, $page->getFont (), $page->getFontSize () );
			$page->drawText ( $amountText, $pos, $y, 'UTF-8' );
			$page->drawText ( $label, $xLabel, $y, 'UTF-8' );
		}

		return $page;
	}

    protected function insertCreditConditions($page) {

		$comments = $this->invoice->getCommentsCollection();
		if($comments && $comments->getSize()){
			$this->_setBoldContentFont ( $page );
			$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( "CONDITIONS OF REPAYMENT" ) . " :", self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );
			$this->y = $this->y - 8;

			$lineHeight = 8;
			$this->_setContentFont ( $page );
			foreach($comments as $comment){
				$value = $comment->getComment();
				$value ? trim($value) : '';
				if ($value) {
					$tb = explode ( "\n", $value );
					foreach ($tb as $itemTb) {
						$this->y = $this->y - $lineHeight;
						$page->drawText ( $itemTb, self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );
					}
				}
				$this->y = $this->y - 5;
			}
		}

	}
}
