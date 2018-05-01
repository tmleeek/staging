<?php

class MDN_Mpm_Helper_Export extends Mage_Core_Helper_Abstract {

    public function ExportCatalog()
    {
        $filePath = $this->generateCatalog();

        $startTime = microtime(true);
        Mage::helper('Mpm/Carl')->uploadCatalog($filePath);
        Mage::helper('Mpm')->log('Catalog uploaded in '.round(microtime(true) - $startTime, 3)." sec");
    }

    public function getCatalog()
    {
        $filePath = $this->generateCatalog();
        return $filePath;

    }

    public function generateCatalog()
    {
        $fileFormat = Mage::getStoreConfig('mpm/catalog_export/export_file_format');
        if(empty($fileFormat)) {
            $fileFormat = MDN_Mpm_Model_System_Config_CatalogExportFormat::FORMAT_XML;
        }

        $filepath = Mage::getSingleton('Mpm/Export_Catalog_'.ucfirst($fileFormat))->generateCatalog();
        return $filepath;
    }

}
