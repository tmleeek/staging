<?php

class MDN_AdvancedStock_Model_Inventory extends Mage_Core_Model_Abstract {

    private $_warehouse = null;
    private $_stockPicture = null;

    //status constants

    const kStatusOpened = 'opened';
    const kStatusClosed = 'closed';

    /**
     * Constructor
     */
    public function _construct() {
        parent::_construct();
        $this->_init('AdvancedStock/Inventory');
    }

    /**
     * Return statuses as array
     * @return type 
     */
    public function getStatuses() {
        $options = array();

        $options[self::kStatusOpened] = mage::helper('AdvancedStock')->__(self::kStatusOpened);
        $options[self::kStatusClosed] = mage::helper('AdvancedStock')->__(self::kStatusClosed);

        return $options;
    }

    /**
     * 
     */
    protected function _beforeSave() {
        parent::_beforeSave();

        if ($this->getei_date() == '')
            $this->setei_date(date('Y-m-d'));
    }

    /**
     * Return warehouse
     */
    public function getWarehouse() {
        if ($this->_warehouse == null) {
            $this->_warehouse = Mage::getModel('AdvancedStock/Warehouse')->load($this->getei_warehouse_id());
        }
        return $this->_warehouse;
    }

    /**
     * Return the collection of products (based on stock picture)
     */
    public function getExpectedProducts($location) {

        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('name')
                ->joinField('eisp_stock', 'AdvancedStock/Inventory_StockPicture', 'eisp_stock', 'eisp_product_id=entity_id', "eisp_inventory_id=" . $this->getId() . " and eisp_shelf_location = '" . $location . "' ", 'inner');

        return $collection;
    }
    
    /**
     * Return expected quantity for one product (based on stock picture)
     * @param type $productId 
     */
    public function getExpectedQuantityForProduct($productId)
    {
        $item = Mage::getModel('AdvancedStock/Inventory_StockPicture')
                ->getCollection()
                ->addFieldToFilter('eisp_product_id', $productId)
                ->addFieldToFilter('eisp_inventory_id', $this->getId())
                ->getFirstItem();
                
        return $item->geteisp_stock();
    }

    /**
     * Return scanned products 
     */
    public function getScannedProducts() {
        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('name');

        $collection->joinTable('AdvancedStock/Inventory_Product', 'eip_product_id=entity_id', array('scanned_qty' => 'eip_qty', 'shelf_location' => 'eip_shelf_location'), 'eip_inventory_id=' . $this->getId(), 'inner');
        $collection->joinTable('AdvancedStock/Inventory_StockPicture', 'eisp_product_id=entity_id', array('expected_qty' => 'eisp_stock'), 'eisp_inventory_id=' . $this->getId(), 'left');
        
        return $collection;
    }

    /**
     * Return differences between what has been scanned and what is expected 
     */
    public function getDifferences($onlyForScannedLocation = false) {
        
        //get all product ids (from stock picture & from scanned products
        $prefix = Mage::getConfig()->getTablePrefix();
        $sql = 'select distinct eip_product_id from '.$prefix.'erp_inventory_product where eip_inventory_id = '.$this->getId();
        if ($onlyForScannedLocation == false)
        {
            $sql .= ' UNION ';
            $sql .= 'select distinct eisp_product_id from '.$prefix.'erp_inventory_stock_picture where eisp_inventory_id = '.$this->getId();
        }
        $allProductIds = mage::getResourceModel('AdvancedStock/Inventory_Collection')->getConnection()->fetchCol($sql);
        
        //get product in picture and scanned with the same quantity
        $sql = "select eip_product_id from ".$prefix."erp_inventory_stock_picture inner join ".$prefix."erp_inventory_product on (eisp_product_id = eip_product_id and eip_inventory_id = eisp_inventory_id) where eip_qty = eisp_stock and eip_inventory_id = ".$this->getId();
        $productsInPictureAndScannedWithSameQuantity = mage::getResourceModel('AdvancedStock/Inventory_Collection')->getConnection()->fetchCol($sql);

        //find products with differences
        $productIds = array_diff($allProductIds, $productsInPictureAndScannedWithSameQuantity);
        
        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('name')
                ->addFieldToFilter('entity_id', array('in' => $productIds));

        $collection->joinTable('AdvancedStock/Inventory_Product', 
                                'eip_product_id=entity_id', 
                                array('eip_qty' => 'eip_qty', 'eip_shelf_location' => 'eip_shelf_location'), 
                                'eip_inventory_id=' . $this->getId(), 
                                'left');
        
        $collection->joinTable('AdvancedStock/Inventory_StockPicture', 
                                'eisp_product_id=entity_id', 
                                array('eisp_stock' => 'eisp_stock', 'eisp_shelf_location' => 'eisp_shelf_location'), 
                                'eisp_inventory_id=' . $this->getId(), 
                                'left');
        
        return $collection;
    }
    
    /**
     * return locations in stock picture that has not been scanned 
     */
    public function getMissedLocations()
    {
        $collection = Mage::getModel('AdvancedStock/Inventory_MissedLocation')
                            ->getCollection()
                            ->addFieldToFilter('eisp_inventory_id', $this->getId());
        return $collection;
    }

    /**
     * Add a scanned product
     * @param type $location
     * @param type $productId
     * @param type $qty 
     */
    public function addScannedProduct($location, $productId, $qty) {
        //try to find if a record already exists for this location / product
        $item = $this->getItem($location, $productId);
        if (!$item) {
            $item = Mage::getModel('AdvancedStock/Inventory_Product');
            $item->seteip_inventory_id($this->getId());
            $item->seteip_product_id($productId);
            $item->seteip_shelf_location($location);
        }

        $item->seteip_qty($item->geteip_qty() + $qty);

        $item->save();
    }

    /**
     *
     * @param type $location
     * @param type $productId 
     */
    public function getItem($location, $productId) {
        $item = Mage::getModel('AdvancedStock/Inventory_Product')
                ->getCollection()
                ->addFieldToFilter('eip_inventory_id', $this->getId())
                ->addFieldToFilter('eip_product_id', $productId)
                ->getFirstItem();
        if (!$item->getId())
            return null;
        else
            return $item;
    }

    /**
     * Return true if a location as already been scanned
     * 
     * @param type $location
     * @return boolean 
     */
    public function locationAlreadyScanned($location) {
        $collection = Mage::getModel('AdvancedStock/Inventory_Product')
                ->getCollection()
                ->addFieldToFilter('eip_inventory_id', $this->getId())
                ->addFieldToFilter('eip_shelf_location', $location);
        if ($collection->getSize() > 0)
            return true;
        else
            return false;
    }

    /**
     * Reset location
     * @param type $location 
     */
    public function resetLocation($location) {
        $collection = Mage::getModel('AdvancedStock/Inventory_Product')
                ->getCollection()
                ->addFieldToFilter('eip_inventory_id', $this->getId())
                ->addFieldToFilter('eip_shelf_location', $location);
        foreach ($collection as $item) {
            $item->delete();
        }
    }

    /**
     * Apply an inventory
     * @param type $stockMovementLabel 
     */
    public function apply($stockMovementLabel, $simulation = false, $onlyForScannedLocation = false) {
        
        $result = '';
        $count = 0;

        $differences = $this->getDifferences($onlyForScannedLocation);
        foreach ($differences as $difference) {
            $productId = $difference->getentity_id();
            $qtyScanned = (int)$difference->geteip_qty();
            $qtyInPicture = (int)$difference->geteisp_stock();

            //calculate stock level at inventory date
            if ($qtyScanned != $qtyInPicture) {
                $result .= $this->applyForProduct($productId, $difference->getName(), $qtyInPicture, $qtyScanned, $stockMovementLabel, $simulation);
                $count++;
            }
        }
        
        //change inventory status
        if (!$simulation)
            $this->setei_status(self::kStatusClosed)->save();

        $result = '<p>Number of stock movement : ' . $count . '</p><p>Partial inventory : '.($onlyForScannedLocation ? ' yes ' : ' no ').'</p>' . $result;
        return $result;
    }

    /**
     * Apply inventory for one product
     * @param type $productId
     * @param type $qtyInStock
     * @param type $qtyScanned 
     */
    public function applyForProduct($productId, $productName, $qtyInStock, $qtyScanned, $stockMovementLabel, $simulation) {

        $stockMovementQty = $qtyInStock - $qtyScanned;
        $targetWarehouseId = null;
        $sourceWarehouseId = null;

        //we need to make a outgoing movement (reduce the stock)
        $fromWarehouseName = Mage::helper('AdvancedStock')->__('nowhere');
        $toWarehouseName = Mage::helper('AdvancedStock')->__('nowhere');
        if ($stockMovementQty > 0) {
            $sourceWarehouseId = $this->getWarehouse()->getId();
            $fromWarehouseName = $this->getWarehouse()->getstock_name();
        } else {
            //we need to make a incoming movement (increase the stock)
            $targetWarehouseId = $this->getWarehouse()->getId();
            $stockMovementQty = -$stockMovementQty;
            $toWarehouseName = $this->getWarehouse()->getstock_name();
        }

        if (!$simulation) {
            //Create stock movement
            $obj = mage::getModel('AdvancedStock/StockMovement')
                    ->setsm_product_id($productId)
                    ->setsm_qty($stockMovementQty)
                    ->setsm_description($stockMovementLabel)
                    ->setsm_date(date('Y-m-d'))
                    ->setsm_type('adjustment')
                    ->setsm_source_stock($sourceWarehouseId)
                    ->setsm_target_stock($targetWarehouseId);
            $obj->save();
        }

        //return debug
        $debug = '<p>==================================================================</br>';
        $debug .= $productName . ', Product Id = ' . $productId . ', Qty in picture = ' . $qtyInStock . ', Qty scanned = ' . $qtyScanned . '<br>';
        $debug .= 'Create stock movement from ' . $fromWarehouseName . ' to ' . $toWarehouseName . ' with qty = ' . $stockMovementQty;

        return $debug;
    }

    /**
     * Return stock picture
     * @return type 
     */
    public function getStockPicture()
    {
        if ($this->_stockPicture == null)
        {
            $this->_stockPicture = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->addAttributeToSelect('name')
                    ->joinTable('AdvancedStock/Inventory_StockPicture', 
                                'eisp_product_id=entity_id', 
                                array('eisp_stock' => 'eisp_stock', 'eisp_shelf_location' => 'eisp_shelf_location'), 
                                'eisp_inventory_id=' . $this->getId(), 
                                'inner');

        }
        return $this->_stockPicture;
    }
    
    /**
     *Updae stock picture 
     */
    public function updateStockPicture()
    {
        //erase previous records
        $prefix = Mage::getConfig()->getTablePrefix();
        $sql = 'delete from '.$prefix.'erp_inventory_stock_picture where eisp_inventory_id = '.$this->getId();
        mage::getResourceModel('AdvancedStock/Inventory_Collection')->getConnection()->query($sql);
        
        //update
        $sql = 'insert into '.$prefix.'erp_inventory_stock_picture (eisp_inventory_id, eisp_product_id, eisp_stock, eisp_shelf_location) select '.$this->getId().', product_id, qty, shelf_location from '.$prefix.'cataloginventory_stock_item where stock_id = '.$this->getei_warehouse_id();
        mage::getResourceModel('AdvancedStock/Inventory_Collection')->getConnection()->query($sql);
        
        //store stock picture date
        $this->setei_stock_picture_date(date('Y-m-d'))->save();
        
    }

}
