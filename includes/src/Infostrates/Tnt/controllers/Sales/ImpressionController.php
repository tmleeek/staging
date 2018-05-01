<?php

class Infostrates_Tnt_Sales_ImpressionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Infostrates_Tnt');
    }


    /**
     * Shipping grid
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/tnt')
            ->_addContent($this->getLayout()->createBlock('tnt/sales_impression'))
            ->renderLayout();
    }
    
	public function getConfigData($field)
	{
        $path = 'carriers/tnt/'.$field;
        return Mage::getStoreConfig($path, Mage::app()->getStore());
	}
    
    protected function _processDownload($resource, $resourceType)
    {    	
    	$helper = Mage::helper('downloadable/download');

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
    
    protected function getEtiquetteUrl($shipmentsIds)
    {
    	//On récupère les infos d'expédition
        if (is_array($shipmentsIds))
        {
        	$path = Mage::getBaseDir('media').'/pdf_bt/';
        	$pdfDocs = array();
        	
        	for ($i = 0; $i < count($shipmentsIds); $i++)
            {
                $shipmentId = Mage::getModel('sales/order_shipment_track')->load($shipmentsIds[$i])->getParentId();
            	$orderNum = Mage::getModel('sales/order_shipment')->load($shipmentId)->getOrder()->getRealOrderId();        		

        		// Array of the pdf files need to be merged
        		$pdfDocs[] = $path.$orderNum.'.pdf';
            }
            
            $filename = $path."tnt_pdf.pdf";
            
            $cmd = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=$filename ";
			//Add each pdf file to the end of the command
			for($i=0; $i<count($pdfDocs);$i++) {
			    $cmd .= $pdfDocs[$i]." ";
			}
			$result = shell_exec($cmd);
			$filename = "tnt_pdf.pdf";
        }
        else
        {
            $shipmentId = $shipmentsIds;
            
        	$orderNum = Mage::getModel('sales/order_shipment')->load($shipmentId)->getOrder()->getRealOrderId();
        	
        	$filename = $orderNum.'.pdf';
        };
        return $filename;
    }
    
    public function printMassAction()
    {
        $path = Mage::getBaseUrl('media').'pdf_bt/';
    	$shipmentsIds = $this->getRequest()->getPost('shipment_ids');
        
        try {
            $filename = $this->getEtiquetteUrl($shipmentsIds);

            $this->_processDownload($path.$filename, 'url');
            exit(0);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('tnt')->__('Impossible de récupérer les BT : '.$filename));
        }
        return $this->_redirectReferer();
    }

    public function printAction()
    {
        $path = Mage::getBaseUrl('media').'pdf_bt/';
    	$shipmentId = $this->getRequest()->getParam('shipment_id');

        try {
        	$filename = $this->getEtiquetteUrl($shipmentId);
        	 
            $this->_processDownload($path.$filename, 'url');
            exit(0);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('tnt')->__('Impossible de récupérer le BT : '.$filename));
        }
        return $this->_redirectReferer();
    }
	
	public function printmergeAction()
    {
    
	// Array of the pdf files need to be merged

//$pdf_merge->addPDF('/data/apache/htdocs/magento/media/pdf_bt/100028902.pdf','all')->addPDF('/data/apache/htdocs/magento/media/pdf_bt/packingslip_6.pdf','all');
//$pdf_merge->merge('browser', 'packingslip.pdf');exit;

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
                $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf_TNT($shipments);
            } /*else {
                $pages = Mage::getModel('sales/order_pdf_shipment')->getPdf_Mondial($shipments);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }*/


            //return $this->_prepareDownloadResponse('packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }
    
}