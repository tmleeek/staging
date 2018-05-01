<?php

class MDN_Mpm_Block_Adminhtml_Catalog_Product_Edit_Tab_RepricingHistory extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('MpmRepricingHistoryGrid');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(mage::helper('Mpm')->__('No items'));
        $this->setDefaultSort('id', 'DESC');
    }

    /**
     * Charge la collection
     *
     * @return unknown
     */
    protected function _prepareCollection()
    {
        //charge
        $collection = Mage::getModel('Mpm/PricingLog')
            ->getCollection()
            ->addFieldToFilter('product_id', $this->getProduct()->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {

        $this->addColumn('id', array(
                'header'=> Mage::helper('Mpm')->__('#'),
                'index' => 'id',
                'filter' => false,
                'sortable' => false,
            ));

        $this->addColumn('created_at', array(
                'header'=> Mage::helper('Mpm')->__('Date'),
                'index' => 'created_at',
                'filter' => false,
                'sortable' => false,
                'align' => 'center'
            ));

        $this->addColumn('channel', array(
                'header'=> Mage::helper('Mpm')->__('Channel'),
                'index' => 'channel',
                'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Channel',
                'align' => 'center',
                'filter' => false,
                'sortable' => false,
            ));

        $this->addColumn('rule_id', array(
                'header' => Mage::helper('Mpm')->__('Rule'),
                'index' => 'rule_id',
                'type' => 'options',
                'options' => $this->getRules(),
                'filter' => false,
                'sortable' => false,
            ));

        $this->addColumn('behaviour', array(
                'header' => Mage::helper('Mpm')->__('Behaviour'),
                'index' => 'behaviour',
                'filter' => false,
                'sortable' => false,
            ));

        $this->addColumn('formula', array(
                'header' => Mage::helper('Mpm')->__('Formula'),
                'index' => 'formula',
                'filter' => false,
                'sortable' => false,
            ));

        $this->addColumn('final_price', array(
                'header' => Mage::helper('Mpm')->__('Final price'),
                'index' => 'final_price',
                'filter' => false,
                'sortable' => false,
            ));

        $this->addColumn('error', array(
                'header' => Mage::helper('Mpm')->__('Error ?'),
                'index' => 'error',
                'filter' => false,
                'sortable' => false,
            ));

        $this->addColumn('debug', array(
                'header' => Mage::helper('Mpm')->__('Message'),
                'index' => 'debug',
                'filter' => false,
                'sortable' => false,
            ));

        $this->addColumn('status', array(
                'header' => Mage::helper('Mpm')->__('Status'),
                'index' => 'status',
                'filter' => false,
                'sortable' => false,
            ));

        $this->addColumn('is_current', array(
                'header' => Mage::helper('Mpm')->__('Current ?'),
                'index' => 'is_current',
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

}
