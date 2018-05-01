<?php

/**
 * HELPER SUPPLIER STOCK IMPORT : import the supplier file
 */
class MDN_DropShipping_Helper_SupplierStockImport extends Mage_Core_Helper_Abstract {

    protected $_exception = "";
    public $_start = 0.0;
    public $_end = 0.0;
    public $_supplierId = 0;
    public $_dropShippingHelper;

    /**
     * constructor for loading the data helper 
     */
    public function MDN_DropShipping_Helper_SupplierStockImport()
    { 
        $this->_dropShippingHelper = Mage::helper('DropShipping');
    }
    
    /**
     * download the CSV file from the FTP location
     * 
     * @param type $supplier
     * @return type
     * @throws Exception 
     */
    public function downloadStockSupplierFile($supplier) {

        // load helper ftp
        $helperFtp = Mage::helper("DropShipping/Ftp");
        $helperFtp->setCredentials($supplier->getsup_ftp_host(), $supplier->getsup_ftp_port(), $supplier->getsup_ftp_login(), $supplier->getsup_ftp_password());

        // get the file name from the file path example   "/www/public_ftp/prometheus/import_file.csv" 
        if (!$supplier->getsup_ftp_file_path())
            throw new Exception('No file path set');
        $pathInfo = pathinfo($supplier->getsup_ftp_file_path());

        $filenameArray = array(0 => $pathInfo["filename"].'.'.$pathInfo["extension"]);
        $allowedExtention = Mage::getStoreConfig('dropshipping/dropship_file_import_settings/file_extensions');

        //check extension
        if (!strstr($allowedExtention, $pathInfo["extension"])) {
            throw new Exception( $this->_dropShippingHelper->__('Bad extention, only ' . $allowedExtention . ' are accepted! "' . $pathInfo["filename"] . '' . $pathInfo["extension"] . '" is deprecated.') );
        }

        // warning the target folder must be created before calling download method !!! 
        $downloadDirectory = Mage::getBaseDir() . DS . "var" . DS . "supplier_files";
        if (!is_dir($downloadDirectory)) {
            if (!mkdir($downloadDirectory, 0777, true) )
                throw new Exception("error when trying to create the directory : '" . $downloadDirectory . ".");
        }

        // param : remote ftp dir, remote file name, new download dir , new file name
        $downloadFile = $helperFtp->downloadFilesMatchingPattern($pathInfo["dirname"], $filenameArray, $downloadDirectory, 'supplier_file.'.$pathInfo['extension']);
        if (count($downloadFile) == 0) {
            throw new Exception($this->_dropShippingHelper->__('Unable to download file : %s', $supplier->getsup_ftp_file_path()));
        }
        
        return $downloadFile;
    }

    /**
     * download the attached file from the import form 
     * 
     * @return filePath
     */
    public function downloadAttachedFile($supplier, $fileName) {

        $fileInfos = pathinfo($fileName);
        $fileExtension = strtolower($fileInfos["extension"]);
        
        // download the attached file ...
        $uploader = new Varien_File_Uploader('file_path');

        $allowedExtention = strtolower(Mage::getStoreConfig('dropshipping/dropship_file_import_settings/file_extensions'));
        $allowedExtention = str_replace(' ', '', $allowedExtention);
        $allowedExtention = explode(',', $allowedExtention);

        $fileName = str_replace(' ', '_', $fileName);

        if (!in_array($fileExtension, $allowedExtention))
            throw new Exception( $this->_dropShippingHelper->__('The extension of the attached file is not allowed, check it in the configuration page of DropShipping module.') );

        $uploader->setAllowedExtensions($allowedExtention);  // array(0=>'txt', 1=>'jpeg')

        $path = Mage::getBaseDir() . DS . "var" . DS . "supplier_files". DS . $supplier->getId();

        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true) ) {
                throw new Exception($this->_dropShippingHelper->__("Error when trying to create the directory : '" . $path . "."));
            }
        }

        $fileName = 'attached_file.'.$fileExtension;
        
        $uploader->save($path, $fileName);

        //If file is uploaded :)
        if ( $uploader->getUploadedFileName() ) {
            return ($path . DS . $fileName);
        } else {
            throw new Exception($this->_dropShippingHelper->__('Unable to load the attached file.'));
        }
    }

 
    
    /**
     * Read the csv file and return an array with sku => qty + unit cost price
     *
     * @param type $supplier
     * @param type $filePath
     * @return array 
     */
    public function readSupplierFile($supplier, $path) {

        // open the local downloaded file
        $content = file_get_contents($path);
        // $delimiter = $supplier->getsup_csv_field_separator(); // " UNUSED
        $separator = $supplier->getsup_csv_field_separator(); // ;
        if (!$separator)
            throw new Exception('No separator set !');
        
        $lines = array();
        $lines = explode("\n", $content);
        // take care about the skipped first line!
        if ($supplier->getsup_csv_skip_first_line())
            unset($lines[0]);

        $stockInfos = array();
        foreach ($lines as $line) {
            if ($line == '')
                continue;
            $tFields = explode($separator, $line);
            $qty = (int) $tFields[$supplier->getsup_csv_qty_col_num() - 1];
            $sku = $tFields[$supplier->getsup_csv_sku_col_num() - 1];
            $cost = $tFields[$supplier->getsup_csv_cost_col_num() - 1];
            
            // test if the sku column is existing and if qty is a numeric
            if (!empty($sku) && is_int($qty) ) {
                $stockInfos[$sku] = array('qty' => $qty, 'cost' => $cost);
            } else {
                throw new Exception($this->_dropShippingHelper->__('Bad column sku, qty or cost, please check the column number.'));
            }
        }
        
        return $stockInfos;
    }

    /**
     * update the product stock, the supplier product and the target warehouse
     * 
     * @param type $supplier
     * @param type $path 
     */
    public function updateProductSupplierData($supplier, $path) {

        //check supplier warehouse
        if ($supplier->getsup_target_warehouse() == 1)
            throw new Exception('You can not update stock level in default warehouse, please create a new one');
        
        // read supplier file and return a table with sku & qty + unit price cost
        $tabProductsFile = $this->readSupplierFile($supplier, $path);

        // count the number of product updated or unknow and line processed
        $totalProcess = array("processedProduct" => 0, "updatedProduct" => 0, "insertedProduct" => 0, "unknowProduct" => 0, "updatedStock" => 0);

        // Get the resource model
        $resource = Mage::getSingleton('core/resource');
        $dropshippingTableName = $resource->getTableName('dropshipping_supplier_file');
        $catalogTableName = $resource->getTableName('catalog_product_entity');
        $purchaseTableName = $resource->getTableName('purchase_product_supplier');
        $stockTableName  = $resource->getTableName('cataloginventory_stock_item');

        // reset the table before fill them
        $this->resetDropShippingSupplierTable($supplier->getId());

        //insert records from array into the table for further SQL processing
        foreach ($tabProductsFile as $productSku => $productInfos) {
            $this->addSupplierFileEntry($supplier->getId(), $productSku, $productInfos['qty'], $productInfos['cost']);
            $totalProcess["processedProduct"]++;
        }

        // sql query on the products inside the table `droppshipping_supplier_file` with the associated supplier/product   
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sqlSelectProducts = '
                    SELECT 
                        `pps_product_id`, 
                        `entity_id`, 
                        `dssf_product_qty`, 
                        `pps_num`, 
                        `dssf_product_sku`, 
                        `dssf_supplier_id`,
                        `dssf_product_cost`,
                        `pps_quantity_product`,
                        `pps_last_unit_price`,
                        `pps_last_price`,
                        `pps_last_unit_price_supplier_currency`,
                        `qty` 
                    FROM ';
        if ($supplier->getsup_dropshipping_match_mode() == 'sku')
        {
            //match to products using product sku
            $sqlSelectProducts .= '`' . $dropshippingTableName . '`  AS SUPPLIER_FILE
                        LEFT JOIN `' . $catalogTableName . '` AS CATALOG_PRODUCT ON CATALOG_PRODUCT.`sku` = SUPPLIER_FILE.`dssf_product_sku`
                        LEFT JOIN `' . $purchaseTableName . '` AS SUPPLIER_PRODUCT ON CATALOG_PRODUCT.`entity_id` = SUPPLIER_PRODUCT.`pps_product_id` AND  SUPPLIER_PRODUCT.pps_supplier_num = ' . $supplier->getid() . '
                        LEFT JOIN `' . $stockTableName . '` AS STOCK ON CATALOG_PRODUCT.`entity_id` = STOCK.product_id AND stock_id = ' . $supplier->getsup_target_warehouse() . '';
        }
        else
        {
            //match to product using supplier reference
            $sqlSelectProducts .= '`' . $dropshippingTableName . '`  AS SUPPLIER_FILE
                        LEFT JOIN `' . $purchaseTableName . '` AS SUPPLIER_PRODUCT ON (dssf_product_sku = pps_reference and pps_supplier_num = ' . $supplier->getid() . ')
                        LEFT JOIN `' . $catalogTableName . '` AS CATALOG_PRODUCT ON (entity_id = pps_product_id)
                        LEFT JOIN `' . $stockTableName . '` AS STOCK ON `entity_id` = product_id AND stock_id = ' . $supplier->getsup_target_warehouse() . '';
        }
        
        $sqlSelectProducts .= ' WHERE SUPPLIER_FILE.`dssf_supplier_id` = ' . $supplier->getid() . ' ;';

        $resultTable = $read->fetchAll($sqlSelectProducts); // suppr unknow do -> AND  SUPPLIER_PRODUCT.pps_supplier_num = ' . $supplier->getid() . ' 
        // for the imported products try to create them or update them into table purchase_product_supplier 
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        foreach ($resultTable as $result) {

            // product sku is unknown
            if (empty($result["entity_id"])) {
                Mage::log("the product sku = " . $result["dssf_product_sku"] . " is UNKNOW for supplier id=" . $result["dssf_supplier_id"] . "\n", null, 'dropShipping_import_product_unknow.log');
                $totalProcess["unknowProduct"]++;
                continue;
            }

            // product is not associated to supplier
            if (empty($result["pps_num"])) {
                // create purchase product/supplier entry
                $queryInsertPurchProdSupp = "INSERT INTO " . $purchaseTableName . " (`pps_product_id`, `pps_supplier_num`, `pps_quantity_product`, `pps_last_unit_price`, `pps_last_price`, pps_last_unit_price_supplier_currency ) VALUES (" . $result["entity_id"] . "," . $supplier->getsup_id() . " , " . $result["dssf_product_qty"] . ", " . $result["dssf_product_cost"] . ", " . $result["dssf_product_cost"] . ", " . $result["dssf_product_cost"] . " );";
                $write->query($queryInsertPurchProdSupp);
                $totalProcess["insertedProduct"]++;
            } else {
                //update stock & price if has changed
                if (
                        ($result['dssf_product_qty'] != $result['pps_quantity_product'])
                        ||
                        ($result['dssf_product_cost'] != $result['pps_last_unit_price'])
                        ||
                        ($result['dssf_product_cost'] != $result['pps_last_price'])
                        ||
                        ($result['dssf_product_cost'] != $result['pps_last_unit_price_supplier_currency'])
                   )
                {
                    //update stock in product / supplier association
                    $queryUpdatePurchProdSupp = "UPDATE " . $purchaseTableName . " SET `pps_quantity_product` = " . $result["dssf_product_qty"] . ", `pps_last_unit_price` = " . $result["dssf_product_cost"] . " , `pps_last_price` = " . $result["dssf_product_cost"] . ", pps_last_unit_price_supplier_currency = " . $result["dssf_product_cost"] . " WHERE `pps_num`=" . $result["pps_num"] . ";";
                    $write->query($queryUpdatePurchProdSupp);
                    $totalProcess["updatedProduct"]++;
                }
            }

            //update stock level in warehouse
            if (((int)$result['dssf_product_qty']) != ((int)$result['qty']))
            {
                $stock = Mage::getModel('cataloginventory/stock_item')->loadByProductWarehouse($result["entity_id"], $supplier->getsup_target_warehouse());
                if (!$stock)
                    $stock = Mage::getModel('cataloginventory/stock_item')->createStock($result["entity_id"], $supplier->getsup_target_warehouse());
                if ($stock->getqty() != $result["dssf_product_qty"])
                {
                    $stock->setqty($result["dssf_product_qty"])->save();
                    $totalProcess["updatedStock"]++;
                }
            }
        } // end foreach

        return $totalProcess;
    }

    /**
     * Add logs for execution
     * @param type $supplierId
     * @param type $totalProcess 
     */
    public function addSupplierLog($supplierId, $message, $duration, $isError = false, $fileName = null, $fileContent = null) {

        $date = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp());

        $supplierlogModel = Mage::getModel('DropShipping/SupplierLog');
        $supplierlogModel->setdssl_supplier_id($supplierId); // format : 2012-10-19 15:21:34
        $supplierlogModel->setdssl_supplier_date($date);
        $supplierlogModel->setdssl_supplier_log($message);
        $supplierlogModel->setdssl_duration($duration);
        $supplierlogModel->setdssl_is_error($isError);
        $supplierlogModel->setdssl_file_name($fileName);
        $supplierlogModel->save();
        
        if ($fileContent)
            $supplierlogModel->saveFile($fileContent);

        //todo : send email if error
        if ($isError) {
            mail(Mage::getStoreConfig('dropshipping/drop_shippable_order/email_report'), "Drop shipping supplier import error", "Error when trying to import product's supplier stock ," . $message . " in " . $duration . " sec.");
            Mage::log("Error when trying to import product's supplier stock ," . $message . " in " . $duration . " sec.", null, "test_mail-addSupplierLog.log");
        }
    }

    /**
     * Called by cron to import files for all suppliers
     *
     * @param unknown_type $object
     */
    public function importAllSuppliersStocks() {

        //get all suppliers having import enabled
        $suppliers = Mage::getModel('Purchase/Supplier')->getCollection()->addFieldToFilter('sup_ftp_enabled', 1);

        foreach ($suppliers as $supplier) {
            $this->importSupplierStock($supplier);
        }
    }

    /**
     * Import stock file for 1 supplier
     * @param type $supplier 
     */
    public function importSupplierStock($supplier, $attachedFileName = NULL) {

        $path = null;
        
        try {
            $this->_start = microtime(TRUE);

            $this->_supplierId = $supplier->getId();

            if ($attachedFileName == NULL) {
                // download file
                $downloadedFiles = $this->downloadStockSupplierFile($supplier);
                $path = $downloadedFiles[0]["localpath"];
            } else {
                $path = $this->downloadAttachedFile($supplier, $attachedFileName);
            }
            
            //import file
            $totalProcess = $this->updateProductSupplierData($supplier, $path);
            
            $msg = "File processed: " . $totalProcess['processedProduct'] . " records, " . $totalProcess['updatedProduct'] . " products updated, " . $totalProcess['insertedProduct'] . " added products, ".$totalProcess['updatedStock']." stocks updated, " . $totalProcess['unknowProduct'] . " unknown sku. ";

            $this->_end = microtime(true);
            $duration = round($this->_end - $this->_start, 2);
            
            //Save logs
            $this->addSupplierLog($supplier->getId(), $msg, $duration, FALSE, basename($path), file_get_contents($path));
        } catch (Exception $ex) {
            $this->_end = microtime(true);
            $duration = round($this->_end - $this->_start, 2);

            $fileContent = (file_exists($path) ?  file_get_contents($path) : '');
            $this->addSupplierLog($this->_supplierId, $ex->getMessage(), $duration, TRUE, basename($path), $fileContent);
            Mage::logException($ex);
        }
    }

    /**
     * Delete all old logs for the giving month delay
     * @param $delay (the number of month)
     */
    public function pruneSupplierLogs($supplierId) {

        //set limit date
        $days = Mage::getStoreConfig('dropshipping/dropship_file_import_settings/prune_logs_delay');
        $dayToSeconds = $days * ( 60 * 60 * 24 ); // get month number
        $timeStamp = Mage::getModel('core/date')->timestamp() - $dayToSeconds;
        $limitDateTime = strtotime( date('Y-m-d H:i:s', $timeStamp) ); // 2012-10-31 09:45:35

        //load logs to delete
        $supplierslogModel = Mage::getModel('DropShipping/SupplierLog')->getCollection()
                ->addFieldToFilter('dssl_supplier_id', $supplierId)
                ->addFieldToFilter('dssl_supplier_date', array('lteq' => date("Y-m-d H:i:s",$limitDateTime) ));

        //parse collection and delete items
        foreach ($supplierslogModel as $log) {
                $log->delete($log->getdssl_id());
        } 

    }

    /**
     * Empty table before importing suppier file in it
     * @param type $supplierId 
     */
    public function resetDropShippingSupplierTable($supplierId) {
        // Get the table name
        $tableName = Mage::getSingleton('core/resource')->getTableName('dropshipping_supplier_file');
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "DELETE FROM `" . $tableName . "` WHERE `dssf_supplier_id`=" . $supplierId;
        $write->query($query);
    }

    /**
     * add entry in table dropshipping_supplier_file  
     * @param type $supplierId
     * @param type $sku
     * @param type $qty 
     * @param type $cost 
     */
    public function addSupplierFileEntry($supplierId, $sku, $qty, $cost) {
        $tableName = Mage::getSingleton('core/resource')->getTableName('dropshipping_supplier_file');
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $queryInsertDropShippingSupplier = 'INSERT INTO ' . $tableName . ' (`dssf_supplier_id`, `dssf_product_sku`, `dssf_product_cost`, `dssf_product_qty` ) VALUES ( ' . $supplierId . ",'" . $sku . "'," . $cost . "," . $qty . ');';
        $write->query($queryInsertDropShippingSupplier);
    }

}