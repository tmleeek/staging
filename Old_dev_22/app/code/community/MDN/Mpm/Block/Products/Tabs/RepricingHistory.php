<?php

class MDN_Mpm_Block_Products_Tabs_RepricingHistory extends Mage_Adminhtml_Block_Widget_Grid
{
    private $channelsInformations = null;

    public function __construct()
    {
        parent::__construct();
        $this->setId('MpmRepricingHistoryGrid');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(mage::helper('Mpm')->__('No items'));
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setTemplate('Mpm/Products/Tabs/Grid.phtml');

    }

    public function getChannelsInformations()
    {
        if($this->channelsInformations === null){
            $this->channelsInformations = Mage::helper('Mpm/Carl')->getChannelsSubscribed();
        }
        return  $this->channelsInformations;
    }


    protected function getChannelCurrency()
    {
        $channel = $this->getChannel();

        if($channel === 'magento')
            return Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();

        $channelsInformations = $this->getChannelsInformations();
        foreach($channelsInformations as $channelInformations){
            if($channel === $channelInformations->channelCode){
                return Mage::app()->getLocale()->currency($channelInformations->currency)->getSymbol();
            }
        }
    }

    public function getProduct()
    {
        return Mage::registry('mpm_product');
    }

    public function getChannel()
    {
        return Mage::registry('mpm_channel');
    }

    /**
     * Charge la collection
     *
     * @return unknown
     */
    protected function _prepareCollection()
    {
        $pricingHistory = new MDN_Mpm_Model_PricingHistoryCollection();
        $pricingHistory->setProductId($this->getProduct()->getProductId());
        $pricingHistory->setChannel($this->getChannel());
        $pricingHistory->load();

        $this->setCollection($pricingHistory);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $this->addColumn('created_at', array(
            'header'=> Mage::helper('Mpm')->__('Date'),
            'index' => 'created_at',
            'filter' => false,
            'sortable' => false,
            'align' => 'center',
            'type' => 'datetime'
        ));

        $this->addColumn('final_price', array(
            'header'=> Mage::helper('catalog')->__('My price'),
            'index' => 'final_price',
            'filter' => false,
            'sortable' => false,
            'width' => '100px',
            'type'  => 'price',
            'frame_callback' => array($this, 'columnCallBack'),

            ));

        $this->addColumn('margin', array(
            'header' => Mage::helper('Mpm')->__('My margin %'),
            'index' => 'margin',
            'filter' => false,
            'sortable' => false,
            'align' => 'center'
        ));

        $this->addColumn('behaviour', array(
            'header' => Mage::helper('Mpm')->__('Behaviour'),
            'index' => 'behaviour',
            'filter' => false,
            'sortable' => false,
            'type' => 'options',
            'options' => Mage::getSingleton('Mpm/System_Config_Behaviour')->toArrayKey(),
            'align' => 'center'
        ));

        $this->addColumn('my_position', array(
            'header'=> Mage::helper('catalog')->__('Found rank'),
            'width' => '100px',
            'index' => 'my_position',
            'align' => 'center'
        ));

        $this->addColumn('target_position', array(
            'header'=> Mage::helper('catalog')->__('Estimated rank'),
            'width' => '100px',
            'index' => 'target_position',
            'align' => 'center'
        ));

        $this->addColumn('status', array(
            'header'=> Mage::helper('catalog')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('Mpm/System_Config_PricingStatus')->toArrayKey(),
            'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_Status',
            'align' => 'center',
        ));

        $this->addColumn('debug', array(
            'header' => Mage::helper('Mpm')->__('Message'),
            'index' => 'debug',
            'filter' => false,
            'sortable' => false,
        ));

        return parent::_prepareColumns();
    }

    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }

    protected function getRules()
    {
        $retour = array();
        $collection = Mage::getModel('Mpm/Rule')->getCollection();
        foreach($collection as $item)
        {
            $retour[$item->getId()] = $item->getname();
        }
        return $retour;
    }

    public function columnCallBack($value, $row, $column, $isExport)
    {
        if ($isExport)
            return $value;
        if ($row->geterror() && ($column->getIndex() != 'status'))
            return;
        $currency =  $this->getChannelCurrency();
        return $currency .' '.$value;
    }
}
