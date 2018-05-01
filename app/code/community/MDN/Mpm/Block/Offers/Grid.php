<?php

class MDN_Mpm_Block_Offers_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('MpmOffers');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(Mage::helper('Mpm')->__('No Items Found'));
        $this->setDefaultSort('id', 'desc');
    }


    protected function _prepareCollection() {

        $collection = Mage::getModel('Mpm/Product_Offer')
            ->getCollection()
            ->join('catalog/product', 'main_table.product_id=entity_id');

        $productAttributes = array('name', 'status', 'cost');
        if (Mage::helper('Mpm')->getSupplierAttribute())
            $productAttributes[] = Mage::helper('Mpm')->getSupplierAttribute();
        if (Mage::helper('Mpm')->getBrandAttribute())
            $productAttributes[] = Mage::helper('Mpm')->getBrandAttribute();
        foreach ($productAttributes as $attributeCode) {
            $alias     = $attributeCode . '_table';
            $attribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);

            /** Adding eav attribute value */
            $collection->getSelect()->joinLeft(
                array($alias => $attribute->getBackendTable()),
                "main_table.product_id = $alias.entity_id AND $alias.attribute_id={$attribute->getId()} and $alias.store_id = 0",
                array($attributeCode.'_attribute' => 'value')
            );
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns() {

        $this->addColumn('sku', array(
            'header' => Mage::helper('Mpm')->__('Sku'),
            'index' => 'sku',
        ));

        $this->addColumn('name_attribute', array(
            'header' => Mage::helper('Mpm')->__('Product'),
            'index' => 'name_attribute',
            'filter_index' => '`name_table`.`value`'
        ));

        $this->addColumn('category', array(
            'header' => Mage::helper('Mpm')->__('Category'),
            'index' => 'main_table.product_id',
            'filter_index' => 'main_table.product_id',
            'filter' => 'MDN_Mpm_Block_Widget_Grid_Column_Filter_Product_Category',
            'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_Category',
            'type'  => 'options',
            'options' => Mage::getSingleton('Mpm/System_Config_Category')->getAllOptions(),
        ));

        if (Mage::helper('Mpm')->getSupplierAttribute())
        {
            $this->addColumn('supplier_attribute', array(
                'header' => Mage::helper('Mpm')->__('Supplier'),
                'index' => Mage::helper('Mpm')->getSupplierAttribute().'_attribute',
                'type' => 'options',
                'options' => $this->getOptions(Mage::helper('Mpm')->getSupplierAttribute()),
                'filter_index' => '`'.Mage::helper('Mpm')->getSupplierAttribute().'_table`.`value`'
            ));
        }

        if (Mage::helper('Mpm')->getBrandAttribute())
        {
            $this->addColumn('brand', array(
                'header' => Mage::helper('Mpm')->__('Brand'),
                'index' => Mage::helper('Mpm')->getBrandAttribute().'_attribute',
                'type' => 'options',
                'options' => $this->getOptions(Mage::helper('Mpm')->getBrandAttribute()),
                'filter_index' => '`'.Mage::helper('Mpm')->getBrandAttribute().'_table`.`value`'
            ));
        }

        $this->addColumn('channel', array(
            'header' => Mage::helper('Mpm')->__('Channel'),
            'index' => 'channel',
            'type' => 'options',
            'options' => Mage::getSingleton('Mpm/System_Config_Channels')->getAllOptions(),
            'align' => 'center',
        ));

        $this->addColumn('seller_name', array(
            'header' => Mage::helper('Mpm')->__('Seller'),
            'index' => 'seller_name',
            'align' => 'center',
        ));

        $this->addColumn('price', array(
            'header' => Mage::helper('Mpm')->__('Price'),
            'index' => 'price',
            'type' => 'price',
            'currency_code' => Mage::getStoreConfig('currency/options/base'),
        ));

        $this->addColumn('shipping', array(
            'header' => Mage::helper('Mpm')->__('Shipping'),
            'index' => 'shipping',
            'type' => 'price',
            'currency_code' => Mage::getStoreConfig('currency/options/base'),
        ));

        $this->addColumn('total', array(
            'header' => Mage::helper('Mpm')->__('Total'),
            'index' => 'total',
            'type' => 'price',
            'currency_code' => Mage::getStoreConfig('currency/options/base'),
        ));

        $this->addColumn('rank', array(
            'header' => Mage::helper('Mpm')->__('Rank'),
            'index' => 'rank',
            'type' => 'number',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getProductId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'adminhtml/catalog_product/edit'
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
            ));

        $this->addExportType('*/*/exportOffersGridCsv', Mage::helper('Mpm')->__('CSV'));
        $this->addExportType('*/*/exportOffersGridExcel', Mage::helper('Mpm')->__('XML Excel'));

        return parent::_prepareColumns();
    }

    public function getGridParentHtml() {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative' => true));
        return $this->fetchView($templateName);
    }

    protected function getOptions($attributeCode)
    {
        $product = Mage::getModel('catalog/product');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($product->getResource()->getTypeId())
            ->addFieldToFilter('main_table.attribute_code', $attributeCode)
            ->load(false);
        $attribute = $attributes->getFirstItem()->setEntity($product->getResource());
        $manufacturers = $attribute->getSource()->getAllOptions(false);
        $retour = array();
        foreach ($manufacturers as $manufacturer) {
            $retour[$manufacturer['value']] = $manufacturer['label'];
        }

        return $retour;
    }

}
