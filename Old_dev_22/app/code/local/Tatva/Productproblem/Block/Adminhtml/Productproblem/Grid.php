<?php

class Tatva_Productproblem_Block_Adminhtml_Productproblem_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('productproblemGrid');
      $this->setDefaultSort('productproblem_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {


      $collection = Mage::getModel('productproblem/productproblem')->getCollection();
      $collection->getSelect()
                 ->join('catalog_product_entity','`catalog_product_entity`.`entity_id`=`main_table`.`productid`',array('sku'));
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('productproblem_id', array(
          'header'    => Mage::helper('productproblem')->__('Problem ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'productproblem_id',
      ));

      $this->addColumn('productid', array(
          'header'    => Mage::helper('productproblem')->__('Product ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'productid',
      ));

      $this->addColumn('sku', array(
          'header'    => Mage::helper('productproblem')->__('Product SKU'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'sku',
      ));

      $this->addColumn('name', array(
          'header'    => Mage::helper('productproblem')->__('Name'),
          'align'     =>'left',
          'index'     => 'name',
      ));

      $this->addColumn('email', array(
          'header'    => Mage::helper('productproblem')->__('Email'),
          'align'     =>'left',
          'index'     => 'email',
      ));


      $this->addColumn('reply', array(
          'header'    => Mage::helper('productproblem')->__('Reply Sent'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'reply',
          'type'      => 'options',
          'options'   => array(
              1 => 'Yes',
              0 => 'No',
          ),
      ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('productproblem')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('productproblem')->__('Show'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('productproblem')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('productproblem')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('productproblem_id');
        $this->getMassactionBlock()->setFormFieldName('productproblem');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('productproblem')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('productproblem')->__('Are you sure?')
        ));

        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}