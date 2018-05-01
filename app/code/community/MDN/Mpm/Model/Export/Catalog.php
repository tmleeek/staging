<?php

ini_set('memory_limit', '2G');
ini_set('max_execution_time', 0);

include_once('Bms/Compression/IncludeAll.php');

class MDN_Mpm_Model_Export_Catalog extends Mage_Core_Model_Abstract
{
    const PRODUCT_COLLECTION_LIMIT = 5000;
    const PROTECTION = 100;

    public $filepath = null;
    public $canArchive = false;

    protected $compressionEngine = null;

    protected $_categoryNames = array();
    protected $_channels = null;
    protected $_collection = null;
    protected $_attributeSetNames = null;
    protected $_websites = array();
    protected $_marketplaceIsInstalled = null;
    protected $_erpIsInstalled = null;
    protected $_whiteListedAttributes = null;

    public function generateCatalog()
    {
        $this->init();
        $this->_channels = Mage::helper('Mpm/Carl')->getChannelsSubscribed();

        $startTime = microtime(true);
        $this->parseCatalog($this->canArchive);
        Mage::helper('Mpm')->log('Catalog generated in ' . round(microtime(true) - $startTime, 3) . " sec (memory peak " . $this->humanFilesize(memory_get_peak_usage(true)) . ")");

        try {
            $startTime = microtime(true);
            $this->filepath = $this->compressionEngine->compressFile($this->filepath);
            Mage::helper('Mpm')->log('Catalog compressed in ' . round(microtime(true) - $startTime, 3) . " sec (memory peak " . $this->humanFilesize(memory_get_peak_usage(true)) . ")");
        } catch (Bms_Compression_Exceptions_UnknownCompressionType $e) {
            Mage::helper('Mpm')->log($e->getMessage());
        }

        return $this->filepath;
    }

    protected function humanFilesize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    protected function init()
    {
        $this->compressionEngine = (new Bms_Compression);

        //for now, force canArchive to false
        //$this->canArchive = $this->compressionEngine->canArchive();
        $this->canArchive = false;

        $filePathWithArchive = Mage::getBaseDir('var') . DS . 'mpm_catalog_export';
        $filePathWithoutArchive = Mage::getBaseDir('var') . DS . 'mpm_catalog_export.' . static::FILE_FORMAT;
        $this->filepath = $this->canArchive ? $filePathWithArchive : $filePathWithoutArchive;

        Mage::helper('Mpm')->log("Start catalog export (format:{" . static::FILE_FORMAT . "}, archive: {$this->canArchive}) to $this->filepath");

        $this->deleteFile($this->filepath);
        if ($this->canArchive) {
            mkdir($this->filepath, 0777, true);
        }
    }

    protected function deleteFile($filepath)
    {
        if (is_dir($filepath) && !empty($filepath) && $filepath != DIRECTORY_SEPARATOR) {
            shell_exec("rm -rf $filepath");
        }
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }

    protected function encodeToUtf8($value)
    {
        if(is_numeric($value)) {
            return $value;
        }

        return $this->isEncodedToUtf8($value) === false ? mb_convert_encoding($value, "UTF-8") : $value;
    }

    public function isEncodedToUtf8($value)
    {
        return preg_match(
            '%(?:
            [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
            |\xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
            |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
            |\xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
            |\xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
            |[\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
            |\xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
            )+%xs',
            $value
        ) === 1;
    }

    public function getProductsCollection($startProductId = 0)
    {
        $this->_collection = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('status', 1);

        $this->_collection->addAttributeToSelect('name');
        $this->_collection->addAttributeToSelect('cost');
        $this->_collection->addAttributeToSelect('manufacturer');
        $this->_collection->addAttributeToSelect('price');
        $this->_collection->addAttributeToSelect('weight');
        $this->_collection->addAttributeToSelect('special_price');
        $this->_collection->addAttributeToSelect('special_from_date');
        $this->_collection->addAttributeToSelect('special_to_date');
        $this->_collection->addAttributeToSelect('type_id');

        foreach ($this->getWhiteListedAttributes() as $attribute) {
            $this->_collection->addAttributeToSelect($attribute);
        }

        $barcode_attribute = Mage::getStoreConfig('advancedstock/barcode/barcode_attribute');
        if(!empty($barcode_attribute)) {
            $this->_collection->addAttributeToSelect($barcode_attribute);
        }

        //important : we use code "mpm/repricing/categories" cause the config renderer uses that code (see Mpm/System_Config_Button_Carl_CategoriesToExport)
        $categoryIds = $this->getCategoryIds();
        if ($categoryIds) {
            $productIds = $this->getProductIdsFromCategoryIds($categoryIds);

            $this->_collection->addFieldToFilter('entity_id', array('in' => $productIds));
        }

        $joinType = 'left';
        $export_only_with_stock = Mage::getStoreConfig('mpm/catalog_export/export_only_with_stock');
        if ($export_only_with_stock) {
            $joinType = 'inner';
        }

        if (Mage::helper('Mpm/Erp')->isInstalled()) {
            $prefix = Mage::getConfig()->getTablePrefix();
            $this->_collection->joinField('qty',
                $prefix . 'product_availability',
                'pa_available_qty',
                'pa_product_id=entity_id',
                'pa_available_qty > 0 and pa_website_id = 0',
                $joinType);
        } else {
            //else use magento inventory table
            $this->_collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1 and qty > 0',
                $joinType);
        }

        $product_types = Mage::getStoreConfig('mpm/catalog_export/product_types');
        if ($product_types) {
            $this->_collection->addFieldToFilter('type_id', array('in' => explode(',', $product_types)));
        }

        $product_visibilities = Mage::getStoreConfig('mpm/catalog_export/product_visibilities');
        if ($product_visibilities) {
            $this->_collection->addAttributeToFilter('visibility', array('in' => explode(',', $product_visibilities)));
        }

        $allowed_manufacturers = Mage::getStoreConfig('mpm/catalog_export/allowed_manufacturers');
        if ($allowed_manufacturers) {
            $this->_collection->addAttributeToFilter('manufacturer', array('in' => explode(',', $allowed_manufacturers)));
        }

        $created_at_after = Mage::getStoreConfig('mpm/catalog_export/created_at_after');
        if ($created_at_after) {
            $tmp = explode('/', $created_at_after);
            $this->_collection->addFieldToFilter('created_at', array('from' => $tmp[2] . '-' . $tmp[1] . '-' . $tmp[0]));
        }

        $created_at_before = Mage::getStoreConfig('mpm/catalog_export/created_at_before');
        if ($created_at_before) {
            $tmp = explode('/', $created_at_before);
            $this->_collection->addFieldToFilter('created_at', array('to' => $tmp[2] . '-' . $tmp[1] . '-' . $tmp[0]));
        }

        $this->_collection->getSelect()->where('e.entity_id > ' . $startProductId);
        $this->_collection->getSelect()->order('e.entity_id');
        $this->_collection->getSelect()->limit(self::PRODUCT_COLLECTION_LIMIT);

        return $this->_collection;
    }

    public function parseCollection($products)
    {
        $startProductId = null;
        foreach ($products as $product) {
            if ($product->getSku()) {
                $this->addProduct($product);
                $startProductId = $product->getId();
                $product->clearInstance();  //memory flush
            }
        }
        return $startProductId;
    }

    public function getCategoryIds()
    {
        $categoryIds = explode(',', str_replace('*,', '', Mage::getStoreConfig('mpm/repricing/categories')));
        if (Mage::getStoreConfig('mpm/repricing/categories') == '*')
            $categoryIds = null;
        if (!Mage::getStoreConfig('mpm/repricing/categories'))
            $categoryIds = null;
        return $categoryIds;
    }

    public function getProductIdsFromCategoryIds($categoryIds)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $tableName = $resource->getTableName('catalog_category_product');
        $query = "select distinct product_id from " . $tableName . " where category_id in (" . implode(',', $categoryIds) . ")";
        $productIds = $readConnection->fetchCol($query);
        return $productIds;
    }

    public function getProductIds()
    {
        $collection = $this->getProductsCollection();
        return $collection->getAllIds();
    }

    protected function getWhiteListedAttributes()
    {
        if ($this->_whiteListedAttributes === null) {
            $this->_whiteListedAttributes = explode(',', Mage::getStoreConfig('mpm/catalog_export/whitelisted_attributes'));
        }

        return $this->_whiteListedAttributes;
    }

    protected function isMarketplaceInstalled()
    {
        if ($this->_marketplaceIsInstalled === null) {
            $this->_marketplaceIsInstalled = Mage::Helper('Mpm/MarketPlace')->marketPlaceExtensionIsInstalled();
        }

        return $this->_marketplaceIsInstalled;
    }

    protected function isErpInstalled()
    {
        if ($this->_erpIsInstalled === null) {
            $this->_erpIsInstalled = Mage::getStoreConfig('advancedstock/erp/is_installed');
        }

        return $this->_erpIsInstalled;
    }

    protected function getCategoryPath($product)
    {
        $paths = array();
        $collection = $product->getCategoryCollection();

        $selectedCategory = null;
        foreach ($collection as $category) {
            if ($selectedCategory == null) {
                $selectedCategory = $category;
            } else {
                if (strlen($selectedCategory->getPath()) < strlen($category->getPath())) {
                    $selectedCategory = $category;
                }
            }
        }

        if (!$selectedCategory)
            return '';


        $path = $selectedCategory->getPath();
        $pathItems = explode('/', $path);
        foreach ($pathItems as $pathItem) {
            if ($pathItem == 1)
                continue;
            if (!isset($this->_categoryNames[$pathItem])) {
                $cat = Mage::getModel('catalog/category')->load($pathItem);
                $this->_categoryNames[$pathItem] = $cat->getName();
            }
            $paths[] = $this->_categoryNames[$pathItem];
        }

        return implode(' > ', $paths);
    }

    protected function getWebsite($websiteId)
    {
        if (!isset($this->_websites[$websiteId])) {
            $this->_websites[$websiteId] = Mage::getModel('core/website')->load($websiteId);
        }

        return $this->_websites[$websiteId];
    }

    protected function getRequiredAttributes()
    {
        return array('id', 'name', 'sku', 'category', 'price', 'cost', 'weight');
    }

    protected function addRequiredAttributes($product)
    {
        $requiredAttributes = $this->getRequiredAttributes();

        if($product->getData('manufacturer')){
            array_push($requiredAttributes, 'manufacturer');
        }

        foreach($requiredAttributes as $attribute){
            $method = 'add'.ucfirst($attribute);
            $this->$method($product);
        }

        return $requiredAttributes;
    }

    protected function getChannelReferences($product)
    {
        $references = array();
        foreach ($this->_channels as $channel) {
            $reference = Mage::helper('Mpm/MarketPlace')->getMpReference($channel->channelCode, $product);
            if ($reference) {
                $references[$channel->channelCode] = array($reference);
            }
        }
        return $references;
    }

    protected function getAttributeSetName($product)
    {
        if ($this->_attributeSetNames == null) {
            $this->_attributeSetNames = array();
            $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')->load();
            foreach ($attributeSetCollection as $id => $item) {
                $this->_attributeSetNames[$id] = $this->encodeToUtf8($item->getAttributeSetName());
            }
        }

        return $this->_attributeSetNames[$product->getattribute_set_id()];
    }

    protected function getAdditionnalAttributes($product, $requiredAttributes)
    {
        $attributes = array();
        foreach($this->getWhiteListedAttributes() as $attribute) {
            $v = $product->getData($attribute);
            if (!in_array($attribute, $requiredAttributes) && (!is_object($v)) && (!is_array($v)) && ($v !== '')) {

                //get value OR attribute text value
                $value = $v;
                $attributeValue = '';
                if ($product->getResource()->getAttribute($attribute))
                    $attributeValue = $product->getAttributeText($attribute);
                if ($attributeValue !== false && $attributeValue !== null)
                    $value = $attributeValue;

                if (in_array($attribute, array('image', 'small_image', 'base_image')) && $value) {
                        $value = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product/'.$value;
                }

                if (($value !== null) && ($value != '')) {
                    if(is_array($value)) {
                        foreach($value as $subValue) {
                            $attributes[$attribute] = $this->encodeToUtf8($subValue);
                        }

                    } else {
                        $attributes[$attribute] = $this->encodeToUtf8($value);
                    }
                }
            }
        }

        return $attributes;
    }

    protected function getBarcode($product)
    {
        $barcode_attribute = Mage::getStoreConfig('advancedstock/barcode/barcode_attribute');
        if (!$barcode_attribute) {
            return Mage::helper('AdvancedStock/Product_Barcode')->getBarcodeForProduct($product->getId());
        }

        $barcode_attribute = Mage::getStoreConfig('advancedstock/barcode/barcode_attribute');
        if(!empty($barcode_attribute)) {
            return $product->getData(Mage::getStoreConfig('advancedstock/barcode/barcode_attribute'));
        }

        return null;
    }

    protected function getErpProductAvailability($product)
    {
        $fieldsToExclude = array('pa_debug');

        $attributes = array();
        $productAvailability = Mage::helper('SalesOrderPlanning/ProductAvailabilityStatus')->getForOneProduct($product->getId());

        if(!$productAvailability || !$productAvailability->getId()) {
            return null;
        }

        foreach ($productAvailability->getData() as $key => $value) {
            if (in_array($key, $fieldsToExclude)) {
                continue;
            }
            //dont encode to Utf8 because all fields are date, int or float (except pa_debug which is excluded)
            $attributes[$key] = $value;
        }
        $attributes['message'] = $this->encodeToUtf8($productAvailability->getMessage());

        return $attributes;
    }

    protected function getErpWarehouses($product)
    {
        $warehouses = Mage::helper('AdvancedStock/Product_Base')->getStocks($product->getId());

        if(empty($warehouses)) {
            return null;
        }

        $warehouseAttributes = array();
        foreach ($warehouses as $item) {
            $warehouseAttributes['warehouse_' . $item->getstock_id()] = array(
                'stock_name' => $this->encodeToUtf8($item->getstock_name()),
                'qty' => (int)$item->getQty(),
                'available_qty' => (int)$item->getAvailableQty(),
                'warning_stock_level' => (int)$item->getWarningStockLevel(),
                'ideal_stock_level' => (int)$item->getIdealStockLevel()
            );
        }
        return $warehouseAttributes;
    }

    protected function getErpBestSupplier($product)
    {
        $bestSupplier = Mage::helper('Mpm/Erp')->getBestSupplier($product);

        if (!$bestSupplier) {
            return null;
        }

        return array(
                'id' => $bestSupplier->getsup_id(),
                'name' => $this->encodeToUtf8($bestSupplier->getsup_name()),
                'code' => $this->encodeToUtf8($bestSupplier->getsup_code()),
                'sku' => $this->encodeToUtf8($bestSupplier->getpps_reference()),
                'country' => $bestSupplier->getsup_country(),
                'buying_price' => $bestSupplier->getpps_last_price(),
                'stock' => $bestSupplier->getpps_quantity_product(),
                'is_primary' => $bestSupplier->getpps_is_default_supplier(),
            );
    }
}