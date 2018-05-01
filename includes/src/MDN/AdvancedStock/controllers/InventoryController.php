<?php

class MDN_AdvancedStock_InventoryController extends Mage_Adminhtml_Controller_Action {

    /**
     * Display inventories
     *
     */
    public function GridAction() {
        $this->loadLayout();
        
        $this->_setActiveMenu('erp');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Stock take'));

        $this->renderLayout();
    }

    /**
     * Edit / create inventory
     */
    public function EditAction() {

        //load current inventory
        $inventoryId = $this->getRequest()->getParam('ei_id');
        $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);
        Mage::register('current_inventory', $inventory);

        $this->loadLayout();

        $this->_setActiveMenu('erp');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Stock take').' #'.$inventoryId);
        
        $this->renderLayout();
    }
    
    /**
     * Save inventory 
     */
    public function SaveAction()
    {
        //load
        $inventoryId = $this->getRequest()->getPost('ei_id');
        $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);

        //save data
        foreach ($this->getRequest()->getPost() as $key => $value) {
            if ($key != 'ei_id')
                $inventory->setData($key, $value);
        }
        $inventory->save();
        
        //apply (if required)
        if ($this->getRequest()->getPost('apply_inventory') == 1)
        {
            $smLabel = $this->getRequest()->getPost('apply_stock_movement_label');
            $simulation = $this->getRequest()->getPost('apply_simulation');
            $onlyForScannedLocation = $this->getRequest()->getPost('apply_only_for_scanned_location');
            $debug = $inventory->apply($smLabel, $simulation, $onlyForScannedLocation);
            if ($simulation)
                die($debug);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('AdvancedStock')->__('Inventory has been applied, stock levels have been changed'));
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('AdvancedStock')->__('Inventory saved'));
        $this->getResponse()->setRedirect($this->getUrl('*/*/Edit', array('ei_id' => $inventory->getId())));
    }
    
    /**
     * Update stock picture 
     */
    public function UpdateStockPictureAction()
    {
        $inventoryId = $this->getRequest()->getParam('ei_id');
        $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);
        
        $inventory->updateStockPicture();
        
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('AdvancedStock')->__('Stock picture updated'));
        $this->getResponse()->setRedirect($this->getUrl('*/*/Edit', array('ei_id' => $inventory->getId())));
    }

    /**
     * Scan products 
     */
    public function ScanAction() {

        //load current inventory
        $inventoryId = $this->getRequest()->getParam('ei_id');
        $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);
        Mage::register('current_inventory', $inventory);

        $this->loadLayout();

        $this->_setActiveMenu('erp');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Stock take').' #'.$inventoryId);
        
        $this->renderLayout();
    }
    
    /**
     * Return location information 
     */
    public function LocationInformationAction() {
        $inventoryId = $this->getRequest()->getParam('ei_id');
        $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);
        Mage::register('current_inventory', $inventory);

        $location = $this->getRequest()->getParam('location');

        //return array
        $response = array();
        $response['error'] = false;
        $response['message'] = '';

        try {
            //check if the location is already scanned for this inventory
            if ($inventory->locationAlreadyScanned($location))
                throw new Exception('error_location_scanned');

            //set location's products
            $block = $this->getLayout()->createBlock('AdvancedStock/Inventory_Scan_Products');
            $block->setTemplate('AdvancedStock/Inventory/Scan/Products.phtml');
            $block->setLocation($location);
            $response['products_html'] = $block->toHtml();

            //set location information
            $block = $this->getLayout()->createBlock('AdvancedStock/Inventory_Scan_Location');
            $block->setTemplate('AdvancedStock/Inventory/Scan/Location.phtml');
            $block->setLocation($location);
            $response['location_html'] = $block->toHtml();

            //set products json
            $response['products'] = $this->getProductsForLocation($inventory, $location);
        } catch (Exception $ex) {
            $response['error'] = true;
            $response['message'] = $ex->getMessage();
        }


        //return response
        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
    }
    
    /**
     * Return products informaiton
     * @param type $inventory
     * @param type $location Re
     */
    protected function getProductsForLocation($inventory, $location) {
        $collection = $inventory->getExpectedProducts($location);
        $retour = array();

        foreach ($collection as $item) {
            $t = array();
            $t['product_id'] = $item->getId();
            $t['barcode'] = Mage::helper('AdvancedStock/Product_Barcode')->getBarcodeForProduct($item->getId());   
            $t['expected_qty'] = (int) $item->geteisp_stock();
            $t['scanned_qty'] = 0;
            $t['name'] = $item->getname();

            $retour[] = $t;
        }

        return $retour;
    }

    /**
     *  
     */
    public function UnknownBarcodeAction()
    {
        $barcode = $this->getRequest()->getParam('barcode');
        $inventoryId = $this->getRequest()->getParam('ei_id');
        
        $response = array(
            'error' => false,
            'mode' => '',
            'message' => '');
        
        try
        {
        
            //try to load product
            $product = mage::helper('AdvancedStock/Product_Barcode')->getProductFromBarcode($barcode);
            if (!$product)
                throw new Exception('Unable to find product with barcode '.$barcode);
            
            //find the product location
            $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);

            //return product information
            $response['mode'] = 'add';
            $response['product']['product_id'] = $product->getId();
            $response['product']['barcode'] = $barcode;
            $response['product']['expected_qty'] = $inventory->getExpectedQuantityForProduct($product->getId());
            $response['product']['scanned_qty'] = 0;
            $response['product']['name'] = $product->getname();
            $response['product']['sku'] = $product->getsku();
            
        }
        catch(Exception $ex)
        {
            $response['mode'] = 'error';
            $response['message'] = $ex->getMessage();
        }
        
        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
        

    }

    /**
     * Save scanned products 
     */
    public function SaveScanAction() {
        
        //load datas
        $inventoryId = $this->getRequest()->getPost('ei_id');
        $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);
        $location = $this->getRequest()->getPost('eip_location');
        $productDatas = $this->getRequest()->getPost('product_datas');
        $productDatas = $this->convertProductDatas($productDatas);

        //save scanned products
        foreach ($productDatas as $productId => $qty) {
            $inventory->addScannedProduct($location, $productId, $qty);
        }

        //confirm & return
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('AdvancedStock')->__('Scanned products saved'));
        $this->getResponse()->setRedirect($this->getUrl('*/*/Scan', array('ei_id' => $inventory->getId())));
    }
    
    /**
     * Convert product datas to array
     * @param type $productData
     * @return type 
     */
    protected function convertProductDatas($productData) {
        $retour = array();

        $t = explode(';', $productData);
        foreach ($t as $item) {
            $tItem = explode('=', $item);
            if (count($tItem) == 2) {
                    $retour[$tItem[0]] = $tItem[1];
            }
        }

        return $retour;
    }


    /**
     * Reset location for inventory 
     */
    public function ResetLocationAction() {
        
        $inventoryId = $this->getRequest()->getParam('ei_id');
        $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);
        Mage::register('current_inventory', $inventory);

        $location = $this->getRequest()->getParam('location');
        $inventory->resetLocation($location);
        
    }
    
    /**
     * Return scanned products grid in ajax
     */
    public function AjaxScannedProductsAction() {
        $inventoryId = $this->getRequest()->getParam('ei_id');
        $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);
        Mage::register('current_inventory', $inventory);

        $block = $this->getLayout()->createBlock('AdvancedStock/Inventory_Edit_Tabs_ScannedProducts');
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Return missed locations grid in ajax
     */
    public function AjaxMissedLocationsAction() {
        $inventoryId = $this->getRequest()->getParam('ei_id');
        $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);
        Mage::register('current_inventory', $inventory);

        $block = $this->getLayout()->createBlock('AdvancedStock/Inventory_Edit_Tabs_MissedLocations');
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Return missed locations grid in ajax
     */
    public function AjaxDifferencesAction() {
        $inventoryId = $this->getRequest()->getParam('ei_id');
        $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);
        Mage::register('current_inventory', $inventory);

        $block = $this->getLayout()->createBlock('AdvancedStock/Inventory_Edit_Tabs_Differences');
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * 
     */
    public function AjaxStockPictureAction() {
        $inventoryId = $this->getRequest()->getParam('ei_id');
        $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);
        Mage::register('current_inventory', $inventory);

        $block = $this->getLayout()->createBlock('AdvancedStock/Inventory_Edit_Tabs_StockPicture');
        $this->getResponse()->setBody($block->toHtml());
    }
    
    /**
     * 
     */
    public function exportCsvScannedProductsAction() {

        $inventoryId = $this->getRequest()->getParam('ei_id');
        $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);
        Mage::register('current_inventory', $inventory);

        $fileName = 'inventory_scanned_products.csv';
        $content = $this->getLayout()->createBlock('AdvancedStock/Inventory_Edit_Tabs_ScannedProducts')
                ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    /**
     * 
     */
    public function exportCsvStockPictureAction() {

        $inventoryId = $this->getRequest()->getParam('ei_id');
        $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);
        Mage::register('current_inventory', $inventory);

        $fileName = 'inventory_stock_picture.csv';
        $content = $this->getLayout()->createBlock('AdvancedStock/Inventory_Edit_Tabs_StockPicture')
                ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }    
    
    /**
     * 
     */
    public function exportCsvDifferencesAction() {

        $inventoryId = $this->getRequest()->getParam('ei_id');
        $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);
        Mage::register('current_inventory', $inventory);

        $fileName = 'inventory_differences.csv';
        $content = $this->getLayout()->createBlock('AdvancedStock/Inventory_Edit_Tabs_Differences')
                ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }    
        
    /**
     * 
     */
    public function exportCsvMissedLocationsAction() {

        $inventoryId = $this->getRequest()->getParam('ei_id');
        $inventory = Mage::getModel('AdvancedStock/Inventory')->load($inventoryId);
        Mage::register('current_inventory', $inventory);

        $fileName = 'inventory_missed_locations.csv';
        $content = $this->getLayout()->createBlock('AdvancedStock/Inventory_Edit_Tabs_MissedLocations')
                ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }    
}
