<?php

class TBT_Rewards_Block_Manage_Metrics_Revenue_Grid extends TBT_Rewards_Block_Manage_Metrics_Grid_Abstract
{
    protected $_resourceCollectionName = 'rewards/metrics_revenue_collection';

    public function getResourceCollectionName()
    {
        return 'rewards/metrics_revenue_collection';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'          => Mage::helper('rewards')->__('Period'),
            'index'           => 'period',
            'width'           => '100px',
            'sortable'        => false,
            'period_type'     => $this->getPeriodType(),
            'renderer'        => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label'    => Mage::helper('rewards')->__('Total'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('is_member', array(
            'header'           => Mage::helper('rewards')->__('Loyalty Member'),
            'header_css_class' => 'a-right',
            'align'            => 'right',
            'index'            => 'is_member',
            'sortable'         => false,
            'width'            => '50px',
            'totals_label'     => '',
            'type'             => 'options',
            'options'          => array(
                '1' => Mage::helper('rewards')->__('Yes'),
                '0' => Mage::helper('rewards')->__('No'),
            ),
        ));

        $this->addColumn('orders_count', array(
            'header'           => Mage::helper('rewards')->__('Orders'),
            'header_css_class' => 'a-right',
            'index'            => 'orders_count',
            'type'             => 'number',
            'total'            => 'sum',
            'sortable'         => false,
        ));

        $currencyCode = $this->getCurrentCurrencyCode();
        $rate         = $this->getRate($currencyCode);

        $this->addColumn('total_revenue_amount', array(
            'header'           => Mage::helper('rewards')->__('Revenue'),
            'header_css_class' => 'a-right',
            'type'             => 'currency',
            'currency_code'    => $currencyCode,
            'index'            => 'total_revenue_amount',
            'total'            => 'sum',
            'sortable'         => false,
            'rate'             => $rate,
        ));

        if ($this->getFilterData() && $this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }

        $this->addExportType('*/*/exportRevenueCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportRevenueExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
