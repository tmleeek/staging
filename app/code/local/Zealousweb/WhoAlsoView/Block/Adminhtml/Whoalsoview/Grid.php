<?php

class Zealousweb_WhoAlsoView_Block_Adminhtml_WhoAlsoView_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        
        parent::__construct();
        $this->setId('whoalsoview_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
    }

    protected function _getCollectionClass() {

        // This is the model we are using for the grid
        //return 'foo_bar/baz_collection';
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('whoalsoview/whoalsoview')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns() {
        // Add the columns that should appear in the grid
        $this->addColumn('id', array(
            'header' => $this->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id'
                )
        );

        $this->addColumn('product_id', array(
            'header' => $this->__('Product Ids'),
            'index' => 'product_id'
                )
        );
        $this->addColumn('product_sku', array(
            'header' => $this->__('Product Skus'),
            'index' => 'product_sku'
                )
        );
        $this->addColumn('product_quantity', array(
            'header' => $this->__('Purchased Product Quantities'),
            'index' => 'product_quantity'
                )
        );

        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row) {
        // This is where our row data will link to
        //return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
