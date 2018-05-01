<?php

class MDN_DropShipping_Model_SupplierLog extends Mage_Core_Model_Abstract {

    /**
     * 
     */
    public function _construct() {
        parent::_construct();
        $this->_init('DropShipping/SupplierLog');
    }

    /**
     * Return processed file path
     * @return string 
     */
    public function getFilePath()
    {
        $path = Mage::getBaseDir() . DS . "var" . DS . "supplier_files" . DS . $this->getdssl_supplier_id() . DS . $this->getId() . '.txt';
        return $path;
    }

    /**
     * Before delete process
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _beforeDelete() {

        parent::_beforeDelete();
        
        try {
            $directoryPath = $this->getFilePath();
            if (file_exists($directoryPath))
                unlink ($directoryPath);
            
        } catch (Exception $err) {
            Mage::Helper('DropShipping')->__("Error when trying to delete imported files. %s", $err->getMessage());
        }
    }
    
    /**
     *
     * @param type $fileContent 
     */
    public function saveFile($fileContent)
    {
        file_put_contents($this->getFilePath(), $fileContent);
    }

}