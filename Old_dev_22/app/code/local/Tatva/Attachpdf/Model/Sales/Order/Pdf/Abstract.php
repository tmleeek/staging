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
abstract class Tatva_Attachpdf_Model_Sales_Order_Pdf_Abstract extends Mage_Sales_Model_Order_Pdf_Abstract
{
    const COORD_X_LEFT_MARGIN = 28;
	const COORD_X_RIGHT_MARGIN = 566;

	public $order;
	public $invoice;
	public $minY = 0;

	protected function insertLogo(&$page, $store = null) {
		//$image = Mage::getStoreConfig ( 'sales/identity/logo', $store );
            $image = Mage::getStoreConfig ( 'sales_pdf/attachpdf/logoattach', $store );
		if ($image) {
			//$image = Mage::getStoreConfig ( 'system/filesystem/media', $store ) . '/sales/store/logo/' . $image;
			$image = $_SERVER['DOCUMENT_ROOT'] . '/media/sales/store/logo/' . $image;
			
			if (is_file ( $image )) {
				$image = Zend_Pdf_Image::imageWithPath ( $image );
				$x1 = self::COORD_X_LEFT_MARGIN;
				$y1 = $this->y - 122;
				$x2 = self::COORD_X_RIGHT_MARGIN;
				$y2 = $this->y - 24;
				$page->drawImage ( $image, $x1, $y1, $x2, $y2 );
				$this->y = $y1;
			}
		}
		//return $page;
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

	protected function _getInfoPayment(&$page) {
		$payment = $this->_getInfoValues();
        if($payment)
        {
    		foreach ( $payment as $value ) {
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
            $value = isset($additionalData['payment_method']) ? $additionalData['payment_method'] : $this->order->getPayment()->getMethodInstance()->getTitle();
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
		$this->insertTVATitle ( 'Subtotal (VAT excl.)', $x1, $x2 - $x1, $page );

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



        //deee
        $page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = 125;
		$y1 = $this->y - 90;
		$x2 = 179;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		$this->insertRedTVAHeader ( $x1, $y1, $x2, $y2, $page );
		$this->insertTVATitle ( 'DEEE TAX', $x1, $x2 - $x1, $page );

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
		$this->insertTVATitle ( 'Total (VAT Incl.)', $x1, $x2 - $x1, $page );

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

	public function getSku($item) {
		/*if ($item->getOrderItem ()->getProductType () == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
			return $item->getOrderItem ()->getProductOptionByCode ( 'simple_sku' );
		}
		return $item->getSku ();*/
		$skyParts = split('-',$item->getSku());
		return $skyParts[0];
	}

	public function getItemOptions($item) {
		$result = array ();
		if ($options = $item->getOrderItem ()->getProductOptions ()) {
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

	protected function insertShippingAddress(&$page, $store = null) {
		// Création du rectangle conteneur
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = self::COORD_X_LEFT_MARGIN;
		$y1 = $this->y - 130;
		$x2 = 292;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		// Rectangle de titre
		$x1 = self::COORD_X_LEFT_MARGIN + 0.5;
		$y1 = $this->y - 16;
		$x2 = 292 - 0.5;
		$y2 = $this->y - 0.5;
		$page->setFillColor ( new Zend_Pdf_Color_Html ( '#C30245' ) );
		$page->drawRectangle ( $x1, $y1, $x2, $y2 );
		// Titre
		$this->_setTitleFont ( $page );
		$page->drawText ( strtoupper ( Mage::helper ( 'tatvasales' )->__ ( 'Shipping address' ) ), 34, $this->y - 12, 'UTF-8' );
		// Adresse
		$locale = Mage::getStoreConfig ( 'general/locale/code', $store );

        $address = array();

        
		if($locale == 'en_EN') {
			$address = $this->_formatAddress ( $this->order->getShippingAddress ()->format ( 'pdf' ) );
            $vowels = array("â", "Ä", "à", "á", "â", "ã", "ä" );
            $address = str_replace($vowels, "A", $address);

            $vowels = array("ê","é","è" ,"ê" ,"ë");
            $address = str_replace($vowels, "E", $address);

            $vowels = array("ò", "ó", "ô", "õ" ,"ö", "Ò" ,"Ó","Ô","Õ","Ö");
            $address = str_replace($vowels, "O", $address);

            $vowels = array("ù","ú","û","ü","Ù","Ú","Û","Ü" );
            $address = str_replace($vowels, "U", $address);

            $vowels = array("Ç","ç");
            $address = str_replace($vowels, "C", $address);

            $vowels = array("ñ");
            $address = str_replace($vowels, "N", $address);

            $vowels = array("Ì","Í","Î","ì","í","î","ï","Ï");
            $address = str_replace($vowels, "I", $address);

            $vowels = array("ý","ÿ");
            $address = str_replace($vowels, "Y", $address);
		}
        else if($locale == 'es_ES')
        {
           $address = $this->_formatAddress ( $this->order->getShippingAddress ()->format ( 'pdf' ) );
           $vowels = array("â", "Ä", "à", "á", "â", "ã", "ä" );
            $address = str_replace($vowels, "A", $address);

            $vowels = array("ê","é","è" ,"ê" ,"ë");
            $address = str_replace($vowels, "E", $address);

            $vowels = array("ò", "ó", "ô", "õ" ,"ö", "Ò" ,"Ó","Ô","Õ","Ö");
            $address = str_replace($vowels, "O", $address);

            $vowels = array("ù","ú","û","ü","Ù","Ú","Û","Ü" );
            $address = str_replace($vowels, "U", $address);

            $vowels = array("Ç","ç");
            $address = str_replace($vowels, "C", $address);

            $vowels = array("ñ");
            $address = str_replace($vowels, "N", $address);

            $vowels = array("Ì","Í","Î","ì","í","î","ï","Ï");
            $address = str_replace($vowels, "I", $address);

            $vowels = array("ý","ÿ");
            $address = str_replace($vowels, "Y", $address);
        }
        else{
			$address = $this->_formatAddress ( $this->order->getShippingAddress ()->format ( 'pdf' ) );
            $vowels = array("â", "Ä", "à", "á", "â", "ã", "ä" );
            $address = str_replace($vowels, "A", $address);

            $vowels = array("ê","é","è" ,"ê" ,"ë");
            $address = str_replace($vowels, "E", $address);

            $vowels = array("ò", "ó", "ô", "õ" ,"ö", "Ò" ,"Ó","Ô","Õ","Ö");
            $address = str_replace($vowels, "O", $address);

            $vowels = array("ù","ú","û","ü","Ù","Ú","Û","Ü" );
            $address = str_replace($vowels, "U", $address);

            $vowels = array("Ç","ç");
            $address = str_replace($vowels, "C", $address);

            $vowels = array("ñ");
            $address = str_replace($vowels, "N", $address);

            $vowels = array("Ì","Í","Î","ì","í","î","ï","Ï");
            $address = str_replace($vowels, "I", $address);

            $vowels = array("ý","ÿ");
            $address = str_replace($vowels, "Y", $address);
		}

		$this->_setContentFont ( $page );
		$y = $this->y - 28;
		foreach ( $address as $value ) {
			if ($value !== '') {
				if($value == '##'){
					$value = " ";
				}
				$value = strtoupper($value);
				$page->drawText ( strip_tags ( ltrim ( $value ) ), 34, $y, 'UTF-8' );
				$y -= 11;
			}
		}
	}

	protected function insertBillingAddress(&$page, $store = null) {
		// Création du rectangle conteneur
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = 304;
		$y1 = $this->y - 130;
		$x2 = self::COORD_X_RIGHT_MARGIN;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		// Rectangle de titre
		$x1 = 304.5;
		$y1 = $this->y - 16;
		$x2 = self::COORD_X_RIGHT_MARGIN - 0.5;
		$y2 = $this->y - 0.5;
		$page->setFillColor ( new Zend_Pdf_Color_Html ( '#C30245' ) );
		$page->drawRectangle ( $x1, $y1, $x2, $y2 );
		// Titre
		$this->_setTitleFont ( $page );
		$page->drawText ( strtoupper ( Mage::helper ( 'tatvasales' )->__ ( 'Billing address' ) ), 310, $this->y - 12, 'UTF-8' );
		// Adresse
		$locale = Mage::getStoreConfig ( 'general/locale/code', $store );

        $customer_details = mage::getmodel('customer/customer')->load($this->order->getCustomerId());
        $vat_id_details = $customer_details->getTaxvat();

		if($locale == 'en_EN' )
        {
			$address = $this->_formatAddress ( $this->order->getBillingAddress ()->format ( 'pdf' ) );
            $vowels = array("â", "Ä", "à", "á", "â", "ã", "ä" );
            $address = str_replace($vowels, "A", $address);

            $vowels = array("ê","é","è" ,"ê" ,"ë");
            $address = str_replace($vowels, "E", $address);

            $vowels = array("ò", "ó", "ô", "õ" ,"ö", "Ò" ,"Ó","Ô","Õ","Ö");
            $address = str_replace($vowels, "O", $address);

            $vowels = array("ù","ú","û","ü","Ù","Ú","Û","Ü" );
            $address = str_replace($vowels, "U", $address);

            $vowels = array("Ç","ç");
            $address = str_replace($vowels, "C", $address);

            $vowels = array("ñ");
            $address = str_replace($vowels, "N", $address);

            $vowels = array("Ì","Í","Î","ì","í","î","ï","Ï");
            $address = str_replace($vowels, "I", $address);

            $vowels = array("ý","ÿ");
            $address = str_replace($vowels, "Y", $address);
		}
        else if($locale == 'es_ES')
        {
           $address = $this->_formatAddress ( $this->order->getBillingAddress ()->format ( 'pdf' ) );
            $vowels = array("â", "Ä", "à", "á", "â", "ã", "ä" );
            $address = str_replace($vowels, "A", $address);

            $vowels = array("ê","é","è" ,"ê" ,"ë");
            $address = str_replace($vowels, "E", $address);

            $vowels = array("ò", "ó", "ô", "õ" ,"ö", "Ò" ,"Ó","Ô","Õ","Ö");
            $address = str_replace($vowels, "O", $address);

            $vowels = array("ù","ú","û","ü","Ù","Ú","Û","Ü" );
            $address = str_replace($vowels, "U", $address);

            $vowels = array("Ç","ç");
            $address = str_replace($vowels, "C", $address);

            $vowels = array("ñ");
            $address = str_replace($vowels, "N", $address);

            $vowels = array("Ì","Í","Î","ì","í","î","ï","Ï");
            $address = str_replace($vowels, "I", $address);

            $vowels = array("ý","ÿ");
            $address = str_replace($vowels, "Y", $address);
        }
        else
        {
			$address = $this->_formatAddress ( $this->order->getBillingAddress ()->format ( 'pdf' ) );
            $vowels = array("â", "Ä", "à", "á", "â", "ã", "ä" );
            $address = str_replace($vowels, "A", $address);

            $vowels = array("ê","é","è" ,"ê" ,"ë");
            $address = str_replace($vowels, "E", $address);

            $vowels = array("ò", "ó", "ô", "õ" ,"ö", "Ò" ,"Ó","Ô","Õ","Ö");
            $address = str_replace($vowels, "O", $address);

            $vowels = array("ù","ú","û","ü","Ù","Ú","Û","Ü" );
            $address = str_replace($vowels, "U", $address);

            $vowels = array("Ç","ç");
            $address = str_replace($vowels, "C", $address);

            $vowels = array("ñ");
            $address = str_replace($vowels, "N", $address);

            $vowels = array("Ì","Í","Î","ì","í","î","ï","Ï");
            $address = str_replace($vowels, "I", $address);

            $vowels = array("ý","ÿ");
            $address = str_replace($vowels, "Y", $address);
		}

		$this->_setContentFont ( $page );
		$y = $this->y - 28;

		foreach ( $address as $value )
        {
			if ($value !== '')
            {
				if($value == '##')
                {
					$value = " ";
				}
				$value = strtoupper($value);
				$page->drawText ( strip_tags ( ltrim ( $value ) ), 310, $y, 'UTF-8' );
				$y -= 11;
			}
		}
        If(!empty($vat_id_details))
        {
            $page->drawText (strip_tags(ltrim(Mage::helper('tatvasales')->__('VAT Intracom. Number').": ".$vat_id_details)), 310, $y, 'UTF-8');
        }
	}

	protected function insertAddress(&$page, $store = null) {
		$this->_setSilverContentFont ( $page );
		$address = Mage::getStoreConfig ( 'sales/identity/address', $store );
		$address ? trim($address) : '';
		if ($address) {
			$tbAddress = explode ( "\n", Mage::getStoreConfig ( 'sales/identity/address', $store ) );
			$nbLines = count($tbAddress);
			$rTbAddress = array_reverse($tbAddress);
			$y = 15;
			foreach ($rTbAddress as $itemAddress) {
				$pos = $this->getAlignCenter($itemAddress, 0, $page->getWidth(), $page->getFont(), $page->getFontSize());
				$page->drawText ( $itemAddress, $pos, $y, 'UTF-8' );
				$y += 12;
			}
		}
		$this->minY = $y + 40;
	}

	protected function _setTitleFont(&$page) {
		$page->setFillColor ( new Zend_Pdf_Color_GrayScale ( 1 ) );
		$font = Zend_Pdf_Font::fontWithName ( Zend_Pdf_Font::FONT_HELVETICA_BOLD );
		$page->setFont ( $font, 10 );
	}
	protected function _setSmallTitleFont(&$page) {
		$page->setFillColor ( new Zend_Pdf_Color_GrayScale ( 1 ) );
		$font = Zend_Pdf_Font::fontWithName ( Zend_Pdf_Font::FONT_HELVETICA_BOLD );
		$page->setFont ( $font, 8 );
	}
	protected function _setBoldContentFont(&$page) {
		$page->setFillColor ( new Zend_Pdf_Color_GrayScale ( 0 ) );
		$font = Zend_Pdf_Font::fontWithName ( Zend_Pdf_Font::FONT_HELVETICA_BOLD );
		$page->setFont ( $font, 8 );
	}
	protected function _setContentFont(&$page) {
		$page->setFillColor ( new Zend_Pdf_Color_GrayScale ( 0 ) );
		$font = Zend_Pdf_Font::fontWithName ( Zend_Pdf_Font::FONT_HELVETICA );
		$page->setFont ( $font, 8 );
	}
	protected function _setItalicContentFont(&$page) {
		$page->setFillColor ( new Zend_Pdf_Color_GrayScale ( 0 ) );
		$font = Zend_Pdf_Font::fontWithName ( Zend_Pdf_Font::FONT_HELVETICA_ITALIC );
		$page->setFont ( $font, 8 );
	}
	protected function _setSmallContentFont(&$page) {
		$page->setFillColor ( new Zend_Pdf_Color_GrayScale ( 0 ) );
		$font = Zend_Pdf_Font::fontWithName ( Zend_Pdf_Font::FONT_HELVETICA );
		$page->setFont ( $font, 6 );
	}
	protected function _setSmallSilverContentFont(&$page) {
		$page->setFillColor ( new Zend_Pdf_Color_Html ( 'silver' ) );
		$font = Zend_Pdf_Font::fontWithName ( Zend_Pdf_Font::FONT_HELVETICA );
		$page->setFont ( $font, 6 );
	}
	protected function _setRedContentFont(&$page) {
		$page->setFillColor ( new Zend_Pdf_Color_Html ( '#C30245' ) );
		$font = Zend_Pdf_Font::fontWithName ( Zend_Pdf_Font::FONT_HELVETICA );
		$page->setFont ( $font, 8 );
	}
	protected function _setBoldRedContentFont(&$page) {
		$page->setFillColor ( new Zend_Pdf_Color_Html ( '#C30245' ) );
		$font = Zend_Pdf_Font::fontWithName ( Zend_Pdf_Font::FONT_HELVETICA_BOLD );
		$page->setFont ( $font, 8 );
	}
	protected function _setBigBoldRedContentFont(&$page) {
		$page->setFillColor ( new Zend_Pdf_Color_Html ( '#C30245' ) );
		$font = Zend_Pdf_Font::fontWithName ( Zend_Pdf_Font::FONT_HELVETICA_BOLD );
		$page->setFont ( $font, 10 );
	}
	protected function _setSilverContentFont(&$page) {
		$page->setFillColor ( new Zend_Pdf_Color_Html ( 'silver' ) );
		$font = Zend_Pdf_Font::fontWithName ( Zend_Pdf_Font::FONT_HELVETICA );
		$page->setFont ( $font, 8 );
	}

}

?>