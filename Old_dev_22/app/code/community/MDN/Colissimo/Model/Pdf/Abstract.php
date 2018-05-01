<?php 
/**
 * Abstract model to print pdf pages
 * @author Arnaud P <arnaud@boostmyshop.com>
 * @version 0.3.0
 * @package MDN\Colissimo\Model\Pdf
 * @todo Refactoring
 */

abstract class MDN_Colissimo_Model_Pdf_Abstract extends Varien_Object
{
    /**
     * X coordinate
     *
     * @var int
     */
    public $x = 0;

    /**
     * Y coordinate
     *
     * @var int
     */
    public $y = 0;

    protected $_pageSize = null;
    protected $_pageWidth = 0;
    protected $_pageHeight = 0;

    protected $pdf = null;

    abstract public function getPdf();

    abstract public function prepare($object);

    /**
     * Initialize renderer process
     *
     * @param string $type
     */
    protected function _initRenderer($type)
    {
        $node = Mage::getConfig()->getNode('global/pdf/' . $type);
        
        foreach ($node->children() as $renderer) {
            $this->_renderers[$renderer->getName()] = array(
                'model'     => (string)$renderer,
                'renderer'  => null
            );
        }
    }

    /**
     * Set PDF object
     *
     * @param  Zend_Pdf $pdf
     * @return Mage_Sales_Model_Order_Pdf_Abstract
     */
    protected function _setPdf(Zend_Pdf $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Retrieve PDF object
     *
     * @throws Mage_Core_Exception
     * @return Zend_Pdf
     */
    protected function _getPdf()
    {
        if (!$this->_pdf instanceof Zend_Pdf) {
            Mage::throwException(Mage::helper('sales')->__('Please define PDF object before using.'));
        }

        return $this->_pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        $pageSize = !empty($settings['page_size']) ? $settings['page_size'] : Zend_Pdf_Page::SIZE_A4;
        $this->_pageSize = $pageSize;
        list($this->_pageWidth, $this->_pageHeight) = explode(':', $this->_pageSize);

        $page = $this->_getPdf()->newPage($pageSize);
        

        /**
         * Cursor initialization (+20 corresponds to page margins) TODO : add margin setting
         */
        list($x, $y) = explode(':', $pageSize);
        $this->x = 20;
        $this->y = $y - 20;

        return $page;
    }

    public function addPage($page)
    {
        $this->_getPdf()->pages[] = $page;
    }

    /**
     * Set font as regular
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Re-4.4.1.ttf');
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as bold
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf');
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as italic
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_It-2.8.2.ttf');
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Insert logo to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param null $store
     */
    protected function _insertLogo(&$page, $store = null)
    {
        list($pageWidth, $pageHeight) = explode(':', $this->_pageSize);

        $this->y = $this->y ? $this->y : $pageHeight;
        $image = Mage::getStoreConfig('sales/identity/logo', $store);
        if ($image) {
            $image = Mage::getBaseDir('media') . DS . 'sales' . DS . 'store' . DS . 'logo' . DS . $image;
            
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
        $this->x = 270; $this->y = $this->_pageHeight - 45;

        $this->_setFontBold($page, 25);
        $this->_drawText($page, $title);
    }

    protected function _drawText(&$page, $text)
    {
        $page->drawText($text, $this->x, $this->y, 'UTF-8');
    }

    protected function _drawMultilineText(&$page, $text, $x, $y, $size, $lineHeight) 
    {
        $retour = -$lineHeight;
        foreach (explode("\n", $text ) as $value) {
            if ($value !== '') {
                $page->drawText(trim(strip_tags($value)), $x, $y, 'UTF-8');
                $y -= $lineHeight;
                $retour += $lineHeight;
            }
        }
        return $retour;
    }

    protected function _drawLine(&$page, $coords = array()) 
    {
        if (!empty($coords)) {
            $x1 = $coords[0];
            $y1 = $coords[1];
            $x2 = $coords[2];
            $y2 = $coords[3];

            $page->drawLine($x1, $y1, $x2, $y2);
        }
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

    protected function _drawHeader(&$page)
    {
        $this->_insertLogo($page);

        /**
         * Building header
         */
        $this->_drawTitle($page, Mage::helper('colissimo')->__('Packing Slip'));
    }

    protected function _wrapTextToWidth(&$page, $text, $maxWidth) 
    {
        $words = explode(' ', $text);
        $nbWords = count($words);
        $font = $page->getFont();
        $fontSize = $page->getFontSize();

        $return = '';
        $currentLine = '';
        $i = 0;
        foreach ($words as $word) {
            $size = $this->_widthForStringUsingFontSize($currentLine . ' ' . $word, $font, $fontSize);
            if ($size < $maxWidth) {
                $currentLine .= ' ' . $word;
            } else {
                $return .= $currentLine . "\r\n";
                $currentLine = $word;
            }
            $i++;
        }
        $return .= $currentLine;
        return trim($return);
    }

    protected function _widthForStringUsingFontSize($string, $font, $fontSize) 
    {
        try {
            //fix iconv issue
            $workingString = '';
            for ($i = 0; $i < strlen($string); $i++) {
                if (ord($string{$i}) < 128)
                    $workingString .= $string{$i};
            }

            $drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $workingString);
            $characters = array();
            for ($i = 0; $i < strlen($drawingString); $i++) {
                $characters[] = (ord($drawingString[$i++]) << 8 ) | ord($drawingString[$i]);
            }
            $glyphs = $font->glyphNumbersForCharacters($characters);
            $widths = $font->widthsForGlyphs($glyphs);
            $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
            return (int)$stringWidth;
        } catch (Exception $ex) {
            Mage::log("Erreur dans Mdn pdf helper methode widthForStringUsingFontSize avec string = " . $string . ' - ' . $ex->getMessage() . ' - ' . $ex->getTraceAsString());
        }
    }
}
