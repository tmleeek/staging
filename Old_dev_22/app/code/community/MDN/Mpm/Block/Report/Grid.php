<?php

class MDN_Mpm_Block_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('Mpmreports');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(Mage::helper('Mpm')->__('No Items Found'));
        $this->setDefaultSort('id', 'desc');
    }

    /**
     * Charge la collection des devis
     *
     * @return unknown
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('Mpm/Report')
            ->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * D�fini les colonnes du grid
     *
     * @return unknown
     */
    protected function _prepareColumns() {

        $this->addColumn('id', array(
                'header' => Mage::helper('Mpm')->__('Id'),
                'index' => 'id',
            ));

        $this->addColumn('report_id', array(
                'header' => Mage::helper('Mpm')->__('Report id'),
                'index' => 'report_id'
            ));

        $this->addColumn('report_type', array(
                'header' => Mage::helper('Mpm')->__('Type'),
                'index' => 'report_type'
            ));

        $this->addColumn('report_params', array(
                'header' => Mage::helper('Mpm')->__('Params'),
                'index' => 'report_params',
                'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Report_Param'
            ));

        $this->addColumn('requested_at', array(
                'header' => Mage::helper('Mpm')->__('Requested at'),
                'index' => 'requested_at',
                'type' => 'datetime'
            ));

        $this->addColumn('status', array(
                'header' => Mage::helper('Mpm')->__('Status'),
                'index' => 'status'
            ));

        $this->addColumn('result', array(
                'header' => Mage::helper('Mpm')->__('Result'),
                'index' => 'result'
            ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Delete'),
                        'url'     => array(
                            'base'=>'Mpm/Report/Delete'
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false
            ));

        return parent::_prepareColumns();
    }

    public function getGridParentHtml() {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative' => true));
        return $this->fetchView($templateName);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setChild('import_offers_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                        'label'     => Mage::helper('Mpm')->__('Import offers'),
                        'onclick'   => "setLocation('".$this->getUrl('adminhtml/Mpm_Carl/ImportOffers')."')"
                    ))
        );

        $this->setChild('import_commissions_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('Mpm')->__('Import commissions'),
                    'onclick'   => "setLocation('".$this->getUrl('adminhtml/Mpm_Carl/ImportCommissions')."')"
                ))
        );

    }

    public function getImportOffersButtonHtml()
    {
        return $this->getChildHtml('import_offers_button');
    }


    public function getImportCommissionsButtonHtml()
    {
        return $this->getChildHtml('import_commissions_button');
    }

    public function getMainButtonsHtml()
    {
        $html = '';
        if($this->getFilterVisibility()){
            $html.= $this->getResetFilterButtonHtml();
            $html.= $this->getSearchButtonHtml();
            $html.= $this->getImportOffersButtonHtml();
            $html.= $this->getImportCommissionsButtonHtml();
        }
        return $html;
    }

}
