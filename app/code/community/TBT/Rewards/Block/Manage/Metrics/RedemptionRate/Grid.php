<?php

class TBT_Rewards_Block_Manage_Metrics_RedemptionRate_Grid extends TBT_Rewards_Block_Manage_Metrics_Grid_Abstract
{
    protected $_resourceCollectionName = 'rewards/metrics_redemptionRate_collection';

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

        $this->addColumn('members', array(
            'header'           => Mage::helper('rewards')->__('Number of Members'),
            'header_css_class' => 'a-right',
            'index'            => 'members',
            'width'            => '100px',
            'total'            => 'sum',
            'type'             => 'number',
            'sortable'         => false,
        ));

        $this->addColumn('members_percentage', array(
            'header'           => Mage::helper('rewards')->__('Percentage of Total Members'),
            'header_css_class' => 'a-right',
            'index'            => 'members_percentage',
            'width'            => '100px',
            'align'            => 'right',
            'renderer'         => 'rewards/manage_grid_renderer_percentage',
            'total'            => 'sum',
            'sortable'         => false,
        ));

        if ($this->getFilterData() && $this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }

        $this->addExportType('*/*/exportRedemptionRateCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportRedemptionRateExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
