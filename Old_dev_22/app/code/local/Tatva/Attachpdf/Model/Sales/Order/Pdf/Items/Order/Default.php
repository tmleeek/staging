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

 * Sales Order Invoice Pdf default items renderer

 *

 * @category   Nostress

 * @package    Nostress_Invoicetemplates

 * @author     NoStress Commerce Team <info@nostresscommerce.cz>

 */



class Tatva_Attachpdf_Model_Sales_Order_Pdf_Items_Order_Default extends Mage_Sales_Model_Order_Pdf_Items_Abstract

{







    public function getSku($item)

    {

        if ($item->getProductOptionByCode('simple_sku'))

            return $item->getProductOptionByCode('simple_sku');

        else

            return $item->getSku();

    }



    public function getItemOptions() {

        $result = array();

        if ($options = $this->getItem()->getProductOptions()) {

            if (isset($options['options'])) {

                $result = array_merge($result, $options['options']);

            }

            if (isset($options['additional_options'])) {

                $result = array_merge($result, $options['additional_options']);

            }

            if (isset($options['attributes_info'])) {

                $result = array_merge($result, $options['attributes_info']);

            }

        }

        return $result;

    }



    public function draw()

    {

        $order  = $this->getOrder();

        $item   = $this->getItem();

        $pdf    = $this->getPdf();

        $page   = $this->getPage();

        $shift  = array(0, 10, 0);



        $this->_setFontRegular();



        $page->drawText($item->getQty()*1, 435, $pdf->y, 'UTF-8');



        /* in case Product name is longer than 80 chars - it is written in a few lines */

        foreach (Mage::helper('core/string')->str_split($item->getName(), 60, true, true) as $key => $part) {

            $page->drawText($part, 35, $pdf->y-$shift[0], 'UTF-8');

            $shift[0] += 10;

        }



        $options = $this->getItemOptions();

        if (isset($options)) {

            foreach ($options as $option) {

                // draw options label

                $this->_setFontItalic();

                foreach (Mage::helper('core/string')->str_split(strip_tags($option['label']), 60, false, true) as $_option) {

                    $page->drawText($_option, 35, $pdf->y-$shift[0], 'UTF-8');

                    $shift[0] += 10;

                }

                // draw options value

                $this->_setFontRegular();

                if ($option['value']) {

                    $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);

                    $values = explode(', ', $_printValue);

                    foreach ($values as $value) {

                        foreach (Mage::helper('core/string')->str_split($value, 60,true,true) as $_value) {

                            $page->drawText($_value, 40, $pdf->y-$shift[0], 'UTF-8');

                            $shift[0] += 10;

                        }

                    }

                }

            }

        }



        foreach ($this->_parseDescription() as $description){

            $page->drawText(strip_tags($description), 65, $pdf->y-$shift[1], 'UTF-8');

            $shift[1] += 10;

        }



        /* in case Product SKU is longer than 36 chars - it is written in a few lines */

        foreach (Mage::helper('core/string')->str_split($this->getSku($item), 25) as $key => $part) {

            if ($key > 0) {

                $shift[2] += 10;

            }

            $page->drawText($part, 240, $pdf->y-$shift[2], 'UTF-8');

        }



        $font = $this->_setFontBold();
         /* added for incl price */
             $_product = Mage::getModel('catalog/product')->load($item->getProductId());
             $weee_amt = Mage::helper('weee')->getAmount($_product);
             $item_amt_without_weee = $item->getRowTotal()- $weee_amt;
    		 $item_tax = $item_amt_without_weee * $item->getTaxPercent ()/100;
    		 $item_row_total = number_format($item_amt_without_weee + $item_tax,2);
             //$string = Mage::helper ( 'core' )->formatPrice ( $item_price, false );
         /* end */
        $row_total = $order->formatPriceTxt($item_row_total);

        $page->drawText($row_total, 565-$pdf->widthForStringUsingFontSize($row_total, $font, 7), $pdf->y, 'UTF-8');


        $item_amt_without_weee = $item->getPrice()- $weee_amt;
    	$item_tax = $item_amt_without_weee * $item->getTaxPercent ()/100;
    	$item_price = number_format($item_amt_without_weee + $item_tax,2);
        $price = $order->formatPriceTxt($item_price);

        $page->drawText($price, 395-$pdf->widthForStringUsingFontSize($price, $font, 7), $pdf->y, 'UTF-8');



        $tax = $order->formatPriceTxt($item->getTaxAmount());

        $page->drawText($tax, 495-$pdf->widthForStringUsingFontSize($tax, $font, 7), $pdf->y, 'UTF-8');



        $pdf->y -= max($shift)+10;

    }

}