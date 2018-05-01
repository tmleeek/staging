<?php

class MDN_DropShipping_Model_Pdf_PurchaseOrder extends MDN_Purchase_Model_Pdf_Order {

    public function getPdf($orders = array()) {
        $this->initLocale($orders);

        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        if ($this->pdf == null)
            $this->pdf = new Zend_Pdf();
        else
            $this->firstPageIndex = count($this->pdf->pages);

        $style = new Zend_Pdf_Style();
        $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);

        foreach ($orders as $order) {

            //add new page
            $titre = mage::helper('purchase')->__('Purchase Order');
            $settings = array();
            $settings['title'] = $titre;
            $settings['store_id'] = 0;
            $page = $this->NewPage($settings);

            //page header
            $txt_date = mage::helper('purchase')->__('Date') . ' : ' . mage::helper('core')->formatDate($order->getpo_date(), 'short');
            $txt_order = mage::helper('purchase')->__('N. ') . $order->getpo_order_id();
            $adresse_fournisseur = $order->getTargetWarehouse()->getstock_address();
            
            $saleOrder = Mage::helper('DropShipping/Order')->getSalesOrderForPurchaseOrder($order->getId());
            $adresse_client = $this->FormatAddress($saleOrder->getShippingAddress());
            $this->AddAddressesBlock($page, $adresse_fournisseur, $adresse_client, $txt_date, $txt_order);

//table header
            $this->drawTableHeader($page);

            $this->y -=10;
            $offset = 0;

//Display products
            foreach ($order->getProducts() as $item) {

                $this->y -= $offset;

//font initialization
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
                $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);

//display SKU or picture

                $x = self::LEFT_MARGIN_WIDTH;

                if ($this->_showPictures == false) {
                    $sku = $item->getpop_supplier_ref();
                    if ($sku == '')
                        $sku = $item->getsku();
                    $page->drawText($this->TruncateTextToWidth($page, $sku, 95), $x, $this->y, 'UTF-8');
                }
                else {
                    $product = mage::getModel('catalog/product')->load($item->getpop_product_id());
                    if ($product->getId()) {
                        $productImagePath = Mage::getBaseDir() . '/media/catalog/product' . $product->getsmall_image();
                        if (is_file($productImagePath)) {
                            try {
                                $image = Zend_Pdf_Image::imageWithPath($productImagePath);
                                $page->drawImage($image, 10, $this->y - $this->_pictureSize + 20, 5 + $this->_pictureSize, $this->y + 10);
                            } catch (Exception $ex) {
                                
                            }
                        }
                    }
                }
                $x += self::SKU_WIDTH;


//name
                $desc_with = $x + self::DESCRIPTION_WIDTH;
                if (mage::getStoreConfig('purchase/purchase_product_grid/display_weee') == 0) {
                    $desc_with += self::WEE_WIDTH;
                }

//WrapTextToWidth is not optimal because it depend of word width, so it need to be cut to 70% to avoid problem
                $realisticWidthToAvoidOverride = $desc_with * 0.7; //70%
                $caption = $this->WrapTextToWidth($page, $item->getpop_product_name(), $realisticWidthToAvoidOverride);

//add packaging name and Quantity to avoid misunderstanding of the supplier
                if ($item->getpop_packaging_id()) {
                    $caption .= "\n" . $item->getpop_packaging_name() . ' (' . $item->getpop_packaging_value() . 'x)';
                }
                $offset = $this->DrawMultilineText($page, $caption, $x, $this->y, 10, 0.2, 11);
                $x += self::DESCRIPTION_WIDTH;

                if (mage::getStoreConfig('purchase/purchase_product_grid/display_weee') == 0) {
                    $x += self::WEE_WIDTH;
                }

//unit price
                if ($order->getpo_status() != MDN_Purchase_Model_Order::STATUS_INQUIRY) {
                    $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($item->getpop_price_ht()), $x, $this->y, self::UNIT_PRICE_WIDTH, 20, 'l');
                }

                $x += self::UNIT_PRICE_WIDTH;

//qty
                $this->drawTextInBlock($page, (int) $item->getOrderedQty(), $x, $this->y, self::QTY_WIDTH, 20, 'l');
                $x += self::QTY_WIDTH;

//WEEE, tax rate, row total
                if ($order->getpo_status() != MDN_Purchase_Model_Order::STATUS_INQUIRY) {
                    if (mage::getStoreConfig('purchase/purchase_product_grid/display_weee') == 1) {
                        $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($item->getpop_eco_tax()), $x, $this->y, self::WEE_WIDTH, 20, 'l');
                        $x += self::WEE_WIDTH;
                    }
                    $this->drawTextInBlock($page, number_format($item->getpop_tax_rate(), 2) . '%', $x, $this->y, self::TAX_WIDTH, 20, 'l');
                    $x += self::TAX_WIDTH;

//Row total
                    $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($item->getRowTotal()), $x, $this->y, self::SUBTOTAL_WIDTH, 20, 'r');
                }


                if ($this->_showPictures)
                    $this->y -= $this->_pictureSize;
                else
                    $this->y -= $offset + 20;

//new page if required
                if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 40)) {
                    $this->drawFooter($page);
                    $page = $this->NewPage($settings);
                    $this->drawTableHeader($page);
                }
            }

//add shipping costs
            if ($order->getpo_status() != MDN_Purchase_Model_Order::STATUS_INQUIRY) {

                $xText = self::LEFT_MARGIN_WIDTH + self::SKU_WIDTH;
                $xValue = $xText + self::DESCRIPTION_WIDTH + self::UNIT_PRICE_WIDTH + self::WEE_WIDTH + self::QTY_WIDTH;

                $this->y -= ($this->_ITEM_HEIGHT - 20);

//Shipping cost
                $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);
                $this->DrawMultilineText($page, mage::helper('purchase')->__('Shipping costs'), $xText, $this->y, 10, 0.2, 11);
                $this->drawTextInBlock($page, number_format($order->getpo_tax_rate(), 2) . '%', $xValue, $this->y, self::TAX_WIDTH, 20, 'l');
                $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($order->getShippingAmountHt()), $x, $this->y, self::SUBTOTAL_WIDTH, 20, 'r');

//Tax & duties
                $this->y -= $this->_ITEM_HEIGHT;
                $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);
                $this->DrawMultilineText($page, mage::helper('purchase')->__('Taxes and Duties'), $xText, $this->y, 10, 0.2, 11);
                $this->drawTextInBlock($page, number_format($order->getpo_tax_rate(), 2) . '%', $xValue, $this->y, self::TAX_WIDTH, 20, 'l');
                $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($order->getZollAmountHt()), $x, $this->y, self::SUBTOTAL_WIDTH, 20, 'r');
            }

//new page if required
            if ($this->y < (150)) {
                $this->drawFooter($page);
                $page = $this->NewPage($settings);
                $this->drawTableHeader($page);
            }

//grey line
            $this->y -= 10;
            $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
            $VerticalLineHeight = 80;
            $page->drawLine($this->_PAGE_WIDTH / 2, $this->y, $this->_PAGE_WIDTH / 2, $this->y - $VerticalLineHeight);

//totals font
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 14);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
            $this->y -= 20;

//Comments
            $comments = $order->getpo_comments();
            if (($comments != '') && ($comments != null)) {
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));
                $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
                $page->drawText(mage::helper('purchase')->__('Comments'), 15, $this->y, 'UTF-8');
                $comments = $this->WrapTextToWidth($page, $comments, $this->_PAGE_WIDTH / 2);
                $this->DrawMultilineText($page, $comments, 15, $this->y - 15, 10, 0.2, 11);
            }
            if ($order->getpo_status() != MDN_Purchase_Model_Order::STATUS_INQUIRY) {
                $page->drawText(mage::helper('purchase')->__('Total (excl tax)'), $this->_PAGE_WIDTH / 2 + 10, $this->y, 'UTF-8');
                $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($order->getTotalHt()), $this->_PAGE_WIDTH / 2, $this->y, $this->_PAGE_WIDTH / 2 - 30, 40, 'r');
                $this->y -= 20;
                $page->drawText(mage::helper('purchase')->__('Tax'), $this->_PAGE_WIDTH / 2 + 10, $this->y, 'UTF-8');
                $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($order->getTaxAmount()), $this->_PAGE_WIDTH / 2, $this->y, $this->_PAGE_WIDTH / 2 - 30, 40, 'r');
                $this->y -= 20;
                $page->drawText(mage::helper('purchase')->__('Total (incl tax)'), $this->_PAGE_WIDTH / 2 + 10, $this->y, 'UTF-8');
                $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($order->getTotalTtc()), $this->_PAGE_WIDTH / 2, $this->y, $this->_PAGE_WIDTH / 2 - 30, 40, 'r');
            }

            $this->y -= 20;
            $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);

//Payment & shipping methods
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
            $this->y -= 20;
            $page->drawText(mage::helper('purchase')->__('Billing Method : ') . $order->getpo_payment_type(), 15, $this->y, 'UTF-8');
            $this->y -= 20;
            $page->drawText(mage::helper('purchase')->__('Carrier : ') . $order->getpo_carrier(), 15, $this->y, 'UTF-8');

            $this->y -= 20;
            $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);

//Static area
            $this->y -= 20;
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
            $page->drawText(mage::helper('purchase')->__('Comments : '), 15, $this->y, 'UTF-8');
            $this->y -= 20;
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
            $txt = Mage::getStoreConfig('purchase/general/pdf_comment');
            $txt = $this->WrapTextToWidth($page, $txt, $this->_PAGE_WIDTH - 100);
            $this->DrawMultilineText($page, $txt, 15, $this->y, 10, 0.2, 11);

//Draw footer
            $this->drawFooter($page);
        }

//Display pages numbers
        $this->AddPagination($this->pdf);

        $this->_afterGetPdf();

//reset language
        Mage::app()->getLocale()->revert();

        return $this->pdf;
    }

}