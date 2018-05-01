<?php
/**
 * Gls_Unibox extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Gls
 * @package    Gls_Unibox
 * @copyright  Copyright (c) 2013 webvisum GmbH
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Webvisum
 * @package    Gls_Unibox
 */
abstract class Gls_Unibox_Model_Pdf_Abstract extends Mage_Core_Model_Abstract
{
    /** NOTE: 1 mm corresponds to 2,8 **/

	/**
     * x coordinate of current position in document
     * @var int
     */
    public $y;
	/**
     * y coordinate of current position in document
     * @var int
     */
	public $x;
    /**
     * Current Pdf
     * @var Zend_Pdf
     */
    public $pdf;
    /**
     * Current Page
     * @var Zend_Pdf_Page
     */
    public $page;
	

    /**
     * Character Set
     * @var string
     */
    public $charset = "UTF-8";
	
	public $_startpunkt;
	public $defaultStyle;
    
	public function __construct(){
		if( Mage::getStoreConfig('glsbox/labels/beginx') != '' && (int)Mage::getStoreConfig('glsbox/labels/beginx') >=0 ) { $startpunktX = (int)Mage::getStoreConfig('glsbox/labels/beginx'); } else { $startpunktX = 0; } 
		if( Mage::getStoreConfig('glsbox/labels/beginy') != '' && (int)Mage::getStoreConfig('glsbox/labels/beginy') >=0 ) { $startpunktY = (int)Mage::getStoreConfig('glsbox/labels/beginy'); } else { $startpunktY = 0; }
		$startpunkt = array('x' => $startpunktX, 'y' => $startpunktY);	//Angabe in Milimeter
		
		$this->_startpunkt = array( 'x' => $this->mmToPts($startpunkt['x']), 'y' => $this->mmToPts($startpunkt['y']));

		$this->pdf = new Zend_Pdf();

		 // Erstelle eine neue Seite mit Hilfe des Zend_Pdf Objekts
		// (die Seite wird an das angegebene Dokument angehängt)
		
		if( Mage::getStoreConfig('glsbox/labels/papersize') != 'A4' && Mage::getStoreConfig('glsbox/labels/papersize') != 'A5' ) { $lettersize = Zend_Pdf_Page::SIZE_A4; } 
		else { if ( Mage::getStoreConfig('glsbox/labels/papersize') == 'A4' ) {$lettersize = Zend_Pdf_Page::SIZE_A4;} if(Mage::getStoreConfig('glsbox/labels/papersize') == 'A5') { $lettersize = '420.94:595.28'; } }
		$this->pdf->pages[] = ($this->page = $this->pdf->newPage($lettersize));
		 
		// Erstelle einen neuen Stil
		$this->defaultStyle = new Zend_Pdf_Style();
		$this->defaultStyle->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
		$this->defaultStyle->setLineColor(new Zend_Pdf_Color_GrayScale(0.2));
		$this->defaultStyle->setLineWidth(2);
		//$fontH = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
		//$this->defaultStyle->setFont($fontH, 32);
		 
		$this->page->setStyle($this->defaultStyle);

		$this->drawGeneralLayout();
	} 



	private function drawGeneralLayout(){
		$recstyle = new Zend_Pdf_Style();
		$recstyle->setLineWidth(0.5);
		$this->page->setStyle($recstyle);
		$this->page->drawRoundedRectangle($this->coordX(0) , $this->coordY(0) - $this->mmToPts(150), $this->coordX(0) + $this->mmToPts(100), $this->coordY(0),  $radius = array(15,15,15,15), $fillType = Zend_Pdf_Page::SHAPE_DRAW_STROKE);

		//General GLS-Layout	
		//x-achse | y-Achse | Länge | Dicke

		$ControlBar1 		= array('x' => 1, 'y' => 2, 'length' => 98, 'thick' => 1, 'horizontal' => true);
		$ControlBar2 		= array('x' => 1, 'y' => 15, 'length' => 98, 'thick' => 0.5, 'horizontal' => true);
		$ControlBar3 		= array('x' => 1, 'y' => 27.5, 'length' => 98, 'thick' => 0.5, 'horizontal' => true);
		$ControlBar4  		= array('x' => 1, 'y' => 56, 'length' => 98, 'thick' => 0.5, 'horizontal' => true);
		$Line1 				= array('x' => 1, 'y' => 62.5, 'length' => 98, 'thick' => 0.25, 'horizontal' => true);
		$Line2				= array('x' => 1, 'y' => 90, 'length' => 81.5, 'thick' => 0.25, 'horizontal' => true);
		$Line3 				= array('x' => 1, 'y' => 119, 'length' => 81.5, 'thick' => 0.25, 'horizontal' => true);
		$Line4 				= array('x' => 1, 'y' => 134.5, 'length' => 98, 'thick' => 0.25, 'horizontal' => true);
		$Line5 				= array('x' => 1, 'y' => 62.5, 'length' => 72, 'thick' => 0.25, 'horizontal' => false);
		$Line6 				= array('x' => 82.5, 'y' => 62.5, 'length' => 72, 'thick' => 0.25, 'horizontal' => false);
		$Line7 				= array('x' => 99, 'y' => 62.5, 'length' => 72, 'thick' => 0.25, 'horizontal' => false);
		
		$PrimaryCodeBorder1_1 		= array('x' => 1, 'y' => 28.5, 'length' => 5, 'thick' => 1, 'horizontal' => true);
		$PrimaryCodeBorder1_2 		= array('x' => 1.5, 'y' => 28.5, 'length' => 5, 'thick' => 1, 'horizontal' => false);
		
		$PrimaryCodeBorder2_1  		= array('x' => 1, 'y' => 55, 'length' => 5, 'thick' => 1, 'horizontal' => true);
		$PrimaryCodeBorder2_2  		= array('x' => 1.5, 'y' => 50, 'length' => 5, 'thick' => 1, 'horizontal' => false);

		$PrimaryCodeBorder3_1 		= array('x' => 22.5, 'y' => 28.5, 'length' => 5, 'thick' => 1, 'horizontal' => true);
		$PrimaryCodeBorder3_2 		= array('x' => 27, 'y' => 28.5, 'length' => 5, 'thick' => 1, 'horizontal' => false);

		$PrimaryCodeBorder4_1  		= array('x' => 22.5, 'y' => 55, 'length' => 5, 'thick' => 1, 'horizontal' => true);
		$PrimaryCodeBorder4_2  		= array('x' => 27, 'y' => 50, 'length' => 5, 'thick' => 1, 'horizontal' => false);		

		$LayoutCollection = array ($ControlBar1,$ControlBar2,$ControlBar3,$ControlBar4, $Line1,$Line2,$Line3,$Line4,$Line5,$Line6,$Line7,	$PrimaryCodeBorder1_1,$PrimaryCodeBorder1_2,$PrimaryCodeBorder2_1,$PrimaryCodeBorder2_2,$PrimaryCodeBorder3_1,$PrimaryCodeBorder3_2,$PrimaryCodeBorder4_1,$PrimaryCodeBorder4_2);
		 
		foreach ($LayoutCollection as $element) {

		 	// define a style
		  	$controlLayoutStyle = new Zend_Pdf_Style();
		 	$controlLayoutStyle->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
		  	$controlLayoutStyle->setLineColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
		  	$controlLayoutStyle->setLineWidth($this->mmToPts($element['thick']));
		  	$this->page->setStyle($controlLayoutStyle);

		  	if($element['horizontal']) {
		  		$this->page->drawLine($this->coordX($this->mmToPts($element['x'])), $this->coordY($this->mmToPts($element['y'])), $this->coordX($this->mmToPts($element['x']) + $this->mmToPts($element['length'])), $this->coordY($this->mmToPts($element['y'])));
		  	} else {
		  		$this->page->drawLine($this->coordX($this->mmToPts($element['x'])), $this->coordY($this->mmToPts($element['y'])), $this->coordX($this->mmToPts($element['x'])), $this->coordY($this->mmToPts($element['y']) + $this->mmToPts($element['length'])));
		  	} 
		}
		try {
			// Erstelle ein neues Grafikobjekt
			$imageFile = Mage::getBaseDir('media') . '/gls/images/GLS-Logo.jpg';
			$stampImage = Zend_Pdf_Image::imageWithPath($imageFile);
		} catch (Zend_Pdf_Exception $e) {
			// Beispiel wie man mit Ladefehlern bei Grafiken umgeht.
			$stampImage = null;
		}
		if ($stampImage != null) {
			$this->page->drawImage($stampImage, 
									$this->coordX(3) , 
									$this->coordY(0) - $this->mmToPts(149), 
									$this->coordX($this->mmToPts(99)) , 
									$this->coordY($this->mmToPts(150)) + $this->mmToPts(14)
								);
		}
		$this->page->setStyle($this->defaultStyle);	 
	}
		
 
 
	private function coordY($y){
		return $this->page->getHeight() - $y - $this->_startpunkt['y'];
	}

	private function coordX($x){
		return $this->_startpunkt['x'] + $x;
	}

	private function ptsToMm( $points ){
		return $points / 72 * 25.4;
	}

	private function mmToPts( $mm ){
		return $mm / 25.4 * 72;
	}	
 
    protected function drawFont($object){
		$fontItem = $object->getItem();

        $fontSize = $fontItem->getSize();
        $length = strlen($object->getValue());
        while($length >= 25){
            $fontSize = $fontSize-1;
            $length = $length-5;
        }

		if($fontItem->getFace() == 'bold') { $fontDecoration = 'bold'; } else { $fontDecoration = 'regular'; }	
		#$fontPath = Mage::getBaseDir('media').DS.'gls'.DS.'fonts'.DS.$fontDecoration.DS.$fontItem->getName().'.ttf';
        #if ( is_file( $fontPath ) ) {
        #    $font = Zend_Pdf_Font::fontWithPath($fontPath,Zend_Pdf_Font::EMBED_SUPPRESS_EMBED_EXCEPTION);
        #} else {
            if($fontDecoration == "bold") {
                $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
            }else{
                $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
            }
        #}

		if ($fontItem->getFace() == "invert") {
			$this->page->setFillColor(new Zend_Pdf_Color_Rgb(0,0,0)); //schwatze farbe setzen fuer Hintergrund
			$width = $this->widthForStringUsingFontSize($object->getValue(), $font, $fontSize);
			$height = $fontSize;
			$this->page->drawRectangle($this->coordX($this->mmToPts($object->getPosx())), $this->coordY($this->mmToPts($object->getPosy())) + $height/2, $this->coordX($this->mmToPts($object->getPosx())) + $width, $this->coordY($this->mmToPts($object->getPosy())) - $height/2, Zend_Pdf_Page::SHAPE_DRAW_FILL);
			$this->page->setFillColor(new Zend_Pdf_Color_Rgb(1,1,1)); //weiße farbe setzen fuer Text
			} else {
			$this->page->setFillColor(new Zend_Pdf_Color_Rgb(0,0,0)); //schwarze farbe setzen fuer text
			}		
		
		$this->page->setFont($font, $fontSize );
		
		if ($fontItem->getRotation() !== null) { $this->page->rotate($this->coordX($this->mmToPts($object->getPosx())), $this->coordY($this->mmToPts($object->getPosy())), deg2rad(360-$fontItem->getRotation())); }
		
		$this->page->drawText($object->getValue(), $this->coordX($this->mmToPts($object->getPosx())), $this->coordY($this->mmToPts($object->getPosy()) + ($fontSize/2) ), 'UTF-8');

		if ($fontItem->getRotation() !== null) { $this->page->rotate($this->coordX($this->mmToPts($object->getPosx())), $this->coordY($this->mmToPts($object->getPosy())), -deg2rad(360-$fontItem->getRotation())); }
	}

    protected function drawBarcode($object){
	
		$barcodeItem = $object->getItem();
		switch ($barcodeItem->getType()){
			case 'Code25interleaved': $barcode = new Zend_Barcode_Object_Code25interleaved(); break;
			case 'Code128': $barcode = new Zend_Barcode_Object_Code128(); break;
			
			default: $barcode = new Zend_Barcode_Object_Code25interleaved(); break;
		}
		$barcodeValue = $object->getValue();
		if ($barcodeItem->getType() == "Code128") {$barcodeValue = str_replace('.','',$object->getValue());} //Punkte entfernen bei Schweizer Barcode

		$barcode
			->setText($barcodeValue)
			->setDrawText(false)
			->setBarThinWidth($barcodeItem->getBarThinWidth())
			->setBarThickWidth($barcodeItem->getBarThickWidth())
			->setFactor($barcodeItem->getFactor())
			->setBarHeight($this->mmToPts($barcodeItem->getHeight()))
			->setWithQuietZones(true);
			

		$renderer = new Zend_Barcode_Renderer_Pdf();
		$renderer->setBarcode($barcode)->setResource($this->pdf)->setLeftOffset($this->coordX($this->mmToPts($object->getPosx())))->setTopOffset($this->mmToPts($object->getPosy()) + $this->_startpunkt['y'] );	
			
		$renderer->draw();
	}


	protected function drawDatamatrix($object) {

		$matrixItem = $object->getItem();

	  	$x        = 214/2;  // barcode center
	  	$y        = 214/2;  // barcode center
	  	$height   = 40;   // barcode height in 1D ; module size in 2D
	  	$width    = 40;    // barcode height in 1D ; not use in 2D
	  
	  	$code	 = str_replace("?", "|", $object->getValue());
	  	$type     = 'datamatrix';

	  	$im     = imagecreatetruecolor(214, 214);
	  	$black  = ImageColorAllocate($im,0x00,0x00,0x00);
	  	$white  = ImageColorAllocate($im,0xff,0xff,0xff);
	  	imagefilledrectangle($im, 0, 0, 214, 214, $white);
	
	  	$data = $matrixItem->gd($im, $black, $x, $y, 0, $type, array('code'=>$code));

		ob_start();
		imagepng($im);
		$imagestring = ob_get_contents();
		ob_end_clean();  
		imagedestroy($im);
	
		$temp = tempnam(sys_get_temp_dir(),'DMX');//tmpfile();
		$handle = fopen($temp,'w+b');
		fwrite($handle, $imagestring);
		fseek($handle,0);
	
		$image = new Zend_Pdf_Resource_Image_Png($temp);
		fclose($handle);
		//statt drawImage($image, $x1, $y1, $x2, $y2) nun drawImage($image, $x1, $y2, $x2, $y1) 
		$this->page->drawImage($image, 
								$this->coordX($this->mmToPts($object->getPosx())) , 
								$this->coordY($this->mmToPts($object->getPosy() +$matrixItem->getDimension())),
								$this->coordX($this->mmToPts($object->getPosx() +$matrixItem->getDimension())) , 
								$this->coordY($this->mmToPts($object->getPosy()))  

							);	
	}

	protected function widthForStringUsingFontSize($string, $font, $fontSize){
    	$drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $string);
     	$characters = array();
     	for ($i = 0; $i < strlen($drawingString); $i++) {
        	$characters[] = (ord($drawingString[$i++]) << 8 ) | ord($drawingString[$i]);
     	}
     	$glyphs = $font->glyphNumbersForCharacters($characters);
     	$widths = $font->widthsForGlyphs($glyphs);
     	$stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
     	return $stringWidth;
	}
}