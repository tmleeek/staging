<?php 
/**
 * Model for UPS labels PDF
 * @author Arnaud P <arnaud@boostmyshop.com>
 * @version 1.1.0
 * @package MDN/Colissimo/Model/Pdf
 */

class MDN_Colissimo_Model_Pdf_Label extends MDN_Colissimo_Model_Pdf_Abstract
{
    private $_files = array();

    /**
     * Try to load a shipment before creating PDF
     */
    public function prepare($shipment)
    {
        if (is_object($shipment)) {
            if ($shipment->getId() > 0) {

                $this->_shipment = $shipment;
                
                return $this;

            } else {
                throw new Exception(Mage::helper('Colissimo')->__('Shipment not found'));
            }
        }
        
        return false;
    }

    public function getPdf()
    {
        require_once(Mage::getBaseDir('lib') . '/fpdf/fpdf.php');
        require_once(Mage::getBaseDir('lib') . '/fpdi/fpdi.php');

        if (is_object($this->_shipment)) {

            $tracks = $this->_shipment->getTracksCollection();

            if (sizeof($tracks) > 0) {

                $pdf = new FPDI();
                foreach ($tracks as $track) {
                    $this->_files[] = Mage::helper('colissimo/Label')->getLabelsDirectory('shipment').DS.$track->getNumber().'.pdf';
                }
                $pdf = $this->_concat($pdf);
                
                return $pdf->Output('','S');;
            } else {
                throw new Exception('There is no tracking for this shipment');
            }
        } else {
            throw new Exception('Please load a shipment with prepare() before using');
        }
    }

    protected function _concat($pdf)
    {
        foreach($this->_files AS $file) {
            $pageCount = $pdf->setSourceFile($file);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplIdx = $pdf->ImportPage($pageNo);
                $s = $pdf->getTemplatesize($tplIdx);
                $pdf->AddPage($s['w'] > $s['h'] ? 'L' : 'P', array($s['w'], $s['h']));
                $pdf->useTemplate($tplIdx);
            }
        }
        return $pdf;
    }


}