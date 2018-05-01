<?php
/**
 * Pdf model for packing slips
 * @author Arnaud P <arnaud@boostmyshop.com>
 * @version 0.3.0
 * @package MDN\UpsShipment\Model\Pdf
 * @todo Refactoring
 */

class MDN_Colissimo_Model_Pdf_PackingSlip extends MDN_Colissimo_Model_Pdf_Abstract
{
    private $_shipment = null;

    const SKU_CELL     = 110;
    const NAME_CELL    = 220;
    const QTY_CELL     = 70;

    private $_SKU_CELL     = 0;
    private $_NAME_CELL    = 0;
    private $_QTY_CELL     = 0;

    public function init(){
        $this->_NAME_CELL = Mage::getStoreConfig('colissimo/packing_slip/name_cell');
        $this->_SKU_CELL = Mage::getStoreConfig('colissimo/packing_slip/sku_cell');
        $this->_QTY_CELL = Mage::getStoreConfig('colissimo/packing_slip/qty_cell');
    }
    /**
     * Try to load a shipment before creating PDF
     */
    public function prepare($shipment)
    {

        if (is_object($shipment)) {
            if ($shipment->getId() > 0) {

                $this->_shipment = $shipment;
                return $this;
                return $shipment;

            } else {
                throw new Exception(Mage::helper('colissimo')->__('Shipment not found'));
            }
        }

        return false;
    }

    /**
     * Builds a packing slip's PDF with label on the right side
     */
    public function getPdf($shipments = array())
    {
        try{
            $this->init();
            $pdf = new Zend_Pdf();
            $this->_setPdf($pdf);

            $tmpdir = Mage::helper('colissimo/label')->getLabelsDirectory('temp');
            $tmpname = uniqid().'.pdf';
            if (!is_dir($tmpdir)) {
                mkdir($tmpdir, 0777, true);
            }

            foreach ($this->_shipment->getAllTracks() as $track) {
                $page = $this->newPage(array(
                    'page_size' => Zend_Pdf_Page::SIZE_A4_LANDSCAPE
                ));
                if(count($pdf->pages) == 0){
                    $this->_drawHeader($page);

                    $this->_drawAddresses($page);

                    $this->_drawProductsTable($page);

                    $this->_drawInfos($page);
                }
                $this->_drawLine($page, array(Mage::getStoreConfig('colissimo/packing_slip/separator_x_pos'), 20, Mage::getStoreConfig('colissimo/packing_slip/separator_x_pos'), $this->_pageHeight - 20));

                $this->x = 520; $this->y = $this->_pageHeight - 20;

                $this->addPage($page);

                file_put_contents($tmpdir. DS .$tmpname,$pdf->render());

                $string = $this->_insertLabel($tmpdir. DS .$tmpname, $track->getNumber());

                $pdf_fpdi = Zend_Pdf::parse($string);

                $pdf->pages[count($pdf->pages) - 1] = clone $pdf_fpdi->pages[0];
            }

            unlink($tmpdir.DS.$tmpname);
        }catch(Exception $e){
            Mage::throwException('Error creating PackingSlip : '.$e->getMessage());
            Mage::log("Creation du packing slip : " . $string . ' - ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
        }
        //return pdf as string
        return $pdf->render();
    }

    public function getPdfOverload($shipments = array())
    {

        try{
            $this->init();
            $pdf = new Zend_Pdf();
            $this->_setPdf($pdf);
            $string = '';

            $tmpdir = Mage::helper('colissimo/label')->getLabelsDirectory('temp');
            $tmpname = uniqid().'.pdf';
            if (!is_dir($tmpdir)) {
                mkdir($tmpdir, 0777, true);
            }
            foreach ($shipments as $shipment) {
                $this->prepare($shipment);
                foreach ($this->_shipment->getAllTracks() as $track) {
                    $page = $this->newPage(array(
                        'page_size' => Zend_Pdf_Page::SIZE_A4_LANDSCAPE
                    ));
                    if(count($pdf->pages) == 0){
                        $this->_drawHeader($page);

                        $this->_drawAddresses($page);

                        $this->_drawProductsTable($page);

                        $this->_drawInfos($page);
                    }

                    $this->_drawLine($page, array(Mage::getStoreConfig('colissimo/packing_slip/separator_x_pos'), 20, Mage::getStoreConfig('colissimo/packing_slip/separator_x_pos'), $this->_pageHeight - 20));

                    $this->x = 520; $this->y = $this->_pageHeight - 20;

                    $this->addPage($page);

                    file_put_contents($tmpdir. DS .$tmpname,$pdf->render());

                    $string = $this->_insertLabel($tmpdir. DS .$tmpname, $track->getNumber());

                    $pdf_fpdi = Zend_Pdf::parse($string);

                    $pdf->pages[count($pdf->pages) - 1] = clone $pdf_fpdi->pages[0];
                }
            }

            unlink($tmpdir.DS.$tmpname);

            //return zend pdf object
            return $pdf;
        }catch(Exception $e){
            Mage::getSingleton('adminhtml/session')->AddError('Error creating PackingSlip : '.$e->getMessage());
            Mage::log("Creation du packing slip : " . $string . ' - ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            Mage::helper('colissimo')->redirectReferrer();
        }

    }

    /**
     * Draw packing slip's header
     * @author Arnaud P <arnaud@boostmyshop.com>
     * @version 1.2.0
     * @param Zend_Pdf_Page $page Reference to current page
     * @return void
     */
    protected function _drawHeader(&$page)
    {
        $this->_insertLogo($page);

        /**
         * Building header
         */
        $this->_drawTitle($page, Mage::helper('colissimo')->__('Packing Slip'));

        $order = $this->_shipment->getOrder();

        /**
         * Insert order number and date
         */
        $this->_setFontRegular($page, 14);
        $this->x = 270; $this->y = $this->_pageHeight - 65;
        $page->drawText(Mage::helper('colissimo')->__('Order').' #' . $order->getincrement_id(), $this->x, $this->y, 'UTF-8');
        $this->x = 270; $this->y = $this->_pageHeight - 85;
        $page->drawText('Date : ' . date('Y-m-d', strtotime($order->getcreated_at())), $this->x, $this->y, 'UTF-8');
    }

    /**
     * Insert logo to pdf page
     * @author Arnaud P <arnaud@boostmyshop.com>
     * @version 1.2.0
     * @param Zend_Pdf_Page $page Reference to current page
     * @param object $store Current store view
     * @return void
     */
    protected function _insertLogo(&$page, $store = null)
    {
        list($pageWidth, $pageHeight) = explode(':', $this->_pageSize);

        $this->y = $this->y ? $this->y : $pageHeight;

        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;

        if(isset($modulesArray['MDN_GlobalPDF'])) {
            $image = Mage::getStoreConfig('globalpdf/general/logo', $store);
            if ($image)
                $image = Mage::getBaseDir('media') . DS . 'upload' . DS . 'image' . DS . $image;
        } else {
            $image = Mage::getStoreConfig('sales/identity/logo', $store);
            if ($image)
                $image = Mage::getBaseDir('media') . DS . 'sales' . DS . 'store' . DS . 'logo' . DS . $image;
        }

        if ($image) {
            // $image = Mage::getBaseDir('media') . DS . 'sales' . DS . 'store' . DS . 'logo' . DS . $image;

            if (is_file($image)) {
                $image       = Zend_Pdf_Image::imageWithPath($image);

                $top         = $this->y; //current cursor position
                $widthLimit  = 220; //half of the page width
                $heightLimit = 65; //assuming the image is not a "skyscraper"
                $width       = $image->getPixelWidth();
                $height      = $image->getPixelHeight();

                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
                if ($ratio > 1 && $width > $widthLimit) {
                    $width  = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $widthLimit;
                }

                $y1 = $top - $height;
                $y2 = $top;
                $x1 = $this->x;
                $x2 = $x1 + $width;

                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);

                $this->y = $this->y - ($height / 2);
                $this->x = $x2 + 10;
            }
        }
    }

    protected function _drawTitle(&$page, $title = 'Page Title')
    {
        $this->x = Mage::getStoreConfig('colissimo/packing_slip/title_x_pos'); $this->y = $this->_pageHeight - 45;

        $this->_setFontBold($page, 25);
        $this->_drawText($page, $title);
    }

    protected function _drawAddresses(&$page)
    {
        /**
         * Ship from address
         */
        $shipfrom = (object) Mage::getSingleton('colissimo/ConfigurationShipment')->getShipper();
        $this->x = 30; $this->y = $this->_pageHeight - 135;
        $this->_setFontBold($page, 15);
        $page->drawText(Mage::helper('colissimo')->__('Ship from').' :', $this->x, $this->y, 'UTF-8');
        $this->_setFontBold($page, 12);
        /*$this->x += 10;*/ $this->y -= 15;
        $page->drawText($shipfrom->addressVO['Name'], $this->x, $this->y, 'UTF-8');
        $this->y -= 15;
        $this->_setFontRegular($page, 12);

        //TODO line1/2/3
        for($i=0;$i<4;$i++){
            if(!empty($shipfrom->addressVO['line'.$i])){
                $page->drawText($shipfrom->addressVO['line'.$i], $this->x, $this->y, 'UTF-8');
                $this->y -= 15;
            }
        }

        $page->drawText($shipfrom->addressVO['postalCode'] . ' ' . $shipfrom->addressVO['city'] . ' ' . $shipfrom->addressVO['countryCode'], $this->x, $this->y, 'UTF-8');

        $this->y -= 15;
        $country_id = Mage::getModel('directory/country')->load($shipfrom->addressVO['countryCode'])->getId();
        $page->drawText(strtoupper(Mage::app()->getLocale()->getCountryTranslation($country_id)), $this->x, $this->y, 'UTF-8');


        /**
         * Ship to address
         */
        $shipto = $this->_shipment->getShippingAddress();
        $this->x = Mage::getStoreConfig('colissimo/packing_slip/shipto_x_pos'); $this->y = $this->_pageHeight - 135;
        $this->_setFontBold($page, 15);
        $page->drawText(Mage::helper('colissimo')->__('Ship to').' :', $this->x, $this->y, 'UTF-8');
        $this->_setFontBold($page, 12);
        /*$this->x += 10;*/ $this->y -= 15;
        $page->drawText($shipto->getname(), $this->x, $this->y, 'UTF-8');
        $this->y -= 15;
        $this->_setFontRegular($page, 12);
        $page->drawText($shipto->getstreet1(), $this->x, $this->y, 'UTF-8');
        $this->y -= 15;
        $page->drawText($shipto->getpostcode() . ' ' . $shipto->getcity() . ' ' . $shipto->getcountry(), $this->x, $this->y, 'UTF-8');
        $this->y -= 15;
        $country_id = Mage::getModel('directory/country')->load($shipto->getcountry())->getId();
        $page->drawText(strtoupper(Mage::app()->getLocale()->getCountryTranslation($country_id)), $this->x, $this->y, 'UTF-8');
    }

    protected function _drawInfos(&$page)
    {
        $this->x = 20; $this->y = ($this->_pageHeight - $this->_pageHeight) + 20;

        $this->_setFontRegular($page, 12);
        $shippingMethod = 'Shipping method : ' . $this->_shipment->getOrder()->getshipping_description();
        if ($this->_widthForStringUsingFontSize($shippingMethod, $page->getFont(), $page->getFontSize()) < 460 ) {
            $page->drawText($shippingMethod, $this->x, $this->y + 45, 'UTF-8');
        } else {
            $this->_drawMultilineText($page, $this->_wrapTextToWidth($page, $shippingMethod, 460), $this->x, $this->y + 60, 460, 15);
        }

        $paymentMethod = $this->_shipment->getOrder()->getPayment()->getMethodInstance()->getTitle();
        $this->_setFontRegular($page, 12);
        $page->drawText('Payment method : ' . $paymentMethod, $this->x, $this->y + 15, 'UTF-8');
    }

    protected function _drawCanvas(&$page)
    {
        /**
         * Separator between label and packing slip
         */
        /**
         * Heading block
         */
        $this->_drawLine($page, array(260, $this->_pageHeight - 20, 480, $this->_pageHeight - 20));
        $this->_drawLine($page, array(480, $this->_pageHeight - 20, 480, $this->_pageHeight - 100));
        $this->_drawLine($page, array(260, $this->_pageHeight - 100, 480, $this->_pageHeight - 100));
        $this->_drawLine($page, array(260, $this->_pageHeight - 20, 260, $this->_pageHeight - 100));

        /**
         * Shipfrom address
         */
        $this->_drawLine($page, array(20, $this->_pageHeight - 120, 240, $this->_pageHeight - 120));
        $this->_drawLine($page, array(240, $this->_pageHeight - 120, 240, $this->_pageHeight - 200));
        $this->_drawLine($page, array(240, $this->_pageHeight - 200, 20, $this->_pageHeight - 200));
        $this->_drawLine($page, array(20, $this->_pageHeight - 200, 20, $this->_pageHeight - 120));

        /**
         * Shipfrom address
         */
        $this->_drawLine($page, array(260, $this->_pageHeight - 120, 480, $this->_pageHeight - 120));
        $this->_drawLine($page, array(480, $this->_pageHeight - 120, 480, $this->_pageHeight - 200));
        $this->_drawLine($page, array(480, $this->_pageHeight - 200, 260, $this->_pageHeight - 200));
        $this->_drawLine($page, array(260, $this->_pageHeight - 200, 260, $this->_pageHeight - 120));

        /**
         * Comments area
         */
        $this->_drawLine($page, array(20, 100, 480, 100));
        $this->_drawLine($page, array(480, 100, 480, 20));
        $this->_drawLine($page, array(480, 20, 20, 20));
        $this->_drawLine($page, array(20,  20, 20, 100));
    }

    protected function _drawProductsTable(&$page)
    {
        $this->x = 20; $this->y = $this->_pageHeight - 220;

        /**
         * Table header
         */
        $table_width = $this->_SKU_CELL + $this->_QTY_CELL + $this->_NAME_CELL;

        $this->_drawLine($page, array($this->x, $this->y, $this->x + $table_width, $this->y));
        $this->_drawLine($page, array($this->x + $table_width, $this->y, $this->x + $table_width, $this->y - 20));
        $this->_drawLine($page, array($this->x, $this->y - 20, $this->x + $table_width, $this->y - 20));
        $this->_drawLine($page, array($this->x, $this->y - 20, $this->x, $this->y));
        /**
         * Table header cells
         */
        $this->_drawLine($page, array($this->x + $this->_QTY_CELL, $this->y, $this->x + $this->_QTY_CELL, $this->y - 20));
        $this->_setFontBold($page, 10);
        $page->drawText('Quantity', $this->x + 5, $this->y - 14, 'UTF-8');
        $this->x += $this->_QTY_CELL;

        $this->_drawLine($page, array($this->x + $this->_SKU_CELL, $this->y, $this->x + $this->_SKU_CELL, $this->y - 20));
        $this->_setFontBold($page, 10);
        $page->drawText('SKU', $this->x + 5, $this->y - 14, 'UTF-8');
        $this->x += $this->_SKU_CELL;

        //$this->_drawLine($page, array($this->x + $this->_NAME_CELL, $this->y, $this->x + $this->_NAME_CELL, $this->y - 20));
        $this->_setFontBold($page, 10);
        $page->drawText('Product name', $this->x + 5, $this->y - 14, 'UTF-8');
        $this->x += $this->_NAME_CELL;

        $this->x = 20; $this->y -= 20;

        /**
         * Products listing
         */
        $items = $this->_shipment->getAllItems();

        foreach ($items as $item) {

            $this->_setFontRegular($page, 10);

            if ($item->getOrderItem()->getParentItemId() == null ) {


                $page->drawText((int)$item->getqty(), $this->x + 5, $this->y - 14, 'UTF-8');
                $this->x += $this->_QTY_CELL;

                $page->drawText($item->getsku(), $this->x + 5, $this->y - 14, 'UTF-8');
                $this->x += $this->_SKU_CELL;

                if ($options = $item->getOrderItem()->getProductOptions()) {
                    $name = $item->getName().' ';
                    if ($this->_widthForStringUsingFontSize(trim($name), $page->getFont(), $page->getFontSize()) < 280) {
                        $name .= "\n";
                    }
                    if (isset($options['attributes_info'])) {
                        foreach ($options['attributes_info'] as $option) {
                            $name .= $option['label'] . ': ' . $option['value'];
                            $name .= ' | ';
                        }
                    }

                    $product = Mage::getModel('catalog/product')->loadByAttribute('sku',$item->getsku());
                    $desc = $product->getDescription();
//                    if($desc)
//                        $name .= wordwrap($desc,80,"\n");
                    // $name = $this->_wrapTextToWidth($page, trim($name), 280);

                    $this->_drawMultilineText($page, $name, $this->x + 5, $this->y -= 14, 460, 15);
                    $this->x = 20;
                    $this->_drawLine($page, array(20, $this->y - 20, 20, $this->y + 14));
                    $this->_drawLine($page, array($this->x + $this->_QTY_CELL, $this->y - 20, $this->x + $this->_QTY_CELL, $this->y + 14));
                    $this->x += $this->_QTY_CELL;
                    $this->_drawLine($page, array($this->x + $this->_SKU_CELL, $this->y - 20, $this->x + $this->_SKU_CELL, $this->y + 14));
                    $this->x += $this->_SKU_CELL;
                    $this->_drawLine($page, array($this->x + $this->_NAME_CELL, $this->y - 20, $this->x + $this->_NAME_CELL, $this->y + 14));
                } else {
                    $page->drawText($item->getname(), $this->x + 5, $this->y - 14, 'UTF-8');
                    $this->x = 20;
                    $this->_drawLine($page, array(20, $this->y - 20, 20, $this->y + 14));
                    $this->_drawLine($page, array($this->x + $this->_QTY_CELL, $this->y - 20, $this->x + $this->_QTY_CELL, $this->y + 14));
                    $this->x += $this->_QTY_CELL;
                    $this->_drawLine($page, array($this->x + $this->_SKU_CELL, $this->y - 20, $this->x + $this->_SKU_CELL, $this->y + 14));
                    $this->x += $this->_SKU_CELL;
                    $this->_drawLine($page, array($this->x + $this->_NAME_CELL, $this->y - 20, $this->x + $this->_NAME_CELL, $this->y + 14));
//                    $this->_drawLine($page, array($this->x, $this->y - 20, $this->x, $this->y));
//                    $this->_drawLine($page, array($this->x + $this->_QTY_CELL, $this->y, $this->x + $this->_QTY_CELL, $this->y - 20));
//                    $this->_drawLine($page, array($this->x + $this->_SKU_CELL, $this->y, $this->x + $this->_SKU_CELL, $this->y - 20));
//                    $this->_drawLine($page, array($this->x + $this->_NAME_CELL, $this->y, $this->x + $this->_NAME_CELL, $this->y - 20));
                }

                $this->x += $this->_NAME_CELL;

                $this->x = 20; $this->y -= 20;
                $this->_drawLine($page, array($this->x, $this->y, $this->x + $table_width, $this->y));
            }
        }
    }

    /**
     * Insert the label to the packing slip and return the modified page
     * @param $tmpfile
     * @param $trackingNumber
     * @return string pdf
     */
    protected function _insertLabel($tmpfile, $trackingNumber)
    {
        require_once(Mage::getBaseDir('lib') . '/fpdf/fpdf.php');
        require_once(Mage::getBaseDir('lib') . '/fpdi/fpdi.php');

        $label = Mage::helper('colissimo/Label')->getLabelsDirectory() . DS . $trackingNumber . '.pdf';

        $pdf = new FPDI();

        //$pdf->AddPage('L', 'A4', false);

        // set the source file

        $pdf->addPage('L','A4');
        $count = $pdf->setSourceFile($tmpfile);
        $tp = $pdf->importPage($count);
        $pdf->useTemplate($tp,0,0);

        //$pdf->addPage('P', array(100,150));
        $pdf->setSourceFile($label);
        $tpl = $pdf->importPage(1);

        // use the imported page and place it at point 0,0 with a width of 100 mm
        $x = Mage::getStoreConfig('colissimo/packing_slip/label_x_pos');
        $y = Mage::getStoreConfig('colissimo/packing_slip/label_y_pos');
        $new_width = 100;
        $new_height = 150;

        $pdf->useTemplate($tpl, $x, $y, $new_width, $new_height);

        return $pdf->Output('','S');
    }
}