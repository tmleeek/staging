<?php

class Tatva_Advice_Block_Adminhtml_Advice_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('adviceGrid');
      $this->setDefaultSort('advice_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('advice/advice')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('advice_id', array(
          'header'    => Mage::helper('advice')->__('ID'),
          'align'     =>'right',
          'width'     => '150px',
          'index'     => 'advice_id',
      ));

      $this->addColumn('advice_fr', array(
          'header'    => Mage::helper('advice')->__('Advice In France'),
          'align'     =>'left',
          'index'     => 'advice_fr',
      ));

      $this->addColumn('advice_en', array(
          'header'    => Mage::helper('advice')->__('Advice In English'),
          'align'     =>'left',
          'index'     => 'advice_en',
      ));


      $this->addColumn('material', array(
			'header'    => Mage::helper('advice')->__('Item Material'),
			'width'     => '350px',
			'index'     => 'material',
      ));


      $this->addColumn('status', array(
          'header'    => Mage::helper('advice')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('advice')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('advice')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('advice')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('advice')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('advice_id');
        $this->getMassactionBlock()->setFormFieldName('advice');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('advice')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('advice')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('advice/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('advice')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('advice')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}