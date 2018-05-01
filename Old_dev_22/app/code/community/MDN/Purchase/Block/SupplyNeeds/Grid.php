<?php

class MDN_Purchase_Block_SupplyNeeds_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    private $_mode = null;
    private $_orderId = null;

    public function __construct() {
        parent::__construct();
        $this->setId('SupplyNeedsGrid');
        $this->_parentTemplate = $this->getTemplate();
        $this->setVarNameFilter('supply_needs');
        $this->setEmptyText(Mage::helper('customer')->__('No Items Found'));

        $this->setDefaultSort('status', 'asc');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);

        $this->setUseAjax(true);
    }

    public function setMode($mode, $orderId) {
        $this->_mode = $mode;
        $this->_orderId = $orderId;
    }

    /**
     *
     *
     * @return unknown
     */
    protected function _prepareCollection() {

        $warehouseId = Mage::helper('purchase/SupplyNeeds')->getCurrentWarehouse();
        if (!$warehouseId) {
            $collection = Mage::getModel('Purchase/SupplyNeeds')
                    ->getCollection();
        } else {
            $collection = Mage::getModel('Purchase/SupplyNeedsWarehouse')
                    ->getCollection()
                    ->addFieldToFilter('stock_id', $warehouseId);
        }
	   /*	$collection->getSelect()->joinLeft(array('d'=>new Zend_Db_Expr("SELECT ov.value as gamme_collection
						FROM `erp_view_supplyneeds_global` AS g
						INNER JOIN catalog_product_entity_int AS cv ON g.product_id = cv.entity_id
						INNER JOIN eav_attribute_option_value AS ov ON cv.value = ov.option_id
						AND cv.attribute_id =249")),array('d.gamme_collection'));
		   echo $collection->getSelect();exit;*/
		/*$collection->getSelect()->joinLeft('catalog_product_entity_int', 'catalog_product_entity_int.attribute_id=249 AND `catalog_product_entity_int`.entity_id = `main_table`.`product_id`', array('gamme_collection_new'  => new Zend_Db_Expr('`catalog_product_entity_int`.value')));*/  //echo $collection->getSelect();exit;

$collection->getSelect()->joinLeft(array('AdvancedStock/CatalogProductInt_gamme' => Mage::helper('HealthyERP')->getPrefixedTableName('catalog_product_entity_int')),
                        '`main_table`.product_id = `AdvancedStock/CatalogProductInt_gamme`.entity_id and `AdvancedStock/CatalogProductInt_gamme`.store_id = 0 and `AdvancedStock/CatalogProductInt_gamme`.attribute_id = 249',
                        array('gamme_collection_new' => 'value'));		
        $this->setCollection($collection);//echo $collection->getSelect();exit;
        return parent::_prepareCollection();
    }

    /**
     * 
     *
     * @return unknown
     */
    protected function _prepareColumns() {

        $this->addColumn('manufacturer_id', array(
            'header' => Mage::helper('purchase')->__('Manufacturer'),
            'index' => 'manufacturer_id',
            'type' => 'options',
            'options' => $this->getManufacturersAsArray(),
        ));
		
		$this->addColumn('gamme_collection_new',
            array(
                'header'=> Mage::helper('catalog')->__('Collection'),
                'index' => 'gamme_collection_new',
                'type'  => 'options',
                'options' => $this->getgammecollectionAsArray(),
				'filter_index' => '`AdvancedStock/CatalogProductInt_gamme`.value'
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('purchase')->__('Sku'),
            'index' => 'sku'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('purchase')->__('Name'),
            'index' => 'name'
        ));
		
			$manufacturer_items = Mage::getModel('eav/entity_attribute_option')->getCollection()->setStoreFilter()->join('attribute','attribute.attribute_id=main_table.attribute_id and entity_type_id = 4', 'attribute_code');

		foreach ($manufacturer_items as $manufacturer_item)
        {
            if($manufacturer_item->getAttributeCode() == 'gamme_collection_new')
            {
              $gamme_collection_options[$manufacturer_item->getOptionId()] = $manufacturer_item->getValue();
            }
       }

        
		

 
        

        mage::helper('AdvancedStock/Product_ConfigurableAttributes')->addConfigurableAttributesColumn($this, 'product_id');

        $this->addColumn('status', array(
            'header' => Mage::helper('purchase')->__('Status'),
            'index' => 'status',
            'align' => 'center',
            'type' => 'options',
            'options' => mage::getModel('Purchase/SupplyNeeds')->getStatuses(),
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/purchasing/purchase_supply_needs/display_details_column'))
        {
            $this->addColumn('sn_details', array(
                'header' => Mage::helper('purchase')->__('Details'),
                'index' => 'sn_details',
                'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_SupplyNeedsDetails',
                'align' => 'center',
                'filter' => false,
                'sortable' => false,
                'product_id_field_name' => 'product_id',
                'product_name_field_name' => 'name'
            ));
        }

        $this->addColumn('sn_needed_qty', array(
            'header' => Mage::helper('purchase')->__('Qty'),
            'index' => 'qty_min',
            'align' => 'center',
            'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_SupplyNeeds_NeededQty',
            'filter' => false
        ));

        $this->addColumn('qty_for_po', array(
            'header' => Mage::helper('purchase')->__('Qty for PO'),
            'align' => 'center',
            'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_SupplyNeeds_QtyForPo',
            'filter' => false,
            'sortable' => false,
        ));

        $this->addColumn('waiting_for_delivery_qty', array(
            'header' => Mage::helper('purchase')->__('Waiting for<br>delivery'),
            'index' => 'waiting_for_delivery_qty',
            'type' => 'number'
        ));

        $this->addColumn('sn_suppliers_name', array(
            'header' => Mage::helper('purchase')->__('Suppliers'),
            'index' => 'product_id',
            'filter' => 'Purchase/Widget_Column_Filter_SupplyNeeds_Suppliers',
            'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_SupplyNeeds_Suppliers',
            'sortable' => false
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('purchase')->__('Action'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getproduct_id',
            'actions' => array(
                array(
                    'caption' => Mage::helper('purchase')->__('View'),
                    'url' => array('base' => 'AdvancedStock/Products/Edit'),
                    'field' => 'product_id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getGridParentHtml() {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative' => true));
        return $this->fetchView($templateName);
    }

    /**
     * Url to refresh grid with ajax
     */
    public function getGridUrl() {
        return $this->getUrl('Purchase/SupplyNeeds/AjaxGrid', array('_current' => true, 'po_num' => $this->_orderId, 'mode' => $this->_mode));
    }

    /**
     * Return suppliers list as array
     *
     */
    public function getSuppliersAsArray() {
        $retour = array();

        //charge la liste des pays
        $collection = Mage::getModel('Purchase/Supplier')
                ->getCollection()
                ->setOrder('sup_name', 'asc');
        foreach ($collection as $item) {
            $retour[$item->getsup_id()] = $item->getsup_name();
        }
        return $retour;
    }
	
	
	
	public function getgammecollectionAsArray() {
        $retour = array();

        $manufacturerAttributeId = 249;

        //get manufacturers
        $product = Mage::getModel('catalog/product');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($product->getResource()->getTypeId())
                ->addFieldToFilter('main_table.attribute_id', $manufacturerAttributeId)
                ->load(false);
        $attribute = $attributes->getFirstItem()->setEntity($product->getResource());
        $manufacturers = $attribute->getSource()->getAllOptions(false);

        //ajoute au menu
        foreach ($manufacturers as $manufacturer) {
            $retour[$manufacturer['value']] = $manufacturer['label'];
        }

        return $retour;
    }

    /**
     * Return manufacturers list as array
     *
     */
    public function getManufacturersAsArray() {
        $retour = array();

        $manufacturerAttributeId = Mage::getStoreConfig('purchase/supplyneeds/manufacturer_attribute');

        //get manufacturers
        $product = Mage::getModel('catalog/product');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($product->getResource()->getTypeId())
                ->addFieldToFilter('main_table.attribute_id', $manufacturerAttributeId)
                ->load(false);
        $attribute = $attributes->getFirstItem()->setEntity($product->getResource());
        $manufacturers = $attribute->getSource()->getAllOptions(false);

        //ajoute au menu
        foreach ($manufacturers as $manufacturer) {
            $retour[$manufacturer['value']] = $manufacturer['label'];
        }

        return $retour;
    }

    /**
     * 
     */
    public function getWarehouses() {
        $collection = Mage::getModel('AdvancedStock/Warehouse')
                ->getCollection()
                ->addFieldToFilter('stock_disable_supply_needs', 0);
        return $collection;
    }

    /**
     * Return current warehouse
     * @return type 
     */
    public function getCurrentWarehouse() {
        return Mage::helper('purchase/SupplyNeeds')->getCurrentWarehouse();
    }

}
