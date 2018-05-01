<?php

class MDN_Mpm_Block_Products_Tabs_Competitors extends Mage_Adminhtml_Block_Widget_Grid
{

    public function getProduct()
    {
        return Mage::registry('mpm_product');
    }

    public function getChannel()
    {
        return Mage::registry('mpm_channel');
    }

    public function __construct()
    {
        parent::__construct();
        $this->setId('MpmCompetitorsGrid');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(mage::helper('Mpm')->__('No items'));
        $this->setUseAjax(true);
        $this->setDefaultSort('total');
        $this->setDefaultDir('asc');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    /**
     * Charge la collection
     *
     * @return unknown
     */
    protected function _prepareCollection()
    {
        $productOffers = new MDN_Mpm_Model_ProductOffersCollection();
        $productOffers->setProductId($this->getProduct()->getProductId());
        $productOffers->setChannel($this->getChannel());
        $productOffers->load();

        $this->setCollection($productOffers);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('seller_name', array(
            'header'=> Mage::helper('Mpm')->__('Seller'),
            'index' => 'competitor',
            'filter' => false,
            'sortable' => false
        ));

        $this->addColumn('rank', array(
            'header'=> Mage::helper('Mpm')->__('Rank'),
            'index' => 'rank',
            'align' => 'center',
            'filter' => false,
            'sortable' => false
        ));

        $this->addColumn('price', array(
            'header'=> Mage::helper('Mpm')->__('Price'),
            'index' => 'price',
            'type'  => 'number',
            'filter' => false,
            'sortable' => false,
            'type' => 'number',
            'frame_callback' => array($this, 'getCurrency'),
        ));

        $this->addColumn('shipping', array(
            'header'=> Mage::helper('Mpm')->__('Shipping'),
            'index' => 'shipping',
            'filter' => false,
            'sortable' => false,
            'type' => 'number',
            'frame_callback' => array($this, 'getCurrency'),
        ));

        $this->addColumn('total', array(
            'header'=> Mage::helper('Mpm')->__('Total'),
            'index' => 'total',
            'filter' => false,
            'sortable' => false,
            'type'  => 'number',
            'frame_callback' => array($this, 'getCurrency'),
        ));

        $this->addColumn('updated_at', array(
            'header'=> Mage::helper('Mpm')->__('Updated at'),
            'index' => 'updated_at',
            'filter' => false,
            'sortable' => false,
            'align' => 'center'
        ));

        return parent::_prepareColumns();
    }

    public function getRowClass($item)
    {
        if ($item->getIsMe())
            return 'mpm-me-row';
        if ($item->getRank() == 1)
            return 'mpm-bbw-row';
    }


    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative' => true));

        return $this->fetchView($templateName);
    }

    public function getCurrency($value, $row, $column, $isExport)
    {
        if ($isExport)
            return $value;
        if ($row->geterror() && ($column->getIndex() != 'status'))
            return;
        $id = 'cell_'.$row->getproduct_id().'_'.$row->getChannel().'_'.$column->getIndex();

        $value = Mage::helper('Mpm/Pricing')->getCurrency($row->channel).' '.$value;

        return '<span id="'.$id.'">'.$value.'</span>';
    }

}
