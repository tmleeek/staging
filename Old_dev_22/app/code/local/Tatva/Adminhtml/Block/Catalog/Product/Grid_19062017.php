<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tatva_Adminhtml_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    //protected $_massactionBlockName = 'ewmpaction/mage_adminhtml_widget_grid_massaction';
    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');

    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

   protected function _prepareCollection()
    {
       
        $store = $this->_getStore();
         $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
			->addAttributeToSelect('lengow_id')
            ->addAttributeToSelect('gamme_collection_new')
            ->addAttributeToSelect('manufacturer')
			 ->addAttributeToSelect('thumbnail')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('lowest_supplier_price1')
            ->addAttributeToSelect('marginrate')
            //->addAttributeToSelect('margin_rate')
            ->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
         
        
        
        if ($store->getId()) {
            
            //$collection->setStoreId($store->getId());
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
			//$collection->joinAttribute('lengow_id', 'catalog_product/int', 'lengow_id', null, 'inner', $store->getId());
            //echo $collection->joinAttribute('marque_value', 'catalog_product/attribute_option_value', 496, null, 'inner', $store->getId())->load(true);
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
            
            
        }
        else {
            
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
            
        }
        //$collection->getSelect()->joinLeft(Mage::getConfig()->getTablePrefix().'marginrate_cron', 'e.entity_id ='.Mage::getConfig()->getTablePrefix().'marginrate_cron.product_id',array('pps_last_price'=>'pps_last_price','margin_rate'=>'margin_rate'));
        //echo $collection->getselect();die();
        //echo "<pre>";print_r($marginrate->getData());die();
        //$collection->getSelect()->joinLeft("marginrate_cron","marginrate_cron.product_id = e.entity_id",array("marginrate_cron.pps_last_price"));
        //$collection->getSelect()->limit(0,10);
        //$collection->setPage(0,20);
        //echo "<pre>";print_r($collection->getData());die();
        //echo $collection->getSelect();die();
        //$collection->getSelect()->columns('MIN(pps_last_price) AS pps_last_price');
        //$collection->getSelect()->group('e.entity_id');
        
        
        //$collection->getSelect()->columns(array('pps_last_price'=>'MIN(pps_last_price)'))->group('e.entity_id');
        //echo count($collection);
        //echo $collection->getSelect()->__toString();die();
       //$collection->getSelect()->joinLeft(Mage::getConfig()->getTablePrefix().'purchase_product_supplier', 'e.entity_id ='.Mage::getConfig()->getTablePrefix().'purchase_product_supplier.pps_product_id',array('*'));
       //echo "<pre>";print_r($collection->getData());die();
        
        //$collection->getSelect()->join(array('mep' => "purchase_product_supplier"), "e.entity_id = mep.pps_product_id", array('mep.pps_last_price'))->join('purchase_supplier', 'sup_id=pps_supplier_num');
        //$collection->getSelect()->columns(array('pps_last_price'=>'MIN(pps_last_price)'));
        //echo count($collection);
        //$collection->getSelect()->limit(700);
        //echo $collection->getSelect();
        //$collection->getSelect()->columns('price as margin_rate');
        //echo "<pre>";print_r($collection->getData());die();
        // $collection->load(true);

        $this->setCollection($collection);

        parent::_prepareCollection();

        $this->getCollection()->addWebsiteNamesToResult();

        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
       if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {

	  $this->addColumn('thumbnail',
                array(
                    'header'=> Mage::helper('catalog')->__('Thumbnail'),
                    'type'  => 'image',
                    'index' => 'thumbnail',

					'renderer'  => 'Tatva_Adminhtml_Block_Catalog_Product_Renderer_Red',
      ));
			
	   $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
		
		$this->addColumn('lengow_id',
            array(
                'header'=> Mage::helper('catalog')->__('Lengow ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'lengow_id',
        ));
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
        ));
		
	   
		$manufacturer_items = Mage::getModel('eav/entity_attribute_option')->getCollection()->setStoreFilter()->join('attribute','attribute.attribute_id=main_table.attribute_id and entity_type_id = 4', 'attribute_code');

		foreach ($manufacturer_items as $manufacturer_item)
        {
            if ($manufacturer_item->getAttributeCode() == 'manufacturer')
            {
              $manufacturer_options[$manufacturer_item->getOptionId()] = $manufacturer_item->getValue();
            }
            elseif($manufacturer_item->getAttributeCode() == 'gamme_collection_new')
            {
              $gamme_collection_options[$manufacturer_item->getOptionId()] = $manufacturer_item->getValue();
            }
       }

        $this->addColumn('gamme_collection_new',
            array(
                'header'=> Mage::helper('catalog')->__('Collection'),
                'index' => 'gamme_collection_new',
                'type'  => 'options',
                'options' => $gamme_collection_options
        ));
		

 
        $this->addColumn('manufacturer',
            array(
                'header'=> Mage::helper('catalog')->__('Marque'),
                'width' => '100px',
                'type'  => 'options',
                'index' => 'manufacturer',
                'options' => $manufacturer_options
        ));
		
        
        
        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name In %s', $store->getName()),
                    'index' => 'custom_name',
            ));
        }

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
        ));

        /*$this->addColumn('pps_last_price',
            array(
                'header'=> Mage::helper('catalog')->__('Lowest Supplier Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'pps_last_price'
        ));*/
        $this->addColumn('lowest_supplier_price1',
            array(
                'header'=> Mage::helper('catalog')->__('Lowest Supplier Price'),
                'index'=>'lowest_supplier_price1',
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'renderer'  => 'Tatva_Adminhtml_Block_Catalog_Product_Renderer_Lowestprice',
        ));
        $this->addColumn('marginrate',
            array(
                'header'=> Mage::helper('catalog')->__('Margin Rate (%)'),
                'index'=>'marginrate',
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'renderer'  => 'Tatva_Adminhtml_Block_Catalog_Product_Renderer_Marginrate'
        ));
        $this->addColumn('qty',
            array(
                'header'=> Mage::helper('catalog')->__('Qty'),
                'width' => '100px',
                'type'  => 'number',
                'index' => 'qty',
        ));

        $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));


        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();
        /*Mage::dispatchEvent('ewmpaction_product_grid_prepare_massaction', array(
         'block' => $this
        ));*/
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('catalog')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('catalog')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));

        $this->getMassactionBlock()->addItem('price', array(
             'label'=> Mage::helper('catalog')->__('Change price'),
             'url'  => $this->getUrl('*/*/massPrice', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'price',
                         'type' => 'text',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Price'),
                         'values' => ''
                     )
             )
        ));

        $this->getMassactionBlock()->addItem('attributes', array(
            'label' => Mage::helper('catalog')->__('Update attributes'),
            'url'   => $this->getUrl('*/catalog_product_action_attribute/edit', array('_current'=>true))
        ));

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }
	public function colIsVisible($code) {
        return isset($this->columnSettings[$code]);
    }
    
}
