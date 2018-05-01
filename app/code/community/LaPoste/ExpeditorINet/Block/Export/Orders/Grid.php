<?php
/**
 * LaPoste_ExpeditorINet
 * 
 * @category    LaPoste
 * @package     LaPoste_ExpeditorINet
 * @copyright   Copyright (c) 2010 La Poste
 * @author      Smile (http://www.smile.fr) & Jibé
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LaPoste_ExpeditorINet_Block_Export_Orders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('expeditorinet_export_order_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare order collection (for different Magento versions)
     * @return LaPoste_ExpeditorINet_Block_Export_Orders_Grid
     */
    protected function _prepareCollection()
    {
        if (version_compare(Mage::getVersion(), '1.4', '>=')) {
            $collection = Mage::getResourceModel('sales/order_grid_collection')
                ->join('order', 'main_table.entity_id = order.entity_id', array('shipping_method'));

        } else {
            $collection = Mage::getResourceModel('sales/order_collection')
                ->addAttributeToSelect(array('status', 'shipping_method'))
                ->joinAttribute('billing_firstname', 'order_address/firstname', 'billing_address_id', null, 'left')
                ->joinAttribute('billing_lastname', 'order_address/lastname', 'billing_address_id', null, 'left')
                ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
                ->addExpressionAttributeToSelect(
                    'billing_name',
                    'CONCAT({{billing_firstname}}, " ", {{billing_lastname}})',
                    array('billing_firstname', 'billing_lastname')
                )
                ->addExpressionAttributeToSelect(
                    'shipping_name',
                    'CONCAT({{shipping_firstname}}, " ", {{shipping_lastname}})',
                    array('shipping_firstname', 'shipping_lastname')
                );
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns (for different Magento versions)
     * @return LaPoste_ExpeditorINet_Block_Export_Orders_Grid
     */
    protected function _prepareColumns()
    {

        $columnData = array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        );
        if (version_compare(Mage::getVersion(), '1.4', '>=')) {
            $columnData['filter_index'] = 'main_table.'.$columnData['index'];
        }
        $this->addColumn('real_order_id', $columnData);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'store_id', array(
                    'header'    => Mage::helper('sales')->__('Purchased from (store)'),
                    'index'     => 'store_id',
                    'type'      => 'store',
                    'store_view'=> true,
                    'display_deleted' => true,
                )
            );
        }

        $columnData = array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        );
        if (version_compare(Mage::getVersion(), '1.4', '>=')) {
            $columnData['filter_index'] = 'main_table.'.$columnData['index'];
        }
        $this->addColumn('created_at', $columnData);

        $this->addColumn(
            'billing_name', array(
                'header' => Mage::helper('sales')->__('Bill to Name'),
                'index' => 'billing_name',
            )
        );

        $this->addColumn(
            'shipping_name', array(
                'header' => Mage::helper('sales')->__('Ship to Name'),
                'index' => 'shipping_name',
            )
        );

        $columnData = array(
            'header'   => Mage::helper('sales')->__('G.T. (Base)'),
            'index'    => 'base_grand_total',
            'type'     => 'currency',
            'currency' => 'base_currency_code'
        );
        if (version_compare(Mage::getVersion(), '1.4', '>=')) {
            $columnData['filter_index'] = 'main_table.'.$columnData['index'];
        }
        $this->addColumn('base_grand_total', $columnData);

        $this->addColumn(
            'carrier', array(
                'header' => Mage::helper('sales')->__('Carrier'),
                'index' => 'shipping_method',
            )
        );

        $columnData = array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        );
        if (version_compare(Mage::getVersion(), '1.4', '>=')) {
            $columnData['filter_index'] = 'main_table.'.$columnData['index'];
        }
        $this->addColumn('status', $columnData);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn(
                'action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'adminhtml/sales_order/view'),
                            'field'   => 'order_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
                )
            );
        }

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action (for different Magento versions)
     * @return LaPoste_ExpeditorINet_Block_Export_Orders_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        if (Mage::getVersion() >= '1.4.1') {
            $this->getMassactionBlock()->setUseSelectAll(false);
        }

        $this->getMassactionBlock()->addItem(
            'export_order', array(
                'label'=> Mage::helper('expeditorinet')->__('Export to Expeditor Inet'),
                'url'  => $this->getUrl('*/*/export'),
            )
        );

        return $this;
    }

    /**
     * Get url called when user click on a grid row 
     * @return string|boolean
     */
    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    /**
     * Get grid url
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/*', array('_current'=>true));
    }

}
