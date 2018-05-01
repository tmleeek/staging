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
abstract class Gls_Unibox_Model_Label_Abstract 
{
	public $data;

	public function __construct() {
        $this->data = new Varien_Data_Collection();
        $this->insertDefaults();
	}
	
	public function getData() {
		return $this->data;
	}
	
	public function importValues($values){
		if(is_Array($values)){
            $this->nationalDeliveryInformation($values['100'],true);
                    $items = $this->data;
                        foreach($items as $item) {
                            if(array_key_exists($item->getTag(),$values)) { $item->setValue($values[$item->getTag()]); }
                        }
            $this->nationalDeliveryInformation($values['100'],false);
            if(array_key_exists('750',$values)){
                $this->bereinigeWennExpressversand($values['750']);
            }
		} 
	}

	protected function bereinigeWennExpressversand($code) {
        //lösche T100 der oberen rechten Ecke weil stattdessen T105 gedruckt werden muss
        if ($code != null) {
            $edit = $this->data->getItemsByColumnValue('tag', '100'); $edit[0]->setValue(null);
        }
	}
	
	protected function nationalDeliveryInformation($countryCode,$before) {
		if($before == true) {
			if ($countryCode == "CH") {
				$item = new Varien_Object(); $item->setTag('static')->setValue('D: Mit der Annahme akzeptieren sie allfg. Zoll- u. MwSt-Kosten via Rechnung zu bezahlen.')->setPosx(2)->setPosy(120)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(5) ); $this->data->addItem($item); $item = null;	
				$item = new Varien_Object(); $item->setTag('static')->setValue('F: En acceptant ce paquet vous vous engagez à régler la TVA et les frais de douane.')->setPosx(2)->setPosy(122)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(5) ); $this->data->addItem($item); $item = null;	
				$item = new Varien_Object(); $item->setTag('static')->setValue('SI+NOM')->setPosx(80)->setPosy(123)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6)->setRotation(90) ); $this->data->addItem($item); $item = null;	
				$item = new Varien_Object(); $item->setTag('690')->setValue(null)->setPosx(8)->setPosy(67)->setItem( Mage::getModel('glsbox/label_item_barcode')->setType('Code128')->setHeight(15)->setBarThickWidth(2)->setBarThinWidth(1)->setFactor(2.5) ); $this->data->addItem($item); $item = null;		
				$item = new Varien_Object(); $item->setTag('692')->setValue(null)->setPosx(31)->setPosy(88)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7)->setFace('bold') ); $this->data->addItem($item); $item = null;	

				$item = new Varien_Object(); $item->setTag('static')->setValue('0509')->setPosx(9)->setPosy(124)->setItem( Mage::getModel('glsbox/label_item_barcode')->setType('Code128')->setHeight(10)->setBarThickWidth(2)->setBarThinWidth(1)->setFactor(2) ); $this->data->addItem($item); $item = null;		
				$item = new Varien_Object(); $item->setTag('static')->setValue('1307')->setPosx(48)->setPosy(124)->setItem( Mage::getModel('glsbox/label_item_barcode')->setType('Code128')->setHeight(10)->setBarThickWidth(2)->setBarThinWidth(1)->setFactor(2) ); $this->data->addItem($item); $item = null;			
			}
			if ($countryCode == "GB") {
					
				$item = new Varien_Object(); $item->setTag('600')->setValue(null)->setPosx(9)->setPosy(63.5)->setItem( Mage::getModel('glsbox/label_item_barcode')->setType('Code128')->setHeight(21)->setBarThickWidth(3)->setBarThinWidth(1)->setFactor(2.2) ); $this->data->addItem($item); $item = null;		
				$item = new Varien_Object(); $item->setTag('600')->setValue(null)->setPosx(31)->setPosy(87.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(9)->setFace('bold') ); $this->data->addItem($item); $item = null;	
	
				$item = new Varien_Object(); $item->setTag('601')->setValue(null)->setPosx(13)->setPosy(125)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(22)->setFace('bold') ); $this->data->addItem($item); $item = null;	
				$item = new Varien_Object(); $item->setTag('602')->setValue(null)->setPosx(13)->setPosy(130)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(12)->setFace('bold') ); $this->data->addItem($item); $item = null;
				
				$item = new Varien_Object(); $item->setTag('603')->setValue(null)->setPosx(40)->setPosy(120)->setItem( Mage::getModel('glsbox/label_item_barcode')->setType('Code128')->setHeight(11)->setBarThickWidth(3)->setBarThinWidth(1)->setFactor(2.2) ); $this->data->addItem($item); $item = null;			
				$item = new Varien_Object(); $item->setTag('603')->setValue(null)->setPosx(60)->setPosy(132.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(5)->setFace('bold') ); $this->data->addItem($item); $item = null;
			}

			if ($countryCode == "SE") {
				$item = new Varien_Object(); $item->setTag('660')->setValue(null)->setPosx(7.5)->setPosy(65)->setItem( Mage::getModel('glsbox/label_item_barcode')->setType('Code128')->setHeight(18.5)->setBarThickWidth(2)->setBarThinWidth(1)->setFactor(2.4) ); $this->data->addItem($item); $item = null;		
				$item = new Varien_Object(); $item->setTag('660')->setValue(null)->setPosx(33)->setPosy(88)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7)->setFace('bold') ); $this->data->addItem($item); $item = null;	
				$item = new Varien_Object(); $item->setTag('static')->setValue('Colli-ID:')->setPosx(22)->setPosy(88)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7)->setFace('bold') ); $this->data->addItem($item); $item = null;		
			}

			if ($countryCode == "NL") {
				$item = new Varien_Object(); $item->setTag('620')->setValue(null)->setPosx(2)->setPosy(64)->setItem( Mage::getModel('glsbox/label_item_barcode')->setType('Code25interleaved')->setHeight(16.5)->setBarThickWidth(3)->setBarThinWidth(1)->setFactor(2.9) ); $this->data->addItem($item); $item = null;		
				$item = new Varien_Object(); $item->setTag('620')->setValue(null)->setPosx(31)->setPosy(88.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7)->setFace('bold') ); $this->data->addItem($item); $item = null;	
			}
		}
		if($before == false) {
			if ($countryCode == "CH" || $countryCode == "GB") {
			    //Entferne Beschriftungen und Values der letzten Box nur bei Schweiz und Großbritanien
				$this->data->getItemByColumnValue('tag', '8958')->setValue(null);
				$this->data->getItemByColumnValue('tag', '759')->setValue(null);
				$this->data->getItemByColumnValue('tag', '8959')->setValue(null);
				$this->data->getItemByColumnValue('tag', '758')->setValue(null);
				$notes = $this->data->getItemsByColumnValue('tag', '8960');
				$notes[0]->setValue(null);
				$notes[1]->setValue(null);
				$this->data->getItemByColumnValue('tag', '920')->setValue(null);
				$this->data->getItemByColumnValue('tag', '921')->setValue(null);
				$this->data->getItemByColumnValue('tag', '853')->setValue(null);
				$this->data->getItemByColumnValue('tag', '854')->setValue(null);
			}
		}	
	}

	protected function insertDefaults(){
        //defaults fuellen
        $item = new Varien_Object(); $item->setTag('static')->setValue('kg')->setPosx(49)->setPosy(59)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(9)->setFace('bold') ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('static')->setValue('RTG')->setPosx(70)->setPosy(59.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('static')->setValue('E2.00.0')->setPosx(90)->setPosy(59.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7) ); $this->data->addItem($item); $item = null;

        $item = new Varien_Object(); $item->setTag('110')->setValue(null)->setPosx(2.5)->setPosy(6.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(28) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('310')->setValue(null)->setPosx(24.5)->setPosy(6.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(28)->setFace('invert') ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('100')->setValue(null)->setPosx(57)->setPosy(6.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(28) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('100')->setValue(null)->setPosx(2)->setPosy(113)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;

        $item = new Varien_Object(); $item->setTag('101')->setValue(null)->setPosx(72)->setPosy(6.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(28)->setFace('invert') ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('320')->setValue(null)->setPosx(2.5)->setPosy(20)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(22) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('8951')->setValue(null)->setPosx(23)->setPosy(16)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('330')->setValue(null)->setPosx(22.5)->setPosy(21.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(10) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('8952')->setValue(null)->setPosx(41)->setPosy(16)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('8913')->setValue(null)->setPosx(40)->setPosy(21)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(12)->setFace('bold') ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('204')->setValue(null)->setPosx(72)->setPosy(19)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(12) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('203')->setValue(null)->setPosx(72)->setPosy(19)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(12) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('202')->setValue(null)->setPosx(76)->setPosy(19)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(12) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('200')->setValue(null)->setPosx(82)->setPosy(19)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(12) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('205')->setValue(null)->setPosx(88)->setPosy(19)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(12) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('201')->setValue(null)->setPosx(94)->setPosy(19)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(12) ); $this->data->addItem($item); $item = null;

        //bar & Qrcodes
        $item = new Varien_Object(); $item->setTag('8902')->setValue(null)->setPosx(4.5)->setPosy(31.5)->setItem( Mage::getModel('glsbox/label_item_datamatrix')->setBorder(true)->setDimension(20) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('8916')->setValue(null)->setPosx(30.5)->setPosy(30)->setItem( Mage::getModel('glsbox/label_item_barcode')->setType('Code25interleaved')->setHeight(20)->setBarThickWidth(2)->setBarThinWidth(1)->setFactor(2.2) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('8903')->setValue(null)->setPosx(75)->setPosy(31)->setItem( Mage::getModel('glsbox/label_item_datamatrix')->setDimension(22) ); $this->data->addItem($item); $item = null; //Qrcode

        $item = new Varien_Object(); $item->setTag('8916')->setValue(null)->setPosx(42)->setPosy(52.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;	//Freitext unter dem Barcode

        $item = new Varien_Object(); $item->setTag('500')->setValue(null)->setPosx(2.5)->setPosy(59)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(9) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('510')->setValue(null)->setPosx(14)->setPosy(59.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('540')->setValue(null)->setPosx(18)->setPosy(59.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('541')->setValue(null)->setPosx(31)->setPosy(59.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('530')->setValue(null)->setPosx(40)->setPosy(59)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(9)->setFace('bold') ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('8904')->setValue(null)->setPosx(55)->setPosy(59.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('static')->setValue("/")->setPosx(60)->setPosy(59.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7) ); $this->data->addItem($item); $item = null; // Trenner "/" von Paket 1 von 2
        $item = new Varien_Object(); $item->setTag('8905')->setValue(null)->setPosx(61)->setPosy(59.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('520')->setValue(null)->setPosx(76.5)->setPosy(59.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7) ); $this->data->addItem($item); $item = null;
        //$item = new Varien_Object(); $item->setTag('750')->setValue(null)->setPosx(2)->setPosy(65)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(14)->setFace('bold') ); $this->data->addItem($item); $item = null;
        //$item = new Varien_Object(); $item->setTag('751')->setValue(null)->setPosx(2)->setPosy(68)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        //$item = new Varien_Object(); $item->setTag('752')->setValue(null)->setPosx(2)->setPosy(71.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        //$item = new Varien_Object(); $item->setTag('753')->setValue(null)->setPosx(2)->setPosy(75)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        //$item = new Varien_Object(); $item->setTag('754')->setValue(null)->setPosx(2)->setPosy(78.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        //$item = new Varien_Object(); $item->setTag('755')->setValue(null)->setPosx(2)->setPosy(82)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        //$item = new Varien_Object(); $item->setTag('756')->setValue(null)->setPosx(2)->setPosy(85.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        //$item = new Varien_Object(); $item->setTag('757')->setValue(null)->setPosx(2)->setPosy(89)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('859')->setValue(null)->setPosx(2)->setPosy(91)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('851')->setValue(null)->setPosx(51)->setPosy(91)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('852')->setValue(null)->setPosx(59)->setPosy(91)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(7) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('860')->setValue(null)->setPosx(2)->setPosy(95)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(14)->setFace('bold') ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('861')->setValue(null)->setPosx(2)->setPosy(99)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('862')->setValue(null)->setPosx(2)->setPosy(103)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('863')->setValue(null)->setPosx(2)->setPosy(107)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(14)->setFace('bold') ); $this->data->addItem($item); $item = null;

        $item = new Varien_Object(); $item->setTag('330')->setValue(null)->setPosx(10)->setPosy(113)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('864')->setValue(null)->setPosx(28)->setPosy(113)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;

        $item = new Varien_Object(); $item->setTag('8958')->setValue(null)->setPosx(2)->setPosy(120)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('759')->setValue(null)->setPosx(13)->setPosy(120)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('8959')->setValue(null)->setPosx(2)->setPosy(123)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('758')->setValue(null)->setPosx(13)->setPosy(123)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('8960')->setValue(null)->setPosx(2)->setPosy(126)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('920')->setValue(null)->setPosx(13)->setPosy(126)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('8960')->setValue(null)->setPosx(2)->setPosy(129)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('921')->setValue(null)->setPosx(13)->setPosy(129)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('853')->setValue(null)->setPosx(2)->setPosy(132)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('854')->setValue(null)->setPosx(23)->setPosy(132)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(8) ); $this->data->addItem($item); $item = null;

        $item = new Varien_Object(); $item->setTag('800')->setValue(null)->setPosx(97.5)->setPosy(63.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6)->setRotation(90) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('8965')->setValue(null)->setPosx(97.5)->setPosy(85)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6)->setRotation(90) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('8915')->setValue(null)->setPosx(97.5)->setPosy(96)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6)->setRotation(90) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('8956')->setValue(null)->setPosx(97.5)->setPosy(110)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6)->setRotation(90) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('8914')->setValue(null)->setPosx(97.5)->setPosy(120)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6)->setRotation(90) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('810')->setValue(null)->setPosx(95)->setPosy(63.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6)->setRotation(90) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('811')->setValue(null)->setPosx(92.5)->setPosy(63.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6)->setRotation(90) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('812')->setValue(null)->setPosx(90)->setPosy(63.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6)->setRotation(90) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('820')->setValue(null)->setPosx(87.5)->setPosy(63.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6)->setRotation(90) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('821')->setValue(null)->setPosx(85)->setPosy(63.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6)->setRotation(90) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('822')->setValue(null)->setPosx(85)->setPosy(68.5)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6)->setRotation(90) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('823')->setValue(null)->setPosx(85)->setPosy(79)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6)->setRotation(90) ); $this->data->addItem($item); $item = null;
        $item = new Varien_Object(); $item->setTag('823')->setValue(null)->setPosx(85)->setPosy(79)->setItem( Mage::getModel('glsbox/label_item_font')->setSize(6)->setRotation(90) ); $this->data->addItem($item); $item = null;
	}
}