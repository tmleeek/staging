<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2013 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Orderpreparation_Model_Pdf_OrderPreparationCommentsPdf extends MDN_Orderpreparation_Model_Pdf_Pdfhelper {

    protected $_currentOrderId = null;

    // ORDER BY ORDER PICKING LIST

    const MODE_ALL = 'ALL'; // Print button from the Tab Prepration from Sales -> Order -> Select an order
    const MODE_ORDER_PREPRATION_NOT_SELECTED_TAB = 'NOT_SELECTED'; // case massDownloadPreparationPdfAction from Other Tab that the "Selected tab"
    const MODE_ORDER_PREPRATION_SELECTED_TAB = 'SELECTED'; //case DownloadDocument Button + Picking List Button when order by order mode is set in options

    CONST X_POS_SKU = 160;
    CONST X_POS_NAME = 250;
    CONST X_POS_SHELF_LOCATION = 230;
    CONST X_POS_QTY = 540;

    protected $_selectedMode;

    /**
     * Alias to set a mode before calling getPdf
     * 
     */
    public function getPdfWithMode($order, $mode){
      $this->_selectedMode = $mode;   
      return $this->getPdf($order);
    }

    public function getPdf($order = array()) {

        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        if ($this->pdf == null)
            $this->pdf = new Zend_Pdf();
        else
            $this->firstPageIndex = count($this->pdf->pages);

        $this->_currentOrderId = $order->getincrement_id();

        $style = new Zend_Pdf_Style();
        $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);

        //cree la nouvelle page
        $titre = mage::helper('purchase')->__('Order #') . $order->getincrement_id();
        $settings = array();
        $settings['title'] = $titre;
        $settings['store_id'] = $order->getStoreId();
        $page = $this->NewPage($settings);

        //cartouche
        $txt_date = "Date :  " . mage::helper('core')->formatDate($order->getCreatedAt(), 'long');
        $txt_order = '';
        
        //$adresse_fournisseur = Mage::getStoreConfig('sales/identity/address');
        $customer = mage::getmodel('customer/customer')->load($order->getCustomerId());
        $adresse_client = mage::helper('purchase')->__('Shipping Address') . ":\n" . $this->FormatAddress($order->getShippingAddress(), '', false, $customer->gettaxvat());
        $adresse_fournisseur = mage::helper('purchase')->__('Billing Address') . ":\n" . $this->FormatAddress($order->getBillingAddress(), '', false, $customer->gettaxvat());
        $this->AddAddressesBlock($page, $adresse_fournisseur, $adresse_client, $txt_date, $txt_order);

        //Rajoute le carrier et la date d'expe pr�vue & les commentaires
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
        $this->y -=15;
        $page->drawText(mage::helper('purchase')->__('Shipping') . ' : ' . $order->getShippingDescription(), 15, $this->y, 'UTF-8');
        $this->y -=15;
        $comments = $this->WrapTextToWidth($page, $order->getmdn_comments(), 550);
        $offset = $this->DrawMultilineText($page, $comments, 15, $this->y, 10, 0.2, 11);
        $this->y -=10 + $offset;
        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);

        //affiche l'entete du tableau
        $this->drawTableHeader($page);
        $this->y -=10;

        //get items
        $preparationWarehouseId = mage::helper('Orderpreparation')->getPreparationWarehouse();
        $operatorId = mage::helper('Orderpreparation')->getOperator();
        $items = Mage::helper('Orderpreparation/Shipment')->GetItemsToShipAsArray($order->getId(), $preparationWarehouseId, $operatorId);
        
        //if array is empty, include all products
        if (count($items) == 0)
        {
            foreach($order->getAllItems() as $orderItem)
            {
                $items[$orderItem->getId()] = $orderItem->getqty_ordered();
            }
        }
                
        //SORT BY LOCATION
        if (mage::getStoreConfig('orderpreparation/picking_list/sort_mode') == 'location') {
            
            //get all locations
            $itemsByLocation = array();        
            foreach ($items as $orderItemId => $qty) {
              $itemsByLocation[$orderItemId] =  mage::getModel('sales/order_item')->load($orderItemId)->getShelfLocation();//return "" for product without stock management so OK
            }
            
            //order them
            if(count($itemsByLocation)>0){
                //order the list by value, so by shelf location
                asort($itemsByLocation);
                
                //order $items like $itemsByLocation
                $orderedItemsByLocation = array();
                foreach ($itemsByLocation as $orderItemId => $shelflocation) {
                    $orderedItemsByLocation[$orderItemId] = $items[$orderItemId];
                }
                
                //if operation is sucessfull, replace $item
                if(count($orderedItemsByLocation)>0){
                    $items = $orderedItemsByLocation;
                }
            }
        }      
        
        
        //Affiche le recap des produits
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
        
        
        foreach ($items as $orderItemId => $qty) {
            
            if ($qty == 0)
                continue;
            
            //Load product
            $item = mage::getModel('sales/order_item')->load($orderItemId);
            $productId = $item->getproduct_id();
            $product = mage::getModel('catalog/product')->load($productId);

            //Does not display products that dont manage stocks
            if (mage::getStoreConfig('orderpreparation/picking_list/display_product_without_stock_management') == 0) {
              if (!$product->getStockItem()->ManageStock()){
                  continue;
              }
            }

            //PICTURE
            if ($product->getSmallImage()) {
                $picturePath = Mage::getBaseDir() . DS . 'media' . DS . 'catalog' . DS . 'product' . $product->getSmallImage();
                if (file_exists($picturePath)) {
                    try {
                        $zendPicture = Zend_Pdf_Image::imageWithPath($picturePath);
                        $page->drawImage($zendPicture, 10, $this->y - 15, 10 + 30, $this->y - 15 + 30);
                    } catch (Exception $ex) {
                        //nothing
                    }
                }
            }

            //BARCODE - EAN
            $barcode = mage::helper('AdvancedStock/Product_Barcode')->getBarcodeForProduct($product);
            if ($barcode) {
                $picture = mage::helper('AdvancedStock/Product_Barcode')->getBarcodePicture($barcode);
                if ($picture) {
                    $zendPicture = $this->pngToZendImage($picture);
                    $page->drawImage($zendPicture, 60, $this->y - 15, 60 + 80, $this->y - 15 + 30);
                }
            }

            //PARENT
            //add configurable product sku + name above if possible
            if ($product->gettype_id() == 'simple'){
              $parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($item->getproduct_id());
              if(count($parentIds)>0){
                $productParent = Mage::getModel('catalog/product')->load($parentIds[0]);
                if($productParent->getId()>0) {
                  $this->y -= 5;
                  $configurableProductYshift = 15;
                  $configurableProductFontHeigth = 8;
                  $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_ITALIC), $configurableProductFontHeigth);
                  $page->drawText($this->TruncateTextToWidth($page, $productParent->getSku(), 70), self::X_POS_SKU, $this->y+$configurableProductYshift, 'UTF-8');
                  $name = $this->WrapTextToWidth($page, $productParent->getName(), self::X_POS_NAME);
                  $offset = $this->DrawMultilineText($page, $name, 300, $this->y+$configurableProductYshift, $configurableProductFontHeigth, 0.2, 11);
                }
              }
            }


            //SKU
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
            $page->drawText($this->TruncateTextToWidth($page, $product->getSku(), 70), self::X_POS_SKU, $this->y, 'UTF-8');

            //SHELF LOCATION
            $page->drawText($item->getShelfLocation(), self::X_POS_SHELF_LOCATION, $this->y, 'UTF-8');
            
            //NAME
            $name = $this->WrapTextToWidth($page, $product->getName(), self::X_POS_NAME);
            $offset = $this->DrawMultilineText($page, $name, 300, $this->y, 10, 0.2, 11);

            //QTY
            $qtySelected = 0;
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
            if (!$product->getStockItem()->ManageStock()){
              $qtySelected = $item->getqty_ordered();
            }else{
              if($this->_selectedMode == self::MODE_ALL){
                 $qtySelected = $item->getqty_ordered();
              }else if($this->_selectedMode == self::MODE_ORDER_PREPRATION_NOT_SELECTED_TAB){
                 $qtySelected = $item->getreserved_qty();
              }else if($this->_selectedMode == self::MODE_ORDER_PREPRATION_SELECTED_TAB){
                $qtySelected = mage::getModel('Orderpreparation/ordertoprepare')->GetTotalAddedQtyForProductForSelectedOrder($productId, $order->getId(), $preparationWarehouseId, $operatorId);
              }else{
                $qtySelected = $this->_selectedMode;
              }
            }
            $page->drawText((int)$qtySelected, self::X_POS_QTY, $this->y, 'UTF-8');

            
            //COMMENTS
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_ITALIC), 8);
            $this->y -= $this->_ITEM_HEIGHT;
            $caption = $this->WrapTextToWidth($page, $item->getcomments(), 300);
            $offset = $this->DrawMultilineText($page, $caption, 200, $this->y, 10, 0.2, 11);
            $this->y -= $offset;

            //CHILDs (optionnal) display child product for configurable or a bundle
            if (mage::getStoreConfig('orderpreparation/picking_list/display_sub_products') == 1) {              
              if ($product->gettype_id() == 'bundle' || $product->gettype_id() == 'configurable'){
                $this->y += 15;
                foreach ($items as $ssorderItemId => $ssqty) {
                  $ssItem = mage::getModel('sales/order_item')->load($ssorderItemId);
                  if ($ssItem->getparent_item_id() == $orderItemId) {
                    $subProductId = $ssItem->getproduct_id();
                    if($subProductId>0){
                     $subProduct = mage::getModel('catalog/product')->load($subProductId);
                     $subProductYshift = 9;
                     $subProductFontHeigth = 8;
                     $this->y -= $subProductFontHeigth + $subProductYshift;
                     $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_ITALIC), $subProductFontHeigth);
                     $page->drawText($this->TruncateTextToWidth($page, $subProduct->getSku(), 70), self::X_POS_SKU, $this->y+$subProductYshift, 'UTF-8');
                     $name = $this->WrapTextToWidth($page, $ssqty.'x '.$subProduct->getName(), self::X_POS_NAME);
                     $offset = $this->DrawMultilineText($page, $name, 300, $this->y+$subProductYshift, $subProductFontHeigth, 0.2, 11);
                    }
                  }
                }
              }
            }


            //LINE between 2 products
            $page->setLineWidth(0.5);
            $page->drawLine(10, $this->y - 4, $this->_BLOC_ENTETE_LARGEUR, $this->y - 4);
            $this->y -= $this->_ITEM_HEIGHT;

            //FOOTER or NEXT PAGE
            if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 40)) {
                $this->drawFooter($page);
                $page = $this->NewPage($settings);
                $this->drawTableHeader($page);
            }
        }

        //dessine le pied de page
        $this->drawFooter($page);

        //rajoute la pagination
        $this->AddPagination($this->pdf);

        $this->_afterGetPdf();

        return $this->pdf;
    }

    /**
     * Dessine l'entete du tableau avec la liste des produits
     *
     * @param unknown_type $page
     */
    public function drawTableHeader(&$page) {

        //entetes de colonnes
        $this->y -= 15;
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);

        $page->drawText(mage::helper('purchase')->__('Sku'), 160, $this->y, 'UTF-8');
        $page->drawText(mage::helper('purchase')->__('Location'), 230, $this->y, 'UTF-8');
        $page->drawText(mage::helper('purchase')->__('Name'), 300, $this->y, 'UTF-8');
        $page->drawText(mage::helper('purchase')->__('Quantity'), 530, $this->y, 'UTF-8');

        //barre grise fin entete colonnes
        $this->y -= 8;
        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);

        $this->y -= 15;
    }

  /**
     * Dessine l'entete de la page
     */
    public function drawHeader(&$page, $title, $StoreId = null) {


        if(!$StoreId){
          $StoreId = Mage::app()->getStore()->getStoreId();
        }
        
        //fond de l'entete
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.7));
        $page->drawRectangle(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y - $this->_BLOC_ENTETE_HAUTEUR, Zend_Pdf_Page::SHAPE_DRAW_FILL);

        // insert le logo
        $this->insertLogo($page, $StoreId);

        //rajoute l'adresse et coordon�es dans l'entete
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);
        $this->DrawMultilineText($page, Mage::getStoreConfig('purchase/general/header_text', $StoreId), 300, $this->y - 10, 10, 0, 15);

        //barre grise sous le bloc d'entete
        $this->y -= $this->_BLOC_ENTETE_HAUTEUR + 5;
        $page->setLineWidth(1.5);
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.1));
        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);

        //nom de l'objet
        $this->y -= 35;
        $name = $title;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 24);
        $this->drawTextInBlock($page, $name, 10, $this->y, $this->_PAGE_WIDTH, 50, 'l');

        //draw order barcode
        if (class_exists('Zend_Barcode'))
        {
            $barcodeOptions = array('text' => $this->_currentOrderId);
            $rendererOptions = array();
            $factory = Zend_Barcode::factory(
                    'Code128', 'image', $barcodeOptions, $rendererOptions
            );
            $image = $factory->draw();
            $zendPicture = $this->pngToZendImage($image);
            $barcodeWidth = 150;
            $barcodeHeight = 35;
            $page->drawImage($zendPicture, $this->_BLOC_ENTETE_LARGEUR - $barcodeWidth, $this->y - 10, $this->_BLOC_ENTETE_LARGEUR, $this->y - 10 + $barcodeHeight);
        }
        
        //barre grise sous le titre
        $this->y -= 20;
        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
    }


    protected function pngToZendImage($pngImage) {
        //save png image to disk
        $path = Mage::getBaseDir() . DS . 'var' . DS . 'barcode_image.png';
        imagepng($pngImage, $path);

        //create zend picture
        $zendPicture = Zend_Pdf_Image::imageWithPath($path);

        //delete file
        unlink($path);

        //return
        return $zendPicture;
    }

}

