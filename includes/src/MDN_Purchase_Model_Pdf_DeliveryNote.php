<?php

class MDN_Purchase_Model_Pdf_DeliveryNote extends MDN_Purchase_Model_Pdf_Pdfhelper {

    private $_date = null;
    private $_po = null;
    
    /**
     * Main function to get the pdf
     * @param type $po
     * @param type $date
     */
    public function getPdfForDate($po, $date)
    {
        $this->_date = $date;
        $this->_po = $po;
        
        $lines = Mage::getSingleton('Purchase/Order_Delivery')->getDeliveredProducts($po, $date);
        
        return $this->getPdf($lines);
    }
    
    public function getPdf($lines = array()) {


        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        if ($this->pdf == null)
            $this->pdf = new Zend_Pdf();
        else
            $this->firstPageIndex = count($this->pdf->pages);

        $style = new Zend_Pdf_Style();
        $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);

        //add new page
        $titre = mage::helper('purchase')->__('PO %s delivery : %s', $this->_po->getpo_order_id(), Mage::helper('core')->formatDate($this->_date, 'short', false));
        $settings = array();
        $settings['title'] = $titre;
        $settings['store_id'] = 0;
        $page = $this->NewPage($settings);

        //table header
        $this->drawTableHeader($page);
        
        foreach ($lines as $line) {

            $product = $line['product'];
            
            $page->drawText($line['qty'], 15, $this->y, 'UTF-8');
            $page->drawText($product->getSku(), 50, $this->y, 'UTF-8');
            $page->drawText($product->getName(), 250, $this->y, 'UTF-8');
            $this->y -= 20;

            //new page if required
            if ($this->y < (150)) {
                $this->drawFooter($page);
                $page = $this->NewPage($settings);
                $this->drawTableHeader($page);
            }            
            
        }

        //Draw footer
        $this->drawFooter($page);

        //Display pages numbers
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
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);

        $page->drawText(mage::helper('purchase')->__('Qty'), 15, $this->y, 'UTF-8');
        $page->drawText(mage::helper('purchase')->__('Sku'), 50, $this->y, 'UTF-8');
        $page->drawText(mage::helper('purchase')->__('Product'), 250, $this->y, 'UTF-8');
 
        //barre grise fin entete colonnes
        $this->y -= 8;
        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);

        $this->y -= 15;
    }


}