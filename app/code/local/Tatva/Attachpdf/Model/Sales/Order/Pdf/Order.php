<?php
/**
 * Magento Module developed by NoStress Commerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@nostresscommerce.cz so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2008 NoStress Commerce (http://www.nostresscommerce.cz)
 *
 */
/**
 * Sales Order Invoice PDF model
 *
 * @category   Nostress
 * @package    Nostress_Invoicetemplates
 * @author     NoStress Commerce Team <info@nostresscommerce.cz>
 */
class Tatva_Attachpdf_Model_Sales_Order_Pdf_Order extends Tatva_Attachpdf_Model_Sales_Order_Pdf_Abstract
{
	public function getPdf($orders = array())
    {
		$this->_beforeGetPdf ();
		$this->_initRenderer ( 'order' );
		$pdf = new Zend_Pdf ( );
		$this->_setPdf ( $pdf );
		$style = new Zend_Pdf_Style ( );
		$this->_setFontBold ( $style, 10 );

		foreach ( $orders as $order )
        {
			if ($order->getStoreId ()) {
				Mage::app ()->getLocale ()->emulate ( $order->getStoreId () );
			}
			$page = $pdf->newPage ( Zend_Pdf_Page::SIZE_A4 );
			$pdf->pages [] = $page;
			$this->order = $order;

			// initialisation axe Y
			$this->y = $page->getHeight ();

			/* Add image */
			$this->insertLogo ( $page, $this->order->getStore () );

			/* Add address */
			$this->insertAddress ( $page, $this->order->getStore () );

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
				$this->insertShippingAddress ( $page, $this->order->getStore () );
			}

			/* Adresse de facturation */
			$this->insertBillingAddress ( $page ,  $this->order->getStore ());

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

		return $pdf;
	}

	protected function insertOrderHeader(&$page)
    {
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
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'ORDER INFORMATION %s', $this->order->getIncrementId () ), 33, $this->y - 19, 'UTF-8' );
		// Contenu
		$this->_setBoldContentFont ( $page );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Order date : %s', Mage::helper ( 'core' )->formatDate ( $this->order->getCreatedAtStoreDate (), 'medium', false ) ), 34, $this->y - 34, 'UTF-8' );
        if (!$this->order->getCustomerIsGuest()) {
			$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Customer %s', $customer->getIncrementId () ), 215, $this->y - 33, 'UTF-8' );
		}
		$this->_setContentFont ( $page );
	//	if(strlen($this->order->getShippingDescription ()) > 50)
        //{
           // $page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Shipping mode : %s', substr($this->order->getShippingDescription (),0,50) )."-", 310, $this->y - 45, 'UTF-8' );
       //     $page->drawText ( substr($this->order->getShippingDescription (),50) , 380, $this->y - 57, 'UTF-8' );
       // }
       // else
      //  {
           $page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Shipping mode : %s', $this->order->getShippingDescription () ), 215, $this->y - 45, 'UTF-8' );
      //  }
        $this->_getInfoPayment ( $page );
		$this->y = $this->y - 64;
	}

	protected function insertItems(&$page)
    {
		$this->insertHeaderItems ( $page );
        $heightLine = 27;
		foreach ( $this->order->getAllItems () as $item ) {

			if ($item->getParentItem ()) {
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
			$page->setFillColor ( new Zend_Pdf_Color_Html ( '#C30245' ) );
			$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );

			// Mise à jour de l'axe Y
			$this->y = $this->y - $heightLine;

		}
		$page->setLineDashingPattern ( Zend_Pdf_Page::LINE_DASHING_SOLID );
		$this->y = $this->y - 10;
	}

	public function getItemOptions($item) {
		$result = array ();
		if ($options = $item->getProductOptions ()) {
			if (isset ( $options ['options'] )) {
				$result = array_merge ( $result, $options ['options'] );
			}
			if (isset ( $options ['additional_options'] )) {
				$result = array_merge ( $result, $options ['additional_options'] );
			}
			if (isset ( $options ['attributes_info'] )) {
				$result = array_merge ( $result, $options ['attributes_info'] );
			}
		}
		return $result;
	}

	 /**
     * Get brand
     */
	public function getBrand($brandId) {
		$storeId = Mage::app()->getStore()->getId();
		$productEntityTypeId = Mage::getModel ( 'eav/entity' )->setType ( 'catalog_product' )->getTypeId ();
		$brandAttribute = Mage::getModel('eav/entity_attribute')->loadByCode($productEntityTypeId,Mage::helper('brand')->getBrandAttributeCode());

		$brands = Mage::getModel('eav/entity_attribute_option')	->getCollection()
																->addFieldToFilter('attribute_id',array('='=>$brandAttribute->getAttributeId()))
																->setIdFilter($brandId)
																->setStoreFilter($storeId, false)
																->load();

		foreach ($brands as $brand)
			return $brand;
	}


	protected function addItem($page, $item) {
		try{
			$_height = 6;
			$sep = '';
			//$baseY = $this->y - 17;
			$reference = array();
			$marqueEtCollection = array();

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
				$page->drawText ( utf8_encode($value), 114, $_y , 'UTF-8');
				$_y -= $_height;
			}

			//Marque et collection
			$_y -= 2;
			foreach($marqueEtCollection as $value){
				$this->_setSmallContentFont ( $page );
				$page->drawText ( utf8_encode($value), 114, $_y, 'UTF-8' );
				$_y -= $_height;
			}

			// Options
			/*$options = $this->getItemOptions ( $item );
			if ($options) {

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

            	$this->_setRedContentFont ( $page );
            $string = number_format ( $item->getTaxPercent (), 2 );
			$pos = $this->getAlignCenter ( $string, 246, 332 - 246, $page->getFont (), $page->getFontSize () );
			$page->drawText ( $string, $pos, $baseY, 'UTF-8' );

              /* added for incl price */
             $item_row_total='';  $item_price='';
             $_product = Mage::getModel('catalog/product')->load($item->getProductId());
             $weee_amt = Mage::helper('weee')->getAmountForDisplayInclTaxes($_product);
             $item_amt_without_weee = $item->getBaseRowTotal()- $weee_amt;
    		 $item_tax = $item_amt_without_weee * $item->getTaxPercent ()/100;
    		 $item_row_total = number_format($item_amt_without_weee + $item_tax,2);

             $item_amt_without_weee_for_price = $item->getBasePrice()- $weee_amt;
          	 $item_tax = $item_amt_without_weee_for_price * $item->getTaxPercent ()/100;
          	 $item_price = number_format($item_amt_without_weee_for_price + $item_tax,2);

             $ordercurrency = $this->order->getBaseCurrencyCode();
             $item_price = (float) $item_price;
             $string = Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($item_price);
			
			$pos = $this->getAlignCenter ( $string, 332, 385 - 332, $page->getFont (), $page->getFontSize () );
			$page->drawText ( $string, $pos, $baseY, 'UTF-8' );

            // Unit price
            $weee_amt = (float) $weee_amt;
            $string = Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($weee_amt);

			//echo $string;die();
			$pos = $this->getAlignCenter ( $string, 385, 456 - 385, $page->getFont (), $page->getFontSize () );
			$page->drawText ( $string, $pos, $baseY, 'UTF-8' );




			// qty
			$string = $item->getQtyOrdered () * 1;
			$pos = $this->getAlignCenter ( $string, 456, 489 - 456, $page->getFont (), $page->getFontSize () );
			$page->drawText ( $string, $pos, $baseY, 'UTF-8' );
			// total ht
            $item_row_total = (float) $item_row_total;
            $string = Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($item_row_total);
			
			$pos = $this->getAlignCenter ( $string, 489, 561 - 489, $page->getFont (), $page->getFontSize () );
			$page->drawText ( $string, $pos, $baseY, 'UTF-8' );
		}catch(Exception $e){
			Mage::logException($e);
			throw $e;
		}
		return $_heightLine;
	}

	public function getSku($item) {
		/*if ($item->getProductType () == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
			return $item->getProductOptionByCode ( 'simple_sku' );
		}
		return $item->getSku ();*/
		//$skyParts = split('-',$item->getSku());
		$skyParts = $item->getSku();
		return $skyParts;
	}

	protected function insertTvaCols(&$page) {
            $ordercurrency = $this->order->getBaseCurrencyCode();
		$tvaDetail = array ();
		foreach ( $this->order->getAllItems () as $orderItem ) {
			$codeTva = 't' . ($orderItem->getTaxPercent () * 1);
			$tvaDetail [$codeTva] = array (
					'sstotht' => 0,
					'rem' => 0,
					'port' => 0,
					'pTva' => 0,
					'tva' => 0,
					'totht' => 0,
                    'weee' => 0  );
		}
		if ($this->order->getShippingAmount () > 0) {
			$codeTva = 't' . ($this->order->getPercentTaxShipping () * 1);
			$tvaDetail [$codeTva] = array (
					'sstotht' => 0,
					'rem' => 0,
					'port' => 0,
					'pTva' => 0,
					'tva' => 0,
					'totht' => 0,
                    'weee' => 0 );
		}

                //echo "<pre>";print_r($this->order->getData());die();
            $model=Mage::getModel('catalog/product');
            //echo "<pre>";print_r($this->order->getAllItems());die();
        	foreach ( $this->order->getAllItems () as $orderItem ) {
                    //echo $orderItem->getBaseTaxAmount ();die();
			//$orderItem = $item->getOrderItem ();
            $tax_info = $this->order->getFullTaxInfo();

            $_product =$model->load($orderItem->getProductId());
			$weee_amt = Mage::helper('weee')->getAmountForDisplayInclTaxes($_product);
			$weee_amt_without_tax = Mage::helper('weee')->getAmount($_product);
            $weee_amt = $weee_amt * $orderItem->getQtyOrdered();
			$codeTva = 't' . ($orderItem->getTaxPercent () * 1);
            $tvaDetail [$codeTva] ['pTva'] = $orderItem->getTaxPercent ();
			//$tvaDetail [$codeTva] ['sstotht'] = $tvaDetail [$codeTva] ['sstotht'] + $item->getRowTotal () + $item->getDiscountAmount ();
			$tvaDetail [$codeTva] ['sstotht'] = $tvaDetail [$codeTva] ['sstotht'] + $orderItem->getBaseRowTotal();
            $tvaDetail [$codeTva] ['totht'] = $tvaDetail [$codeTva] ['totht'] + $orderItem->getBaseRowTotal()  - $orderItem->getBaseDiscountAmount ()+ $this->order->getBaseShippingAmount () +$weee_amt_without_tax;
            $tvaDetail [$codeTva] ['totht'] = $tvaDetail [$codeTva] ['totht'] + $this->order->getBaseShippingAmount ();
            
		    //$tvaDetail [$codeTva] ['tva'] = $tvaDetail [$codeTva] ['tva'] + $orderItem->getBaseTaxAmount();
            
                    $tvaDetail [$codeTva] ['tva'] = $tvaDetail [$codeTva] ['tva'] + $this->order->getTaxAmount();
                   //$tvaDetail [$codeTva] ['tva'] = number_format ( $tvaDetail [$codeTva] ['tva'], 2 );
                    //echo $tvaDetail [$codeTva] ['tva'];die();
			//$tvaShipping = $this->order->getBaseShippingAmount () * $orderItem->getTaxPercent () / 100;
		   //	$tvaDetail [$codeTva] ['tva'] = $tvaDetail [$codeTva] ['tva'] + $tvaShipping;
			$tvaDetail [$codeTva] ['rem'] = $tvaDetail [$codeTva] ['rem'] + $orderItem->getBaseDiscountAmount();
            $tvaDetail [$codeTva] ['port'] = $this->order->getBaseShippingAmount ();
            $tvaDetail [$codeTva] ['weee'] = $tvaDetail [$codeTva] ['weee'] + $weee_amt_without_tax;

            //break;
		}

        $tvaDetail [$codeTva] ['totht'] = $this->order->getBaseSubtotal() + $this->order->getBaseShippingAmount();
				if($this->order->getBaseDiscountAmount()){
					$discount = 0;
					foreach ( $this->order->getAllItems () as $item ) {
						$discount += $item->getBaseDiscountAmount();
					}
					$tvaDetail [$codeTva] ['totht'] -= $discount;
				}
       
		// Remplissage des lignes
		$this->_setContentFont ( $page );
		$_curY = $this->y - 16;
		$lineHeight = 12;
		$cpt = 1;
                //echo "<pre>";print_r($tvaDetail);die();
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
				$this->insertTVAValue (Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($this->order->getTaxAmount()), 280, $_curY, 330 - 280, $page );

			if ($tvaItem ['totht'])
				$this->insertTVAValue (Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($tvaItem ['totht']), 330, $_curY, 384 - 330, $page );

                break;
		}
	}

	protected function insertTotals($page, $source) {
		$order = $source->getOrder ();
                $ordercurrency = $order->getBaseCurrencyCode();
		$totals = $this->_getTotalsList ( $source );
		$this->_setContentFont ( $page );

		$xLabel = 400;
		$xValue = 500;
		$lineHeight = 14;
		//$y = $this->y - $lineHeight;

		$amountText = null;
		$discountAmount = 0;
		$totalDue = 0;
		$taxAmount = false;

		foreach ( $totals as $total ) {
			if($total ['source_field'] == 'subtotal') {
				$amount = $this->order->getBaseSubtotal() + $this->order->getBaseShippingAmount();
				if($order->getDiscountAmount()){
					$discount = 0;
					foreach ( $this->order->getAllItems () as $orderItem) {
						$discount += $orderItem->getDiscountAmount();
					}
					$amount -= $discount;
				}

			} else if($total ['source_field'] == 'tax_amount') {
				$amount = $this->order->getBaseTaxAmount();
			}
			else if($total ['source_field'] == 'grand_total') {
				$amount = $this->order->getBaseGrandTotal();
			}

			$displayZero = (isset ( $total ['display_zero'] ) ? $total ['display_zero'] : 0);
		   
	        //$amountText = Mage::helper ( 'core' )->formatPrice  ($amount,false);
            $amountText = Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($amount);

			if (isset ( $total ['amount_prefix'] ) && $total ['amount_prefix']) {
				$amountText = "{$total['amount_prefix']}{$amountText}";
			}

			if (in_array ( $total ['source_field'], array (
					'subtotal',
					'tax_amount',
					'discount_amount',
					'grand_total') )) {

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
							$taxAmount = $this->order->getBaseTaxAmount();
						}else {
							$write = false;
						}

						break;
					case 'discount_amount' :
						if($order->getCartFixed()){
							$couponCode = $order->getCouponCode();
							if($couponCode && preg_match('/^'.Sqli_ChequeCadeau_Model_ChequeCadeau::PREFIX_CHQ.'/', $couponCode )){
								$label = Mage::helper('chequecadeau')->__('Gift Check');
							}else{
								$label = Mage::helper ( 'tatvasales' )->__ ( 'Discount' ) . ' :';
							}

							$this->_setContentFont ( $page );
							$discountAmount = $order->getDataUsingMethod ( $total ['source_field'] );
						}else{
							$write = false;
						}

						$y = $this->y - ($lineHeight * 4);
						break;
					case 'grand_total' :
						$this->_setBigBoldRedContentFont ( $page );
						$label = Mage::helper ( 'tatvasales' )->__ ( 'Total due' ) . ' :';
						$y = $this->y - ($lineHeight * 6);
						$totalDue = $this->order->getBaseGrandTotal();
						break;
					default :
						continue;
				}

				if($write){
					$value = $amountText;
					$pos = $this->getAlignRight ( $value, 490, 60, $page->getFont (), $page->getFontSize () );
					$page->drawText ( $value, $pos, $y, 'UTF-8' );
					$page->drawText ( $label, $xLabel, $y, 'UTF-8' );
				}
				//$y -= $lineHeight;
			}


		}

		if($taxAmount || !$order->getShippingAddress()){
			$this->_setBoldContentFont ( $page );
			$label = Mage::helper('tatvasales')->__('Total incl. VAT') . ' :';
			$y = $this->y - ($lineHeight * 3);
			//$amountText = Mage::helper ( 'core' )->formatPrice  ( $totalDue + $discountAmount,false );
            $amountText = Mage::app()->getLocale()->currency($ordercurrency)->toCurrency($totalDue + $discountAmount);

			$pos = $this->getAlignRight ( $amountText, 490, 60, $page->getFont (), $page->getFontSize () );
			$page->drawText ( $amountText, $pos, $y, 'UTF-8' );
			$page->drawText ( $label, $xLabel, $y, 'UTF-8' );
		}
		return $page;
	}

	protected function insertTotaux(&$page) {
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$page->setFillColor ( new Zend_Pdf_Color_Html ( '#F9E6ED' ) );//#FFF0E2
		$page->drawRectangle ( 397, $this->y - 90, self::COORD_X_RIGHT_MARGIN, $this->y );
		$page = $this->insertTotals ( $page, $this );
		$this->y = $this->y - 90 - 15;
	}

	public function getOrder() {
		return $this->order;
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

		/*if ($area && is_array($taxSentenceConfig) && $order->getTaxAmount()<=0)
        {
			$sentence = false;

			//Si on a une adresse
			if(is_object($address))
            {
				foreach ($taxSentenceConfig as $elmt)
                {
				    if (in_array($area->getAreaId(),$elmt['areas']))
                    {
					    $sentence = $elmt['sentence'];
						break;
					}
				}
            }
		}
        else
        {
			$sentence = false;
		}*/

        $tax_amount = $order->getTaxAmount();
		//var_dump($area->getData());
        //exit;
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
                    //var_dump($elmt['areas']);

                    //echo $area->getAreaId();
                    //echo "<br />";
                    foreach($area as $subarea)
                    {
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

	protected function _getInfoValues() {
		$paymentInfo = Mage::helper ( 'payment' )->getInfoBlock ( $this->order->getPayment () )->setIsSecureMode ( true )->toPdf ();
		$payment = explode ( '{{pdf_row_separator}}', $paymentInfo );
		foreach ( $payment as $key => $value ) {
			if (strip_tags ( trim ( $value ) ) == '') {
				unset ( $payment [$key] );
			}
		}
		reset ( $payment );
		return $payment;
	}

	/*protected function _getInfoPayment(&$page) {
		$payment = $this->_getInfoValues();
        if($payment)
        {
    		foreach ( $payment as $value ) {echo 'payment=='.$value;exit;
    			if (trim ( $value ) !== '') {
    				if(strtoupper ( rtrim ($value) ) == "PARTENAIRE"){
    					$value = $this->order->getPayment()->getMarketplacesPartnerCode();
    				}
                    if(strlen($this->order->getShippingDescription ()) > 50)
                    {
    				    $page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Payment mode : %s', strip_tags ( strtoupper(trim ( $value ) ) ) ), 310, $this->y - 69, 'UTF-8' );
                    }
                    else
                    {
    				    $page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Payment mode : %s', strip_tags ( strtoupper(trim ( $value ) ) ) ), 310, $this->y - 57, 'UTF-8' );
                    }
    				break;
    			}
    		}
        }
        else
        {
            $additionalData = @unserialize($this->order->getPayment ()->getAdditionalData());
            $value = isset($additionalData['payment_method']) ? $additionalData['payment_method'] : NULL;
            if(strtoupper ( rtrim ($value) ) == "PARTENAIRE"){
    			$value = $this->order->getPayment()->getMarketplacesPartnerCode();
    		}
    		if(strlen($this->order->getShippingDescription ()) > 50)
            {
                $page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Payment mode : %s', strip_tags ( strtoupper(trim ( $value ) ) ) ), 310, $this->y - 69, 'UTF-8' );
            }
            else
            {
                $page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Payment mode : %s', strip_tags ( strtoupper(trim ( $value ) ) ) ), 310, $this->y - 57, 'UTF-8' );
            }
        }
	}*/

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
}

?>