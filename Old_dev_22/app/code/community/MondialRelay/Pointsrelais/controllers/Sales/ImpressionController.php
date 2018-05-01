<?php
/*define('MAGENTO', realpath(dirname(__FILE__))); 
require_once(MAGENTO.'/PDFMerger/PDFMerger.php');*/
class MondialRelay_Pointsrelais_Sales_ImpressionController extends Mage_Adminhtml_Controller_Action
{
    protected $_trackingNumbers = array();
    
    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('MondialRelay_Pointsrelais');
    }


    /**
     * Shipping grid
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/pointsrelais')
            ->_addContent($this->getLayout()->createBlock('pointsrelais/sales_impression'))
            ->renderLayout();
    }
    
	public function getConfigData($field)
	{
        $path = 'carriers/pointsrelais/'.$field;
        return Mage::getStoreConfig($path, Mage::app()->getStore());
	}
    
    protected function _processDownload($resource, $resourceType)
    {
        $helper = Mage::helper('downloadable/download');
        /* @var $helper Mage_Downloadable_Helper_Download */

        $helper->setResource($resource, $resourceType);

        $fileName       = $helper->getFilename();
        $contentType    = $helper->getContentType();

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true);

        if ($fileSize = $helper->getFilesize()) {
            $this->getResponse()
                ->setHeader('Content-Length', $fileSize);
        }

        if ($contentDisposition = $helper->getContentDisposition()) {
            $this->getResponse()
                ->setHeader('Content-Disposition', $contentDisposition . '; filename='.$fileName);
        }

        $this->getResponse()
            ->clearBody();
        $this->getResponse()
            ->sendHeaders();

        $helper->output();
    }
    
    protected function getTrackingNumber($shipmentId)
    {
                Mage::Log('***getTrackingNumber****');
Mage::Log('***getTrackingNumber**** 1 : '.$shipmentId);
        $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
        $trackingNumbersToReturn = array();
        //On récupère le numéro de tracking
        $tracks = $shipment->getTracksCollection();
        //->addAttributeToFilter('carrier_code', array('like' => 'pointsrelais%'));
        
        foreach ($tracks as $track) {
Mage::Log('***getTrackingNumber**** 2 : '.$track->getnumber());

                $trackingNumbersToReturn[] = $track->getnumber();
 
        }
        
        return $trackingNumbersToReturn;
    }
    
    protected function getEtiquetteUrl($shipmentsIds)
    {
                Mage::Log('***getEtiquetteUrl****');
        //On récupère les infos d'expédition
        if (is_array($shipmentsIds))
        {
            foreach($shipmentsIds as $shipmentsId)
            {
                array_merge($this->_trackingNumbers, $this->getTrackingNumber($shipmentsId));
            }
            foreach($this->_trackingNumbers as $trackingId)
            {
            	
                Mage::Log('********');
                Mage::Log('$trackingId : ',$trackingId);
                Mage::Log('********');
            }
        }
        else
        {
            $shipmentId = $shipmentsIds;
            $this->_trackingNumbers = $this->getTrackingNumber($shipmentId);            
        };
        
        // On met en place les paramètres de la requète
        $params = array(
                       'Enseigne'       => $this->getConfigData('enseigne'),
                       'Expeditions'    => implode(';',$this->_trackingNumbers),
                       'Langue'    => 'FR',
        );
        //On crée le code de sécurité
        $code = implode("",$params);
        $code .= $this->getConfigData('cle');
        
        //On le rajoute aux paramètres
        $params["Security"] = strtoupper(md5($code));
        
        // On se connecte
        $client = new SoapClient("http://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL");
        
        // Et on effectue la requète
        $etiquette = $client->WSI2_GetEtiquettes($params)->WSI2_GetEtiquettesResult;
        
        return $etiquette->URL_PDF_A5;
    }
    
    protected function getEtiquetteUrlFromTrack($trackIds)
    {
                Mage::Log('***getEtiquetteUrlFromTrack****');
			$mrTrackingNumber = array();
            foreach($trackIds as $trackingId)
            {
            	
                Mage::Log('********');
                Mage::Log('$trackingId : '.$trackingId);
                Mage::Log('********');
                $trackObj = Mage::getModel('sales/order_shipment_track')->load($trackingId);
                $mrTrackingNumber[] = $trackObj->getnumber();
            }
        
	        // On met en place les paramètres de la requète
	        $params = array(
	                       'Enseigne'       => $this->getConfigData('enseigne'),
	                       'Expeditions'    => implode(';',$mrTrackingNumber),
	                       'Langue'    => 'FR',
	        );
	        
            Mage::Log('$trackingIds : '.implode(';',$mrTrackingNumber));
            
	        //On crée le code de sécurité
	        $code = implode("",$params);
	        $code .= $this->getConfigData('cle');
	        
	        //On le rajoute aux paramètres
	        $params["Security"] = strtoupper(md5($code));
	        
	        // On se connecte
	        $client = new SoapClient("http://www.mondialrelay.com/WebService/Web_Services.asmx?WSDL");
	        // Et on effectue la requète
	        $etiquette = $client->WSI2_GetEtiquettes($params)->WSI2_GetEtiquettesResult;
	        
	        Mage::Log('********2');
                Mage::Log($etiquette);
                Mage::Log('********2');
	        return $etiquette;

    }

    public function printMassAction()
    {
        $trackIds = $this->getRequest()->getPost('track_ids');
       if(!is_array($trackIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Merci de sélectionner des commandes.'));

        } else {
	        try {
	            $etiquette = $this->getEtiquetteUrlFromTrack($trackIds);
	            if($etiquette->STAT == 0){
	            $this->_processDownload('http://www.mondialrelay.fr' . $etiquette->URL_PDF_A5, 'url');
	            }else{
	            	 $this->_getSession()->addError(Mage::helper('pointsrelais')->__('Désolé, une erreure est survenu lors de la récupération de l\'étiquetes. Merci de contacter Mondial Relay ou de réessayer plus tard, erreur '.$etiquette->STAT.'.'));
	            }
	            exit(0);
	        } catch (Exception $e) {
	                Mage::Log('$Mage_Core_Exception : ',$e->getMessage());
	            $this->_getSession()->addError(Mage::helper('pointsrelais')->__('Désolé, une erreure est survenu lors de la récupération de l\'étiquetes. Merci de contacter Mondial Relay ou de réessayer plus tard.'));
	        }
        }
        return $this->_redirectReferer();
        
    }

    /*public function printAction()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        
        try {
            $urlEtiquette = $this->getEtiquetteUrl($shipmentId);
            $this->_processDownload('http://www.mondialrelay.fr' . $urlEtiquette, 'url');
//            exit(0);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('pointsrelais')->__('Désolé, une erreure est survenu lors de la récupération de l\'étiquetes. Merci de contacter Mondial Relay ou de réessayer plus tard'));
        }
        return $this->_redirectReferer();
    }*/
	
	
	public function printAction()
    {
      /*define('MAGENTO', realpath(dirname(__FILE__))); 
      require_once(MAGENTO.'/PDFMerger/PDFMerger.php');*/
     //$pdf_merge = new PDFMerger;
      
      //$pdf_merge = new PDFMerger;

      //$pdf_merge->addPDF($path.'sticker_2.pdf')->addPDF($path.'packingslip.pdf')->merge('download', 'TEST2.pdf');exit;
        //$shipmentId = $this->getRequest()->getParam('shipment_id');
        //$url  = 'http://www.mondialrelay.fr/PDF/StickerMaker2.aspx?ens=EC00096349&expedition=10216382&lg=FR&format=A5&crc=881C3AE0E528271C0770BA54C500190A';


            /*$path = $_SERVER['DOCUMENT_ROOT'].'/az_boutique_live'.Mage::getStoreConfig('sales/pdf/path_shipment').'mondiallabel/'.$shipmentId.'.pdf';

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $data = curl_exec($ch);

            curl_close($ch);

            file_put_contents($path, $data);

           // file_get_contents(Mage::getBaseDir('export').'/'.$file
            $this->_prepareDownloadResponse($shipmentId.'.pdf',file_get_contents($path));*/

            //exit;
// exit(0);

        /*$shipmentIds = $this->getRequest()->getPost('shipment_ids');

        try {


            $urlEtiquette = $this->getEtiquetteUrl($shipmentIds[0]);
$this->_processDownload('http://www.mondialrelay.fr' . $urlEtiquette, 'url');

        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('pointsrelais')->__('Désolé, une erreure est survenu lors de la récupération de l\'étiquetes. Merci de contacter Mondial Relay ou de réessayer plus tard'));
        }
        return $this->_redirectReferer();*/

        $shipmentIds = $this->getRequest()->getPost('shipment_ids');
        if (!empty($shipmentIds)) {
            $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $shipmentIds))
                ->load();
            if (!isset($pdf)){
                $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf_Mondial($shipments);
            } /*else {
                $pages = Mage::getModel('sales/order_pdf_shipment')->getPdf_Mondial($shipments);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }*/


            //return $this->_prepareDownloadResponse('packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }
    
}