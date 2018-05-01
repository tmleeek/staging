<?php
class Tatva_Freegift_Block_Adminhtml_Freegift extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
     * Set grid params
     *
     */
    protected $model;
    public function __construct()
    {
        parent::__construct();
        $this->setId('freegift_grid');
        //$this->model = Mage::getModel('freegift/freegift');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->_getProduct()->getId()) {
            $this->setDefaultFilter(array('in_products' => 1));
        }
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

    /**
     * Retirve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Add filter
     *
     * @param object $column
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Related
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        
        $productId = $this->_getProduct()->getId();
        if ($column->getId() == 'in_products') {
            $productIds = $this->getSelectedFreegiftProducts();
            
            if (empty($productIds)) {
                $productIds = 0;
            }
            
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
          
       $collection = Mage::getModel('catalog/product_link')->useAlsoBoughtLinks()
            ->getProductCollection()
            ->setProduct($this->_getProduct())
            ->addAttributeToSelect('*');
           
        if ($this->isReadonly()) {
            $productIds = $this->getFreegiftProducts();
            if (empty($productIds)) {
                $productIds = array(0);
            }
            $collection->addFieldToFilter('entity_id', array('in' => $productIds));
            //echo "<pre/>";print_r($collection->getData());
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return false;
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        if (!$this->isReadonly()) {
            $this->addColumn('in_products', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_products',
                'values'            => $this->getSelectedFreegiftProducts(),
                'align'             => 'center',
                'index'             => 'entity_id'
            ));
        }

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('catalog')->__('Type'),
            'width'     => 100,
            'index'     => 'type_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name', array(
            'header'    => Mage::helper('catalog')->__('Attrib. Set Name'),
            'width'     => 130,
            'index'     => 'attribute_set_id',
            'type'      => 'options',
            'options'   => $sets,
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('catalog')->__('Status'),
            'width'     => 90,
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        $this->addColumn('visibility', array(
            'header'    => Mage::helper('catalog')->__('Visibility'),
            'width'     => 90,
            'index'     => 'visibility',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => 80,
            'index'     => 'sku'
        ));

        $this->addColumn('price', array(
            'header'        => Mage::helper('catalog')->__('Price'),
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'         => 'price'
        ));

         $this->addColumn('position', array(
            'header'    => Mage::helper('freegift')->__('Orders number'),
            'name'      => 'position',
            'width'     => '60px',
            'type'      => 'number',
            'validate_class' => 'validate-number',
            'index'     => 'position',
            'editable'  => !$this->isReadonly(),
            'edit_only' => !$this->_getProduct()->getId()
        ));

        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/freegiftGrid', array('_current' => true));
    }

    
    protected function getFreegiftProducts()
    {
        $products = $this->getProductsFreegift();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedFreegiftProducts());
        }
        return $products;
    }



    public function getSelectedFreegiftProducts()
    {
        $products=array();
         $product=Mage::registry('current_product');
         $id=Mage::registry('current_product')->getEntityId();
         $products=$this->getProducsFreeGiftsdata($id);

        return $products;
    }


    public function getProducsFreeGiftsdata($id)
    {
       $products=array();   $links=array();
       $write = Mage::getSingleton("core/resource")->getConnection("core_write");
       $read= Mage::getSingleton('core/resource')->getConnection('core_read');
       $cvarchartable="catalog_product_link";
       $sql_check_link_id='SELECT linked_product_id  FROM `catalog_product_link` WHERE `product_id` ='.$id.' AND link_type_id =100';
       $links=$read->FetchAll($sql_check_link_id);

       foreach($links as $link_id)
       {
           $products[]= $link_id['linked_product_id'];
       }
       array_unique($products);
       return $products;
    }
}