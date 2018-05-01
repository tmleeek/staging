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

    protected function _drawItem(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order)
    {
        $type = $item->getProductType();
        $renderer = $this->_getRenderer($type);
        $renderer->setOrder($order);
        $renderer->setItem($item);
        $renderer->setPdf($this);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);

        $renderer->draw();
    }

    public function getPdf($orders = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('order');

        $pdf = new Zend_Pdf();
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($orders as $order) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($order['increment_id']);

            if ($this->getStoreId()) {
                Mage::app()->getLocale()->emulate($this->getStoreId());
            }
            $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
            $pdf->pages[] = $page;

            /* Add image */
            $this->insertLogo($page, $this->getStoreId());

            /* Add address */
            $this->insertAddress($page, $this->getStoreId());

            /* Add head */
            $this->insertOrder($page, $order, false);


            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $this->_setFontRegular($page);
            $page->drawText(Mage::helper('sales')->__('Order # ') . $order->getIncrementId(), 35, 780, 'UTF-8');

            /* Add table */
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);

            $page->drawRectangle(25, $this->y, 570, $this->y -15);
            $this->y -=10;

            /* Add table head */
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
            $page->drawText(Mage::helper('sales')->__('Product'), 35, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('SKU'), 240, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Price'), 380, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('QTY'), 430, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Tax'), 480, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Subtotal'), 535, $this->y, 'UTF-8');

            $this->y -=15;

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            /* Add body */
            foreach ($order->getAllItems() as $item){
                if ($item->getParentItem()) {
                    continue;
                }

                $shift = array();
                if ($this->y<15) {
                    /* Add new table head */
                    $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
                    $pdf->pages[] = $page;
                    $this->y = 800;

                    $this->_setFontRegular($page);
                    $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
                    $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                    $page->setLineWidth(0.5);
                    $page->drawRectangle(25, $this->y, 570, $this->y-15);
                    $this->y -=10;

                    $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
                    $page->drawText(Mage::helper('sales')->__('Product'), 35, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('SKU'), 240, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('Price'), 380, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('QTY'), 430, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('Tax'), 480, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('Subtotal'), 535, $this->y, 'UTF-8');

                    $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                    $this->y -=20;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
            }

            /* Add totals */
            $page = $this->insertTotals($page, $order);

            if ($this->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
        }

        $this->_afterGetPdf();
        return $pdf;
    }

    protected function insertTotals($page, $source){
        $order = $source;
//        $font = $this->_setFontBold($page);

        $totals = $this->_getTotalsList($source);

        $lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );
        foreach ($totals as $total) {
            $amount = $source->getDataUsingMethod($total['source_field']);
            $displayZero = (isset($total['display_zero']) ? $total['display_zero'] : 0);

            if ($amount != 0 || $displayZero) {
                $amount = $order->formatPriceTxt($amount);

                if (isset($total['amount_prefix']) && $total['amount_prefix']) {
                    $amount = "{$total['amount_prefix']}{$amount}";
                }

                $fontSize = (isset($total['font_size']) ? $total['font_size'] : 7);
                //$page->setFont($font, $fontSize);

                $label = Mage::helper('sales')->__($total['title']) . ':';

                $lineBlock['lines'][] = array(
                    array(
                        'text'      => $label,
                        'feed'      => 475,
                        'align'     => 'right',
                        'font_size' => $fontSize,
                        'font'      => 'bold'
                    ),
                    array(
                        'text'      => $amount,
                        'feed'      => 565,
                        'align'     => 'right',
                        'font_size' => $fontSize,
                        'font'      => 'bold'
                    ),
                );
            }
        }

        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
    }  
}