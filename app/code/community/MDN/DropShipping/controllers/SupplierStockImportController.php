<?php

class MDN_DropShipping_SupplierStockImportController extends Mage_Adminhtml_Controller_Action {

    /**
     * send connection information, then try to download csv file 
     */
    public function ImportAction() {

        $supplierId = $this->getRequest()->getParam("sup_id");
        
        try {
            $supplier = Mage::getModel("Purchase/Supplier")->load($supplierId);
            $fileName = "";
            if (isset($_FILES['file_path']))
                $fileName = $_FILES['file_path']['name'];
            
            // import file
            Mage::helper("DropShipping/SupplierStockImport")->importSupplierStock($supplier, $fileName);

            // check for clearing logs
            Mage::helper("DropShipping/SupplierStockImport")->pruneSupplierLogs($supplierId);
            
            // display infos success
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('DropShipping')->__("Import Processed, please check logs to see the result"));
            $this->_redirect('Purchase/Suppliers/Edit', array('sup_id' => $supplierId, 'tab' => 'tab_log'));
            
        } catch (Exception $error) {
            Mage::getSingleton('adminhtml/session')->addError($error->getMessage());
            $this->_redirect('Purchase/Suppliers/Edit', array('sup_id' => $supplierId, 'tab' => 'tab_log'));
        }
        
    }


    /**
     * 
     */
    public function FtpImportAction() {
        
        $supplierId = $this->getRequest()->getParam("sup_id");
        $supplier = Mage::getModel("Purchase/Supplier")->load($supplierId);
        
        try {
            
            Mage::helper('DropShipping/SupplierStockImport')->importSupplierStock($supplier);
            
            // display infos success
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('DropShipping')->__("Import Processed, please check logs to see the result"));
            $this->_redirect('Purchase/Suppliers/Edit', array('sup_id' => $supplierId, 'tab' => 'tab_log'));
            
        } catch (Exception $error) {
            Mage::getSingleton('adminhtml/session')->addError($error->getMessage());
            $this->_redirect('Purchase/Suppliers/Edit', array('sup_id' => $supplierId, 'tab' => 'tab_log'));
        }
    }
    
    /**
     * action to download the imported file from log grid 
     */
    public function DownloadImportedFileAction() {

        $logId = $this->getRequest()->getParam("dssl_id");

        try {
            // call download method
            $log = Mage::getModel('DropShipping/SupplierLog')->load($logId);
            $this->_prepareDownloadResponseV2($log->getdssl_file_name(), file_get_contents($log->getFilePath()), 'text/csv');
        } catch (Exception $error) {
            Mage::getSingleton('adminhtml/session')->addError($error->getMessage());
            $this->_redirect('Purchase/Suppliers/Edit', array('sup_id' => $supplierId));
        }
    }

    /**
     * Custom download response method for magento multi version compatibility
     */
    protected function _prepareDownloadResponseV2($fileName, $content, $contentType) {
        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', strlen($content))
                ->setHeader('Content-Disposition', 'attachment; filename=' . $fileName)
                ->setBody($content);
    }

}