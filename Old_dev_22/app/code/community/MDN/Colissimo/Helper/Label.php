<?php 
/**
 * Label helper for ColissimoShipment
 * @author Arnaud P <arnaud@boostmyshop.com>
 * @version 1.0.0
 * @package MDN\ColissimoSHipment\Helper
 */

class MDN_Colissimo_Helper_Label extends Mage_Core_Helper_Abstract
{
    /**
     * Saves a label to JPG portrait format
     * @param string $shipmentResponse XML response from Colissimo
     * @param string $objectType Object type currently handled
     * @return bool Whether or not image saving succeeded
     */
    public function saveLabel($shipmentResponse, $objectType = 'shipment')
    {
        if ($objectType != '') {
            try{
                $pdfUrl = $shipmentResponse->PdfUrl;
                $labelDirectory = $this->getLabelsDirectory($objectType);

                $trackingNumber = $shipmentResponse->parcelNumber;

                if (!is_dir($labelDirectory)) {
                    mkdir($labelDirectory, 0777, true);
                }

                $tmpFile = $labelDirectory . DS . $trackingNumber . '.pdf.tmp';
                $endFile = $labelDirectory . DS . $trackingNumber . '.pdf';

                $pdfContent = file_get_contents($pdfUrl);

                if(file_put_contents($tmpFile, $pdfContent)){
                    if($objectType == 'shipment'){
                        $newLabel = $this->labelJobEnvoi($tmpFile);
                    }elseif($objectType == 'return'){
                        $newLabel = $pdfContent;
                        //TODO labelJobRetour();
                    }

                    if($newLabel != false){
                        if (file_put_contents($endFile, $newLabel)) {
                            unlink($tmpFile);
                            return file_get_contents($endFile);
                        }
                    }
                }else{
                    Mage::throwexception('Fail writting "'.$tmpFile.'"');
                }
            }catch(Exception $e){
                Mage::throwexception('Save Label "'.$e->getMessage().'"');
                Mage::log('saveLabel : '.$e->getMessage().' '.$e->getTraceAsString(),'colissimopdf.log' );

            }
        }
        
        return false;
    }

    /**
     * Returns path to labels directory
     * @param string
     * @return string Path to labels directory
     */
    public function getLabelsDirectory($objectType = 'shipment')
    {
        $path = Mage::getStoreConfig('colissimo/labels/label_path');
        if(preg_match("/^\/media\//",$path)){
            return Mage::getBaseDir('media') . DS . 'colissimo_shipments' . DS . 'labels' . DS . $objectType;
        }else{
            return  BASE_DIR . Mage::getStoreConfig('colissimo/labels/label_path'). DS . $objectType;
        }
    }

    /**
     * Called by magento cron task to clean label directory
     * @author Arnaud P <arnaud@boostmyshop.com>
     * @version 1.0.0
     * @param void
     * @return void
     */
    public function cleanLabelDirectories()
    {
        $this->_cleanLabelDirectory('shipment');
        $this->_cleanLabelDirectory('return');
        $this->_cleanLabelDirectory('temp');
    }

    /**
     * Cleans label directory from old labels
     * @author Arnaud P <arnaud@boostmyshop.com>
     * @version 1.0.0
     * @param string $labelsType Object labels to clean
     * @return void
     */
    protected function _cleanLabelDirectory($labelsType = 'shipments')
    {
        $labelsTTL = Mage::getStoreConfig('colissimo/labels/labels_lifetime') != '' ? Mage::getStoreConfig('colissimo/labels/labels_lifetime') : '15';

        $labelsTTL = $labelsTTL * 24 * 3600;

        $now = time();

        $dir = $this->getLabelsDirectory($labelsType);
        
        if (false === ($src = opendir($dir))) {
            throw new Exception('Error while opening labels directory !');
        }

        while ($entry = @readdir($src)) {
            if ($entry != '.' && $entry != '..') {
                if ($now - filemtime($dir . DS . $entry) >= $labelsTTL) {
                    unlink($dir. DS . $entry);
                }
            }
        }
        closedir($src);
    }

    /**
     * Cut and resize given colissimo label to fit 150*100
     * @param $tmpFile
     * @return pdf string
     */
    public function labelJobEnvoi($tmpFile){
        require_once(Mage::getBaseDir('lib') . '/fpdf/fpdf.php');
        require_once(Mage::getBaseDir('lib') . '/fpdi/fpdi.php');

        try{
            $pdf = new FPDI();

            // add a page format 100mm(w) * 150mm (h)
            $width = Mage::getStoreConfig('colissimo/labels/label_width');
            $height = Mage::getStoreConfig('colissimo/labels/label_height');
            $pdf->AddPage('P', array($width, $height), false);

            // set the source file
            $pdf->setSourceFile($tmpFile);

            // import page 1
            $tplIdx = $pdf->importPage(1);

            // use the imported page and place it at point 0,0 with a width of 100 mm
            $x = Mage::getStoreConfig('colissimo/labels/label_x');
            $y = Mage::getStoreConfig('colissimo/labels/label_y');
            $new_width = Mage::getStoreConfig('colissimo/labels/label_new_width');
            $new_height = Mage::getStoreConfig('colissimo/labels/label_new_height');

            $pdf->useTemplate($tplIdx, $x, $y, $new_width, $new_height);

            return $pdf->Output('','S');

        }catch(Exception $e){
            Mage::throwException('Error occured in PDF job : '.$e->getMessage());
            Mage::log('Error occured in PDF job : '.$e->getMessage().' '.$e->getTraceAsString(),'colissimopdf.log' );
        }

    }
}