<?php

class MDN_Mpm_Block_Adminhtml_Catalog_Product_Edit_Tab_OfferGrid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected $channelToFilter = false;
    protected $filters = array();
    protected $sort = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('MpmAllOffersGrid');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(mage::helper('Mpm')->__('No items'));
        $this->setUseAjax(true);
        $this->setDefaultSort('total');
        $this->setDefaultDir('ASC');
    }

    public function getProduct()
    {
        return Mage::registry('product');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::helper('Mpm/Product')->getOffers($this->getProduct(), false, $this->channelToFilter,
        $this->filters, $this->sort);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $this->addColumn('channel', array(
                'header'=> Mage::helper('Mpm')->__('Channel'),
                'index' => 'channel',
                'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Channel',
                'align' => 'center',
                'type' => 'options',
                'options' => Mage::helper('Mpm/Carl')->getChannelsSubscribed(true)
            ));

        $this->addColumn('competitor', array(
                'header'=> Mage::helper('Mpm')->__('Seller'),
                'index' => 'competitor'
            ));

        $this->addColumn('rank', array(
                'header'=> Mage::helper('Mpm')->__('Rank'),
                'index' => 'rank',
                'align' => 'center'
            ));

        $this->addColumn('price', array(
                'header'=> Mage::helper('Mpm')->__('Price'),
                'index' => 'price',
                'type'  => 'number',
                'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Catalog_Product_Edit_Price',
                'filter' => false,
                'align' => "right"
            ));

        $this->addColumn('shipping', array(
                'header'=> Mage::helper('Mpm')->__('Shipping'),
                'index' => 'shipping',
                'filter' => false,
                'type'  => 'number',
                'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Catalog_Product_Edit_Price',
                'align' => "right"
            ));

        $this->addColumn('total', array(
                'header'=> Mage::helper('Mpm')->__('Total'),
                'index' => 'total',
                'filter' => false,
                'type'  => 'number',
                'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Catalog_Product_Edit_Price',
                'align' => "right"

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

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/Mpm_Carl/ProductOffersGrid', array('product_id' => $this->getProduct()->getId()));
    }

    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }

    /**
     * @param boolean $channelToFilter
     */
    public function setChannelToFilter($channelToFilter)
    {
        $this->channelToFilter = $channelToFilter;
    }

    public function addFilters($filter){
        $this->filters = $filter;
    }

    public function setSortValue($sort)
    {
       $this->sort = $sort;
    }

}
