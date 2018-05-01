<?php

class Tatva_Collectionpages_Block_Adminhtml_Collectionpages_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('collectionpagesGrid');
      $this->setDefaultSort('collectionpages_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection =Mage::getModel('collectionpages/collectionpages')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {

      $this->addColumn('brands_id', array(
          'header'    => Mage::helper('collectionpages')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'brands_id',
      ));



      $this->addColumn('option_id', array(
          'header'    => Mage::helper('collectionpages')->__('Option Id'),
          'align'     =>'left',
          'index'     => 'option_id',

      ));


      $this->addColumn('option_value', array(
          'header'    => Mage::helper('collectionpages')->__('Option Value'),
          'align'     =>'left',
          'index'     => 'option_value',

      ));
      $this->addColumn('status', array(
          'header'    => Mage::helper('collectionpages')->__('Status'),
          'align'     =>'left',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array('1'=>'Created','2'=>'Remaining'),
      ));

	  $this->addExportType('*/*/exportCsv', Mage::helper('collectionpages')->__('CSV'));
	  $this->addExportType('*/*/exportXml', Mage::helper('collectionpages')->__('XML'));

      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('collectionpages_id');
        $this->getMassactionBlock()->setFormFieldName('collectionpages');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('collectionpages')->__('Create Page(s)'),
             'url'      => $this->getUrl('*/*/massCreatepages'),
             'confirm'  => Mage::helper('collectionpages')->__('Are you sure?')
        ));

        return $this;
    }

   public function getRowUrl($row)
  {
      return '';
  }


  protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }


  protected function _filterStoreCondition($collection, $column)
  {
   if (!$value = $column->getFilter()->getValue())
    {
      return;
    }
    $this->getCollection()->addStoreFilter($value);
  }

}