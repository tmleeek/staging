<?php

require_once(Mage::getModuleDir('controllers', 'TBT_Rewards') . DS . 'Admin' . DS . 'AbstractController.php');

class TBT_Rewards_Manage_MetricsController extends TBT_Rewards_Admin_AbstractController
{
    /**
     * Admin session model
     *
     * @var null|Mage_Admin_Model_Session
     */
    protected $_adminSession = null;

    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(
                Mage::helper('rewards')->__('Sweet Tooth'),
                Mage::helper('rewards')->__('Sweet Tooth')
            );

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction ()->renderLayout ();
        return $this;
    }

    /**
     * Number of loyalty members report.
     * A customer is considered a member of the loyalty program as soon as he earns any points.
     *
     * @return TBT_Rewards_Manage_MetricsController
     */
    public function numberOfMembersAction()
    {
        $this->_title($this->__('Sweet Tooth Reports'))
             ->_title($this->__('Number of Members'));

        $this->_initAction()
            ->_setActiveMenu('rewards/metrics/numberOfMembers');

        $gridBlock       = $this->getLayout()->getBlock('manage_metrics_numberOfMembers.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
        $chartsBlock      = $this->getLayout()->getBlock('rewards.metrics.charts');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock,
            $chartsBlock
        ));

        $this->renderLayout();

        return $this;
    }


    /**
     * Export numberOfMembers report grid to CSV format
     *
     * @return TBT_Rewards_Manage_MetricsController
     */
    public function exportNumberOfMembersCsvAction()
    {
        $fileName = 'numberOfMembers.csv';
        $grid     = $this->getLayout()->createBlock('rewards/manage_metrics_numberOfMembers_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());

        return $this;
    }

    /**
     * Export numberOfMembers report grid to Excel XML format
     *
     * @return TBT_Rewards_Manage_MetricsController
     */
    public function exportNumberOfMembersExcelAction()
    {
        $fileName = 'numberOfMembers.xml';
        $grid     = $this->getLayout()->createBlock('rewards/manage_metrics_numberOfMembers_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));

        return $this;
    }

    /**
     * [revenueAction description]
     *
     * @return TBT_Rewards_Manage_MetricsController
     */
    public function revenueAction()
    {
        $this->_title($this->__('Sweet Tooth Reports'))
             ->_title($this->__('Members Revenue'));

        $this->_initAction()
            ->_setActiveMenu('rewards/metrics/revenue');

        $gridBlock       = $this->getLayout()->getBlock('manage_metrics_revenue.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
        $chartsBlock      = $this->getLayout()->getBlock('rewards.metrics.charts');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock,
            $chartsBlock
        ));

        $this->renderLayout();

        return $this;
    }

    /**
     * Export loyalty members revenue report grid to CSV format
     *
     * @return TBT_Rewards_Manage_MetricsController
     */
    public function exportRevenueCsvAction()
    {
        $fileName = 'revenue.csv';
        $grid     = $this->getLayout()->createBlock('rewards/manage_metrics_revenue_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());

        return $this;
    }

    /**
     * Export loyalty members revenue report grid to Excel XML format
     *
     * @return TBT_Rewards_Manage_MetricsController
     */
    public function exportRevenueExcelAction()
    {
        $fileName = 'revenue.xml';
        $grid     = $this->getLayout()->createBlock('rewards/manage_metrics_revenue_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));

        return $this;
    }

    /**
     * [redemptionRateAction description]
     *
     * @return [type] [description]
     */
    public function redemptionRateAction()
    {
        $this->_title($this->__('Sweet Tooth Reports'))
             ->_title($this->__('Members Redemption Rate'));

        $this->_initAction()
            ->_setActiveMenu('rewards/metrics/redemptionRate');

        $gridBlock       = $this->getLayout()->getBlock('manage_metrics_redemptionRate.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
        $chartsBlock      = $this->getLayout()->getBlock('rewards.metrics.charts');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock,
            $chartsBlock
        ));

        $this->renderLayout();

        return $this;
    }

    /**
     * Export loyalty members revenue report grid to CSV format
     *
     * @return TBT_Rewards_Manage_MetricsController
     */
    public function exportRedemptionRateCsvAction()
    {
        $fileName = 'redemptionRate.csv';
        $grid     = $this->getLayout()->createBlock('rewards/manage_metrics_redemptionRate_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());

        return $this;
    }

    /**
     * Export loyalty members revenue report grid to Excel XML format
     *
     * @return TBT_Rewards_Manage_MetricsController
     */
    public function exportRedemptionRateExcelAction()
    {
        $fileName = 'redemptionRate.xml';
        $grid     = $this->getLayout()->createBlock('rewards/manage_metrics_redemptionRate_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));

        return $this;
    }

    public function earningsAction()
    {
        $this->_title($this->__('Sweet Tooth Reports'))
             ->_title($this->__('Members Earnings Distribution'));

        $this->_initAction()
            ->_setActiveMenu('rewards/metrics/earnings');

        $gridBlock       = $this->getLayout()->getBlock('manage_metrics_earnings.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
        $chartsBlock      = $this->getLayout()->getBlock('rewards.metrics.charts');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock,
            $chartsBlock
        ));

        $this->renderLayout();

        return $this;
    }

    /**
     * Export loyalty members earnings distribution report grid to CSV format
     *
     * @return TBT_Rewards_Manage_MetricsController
     */
    public function exportEarningsCsvAction()
    {
        $fileName = 'earnings_distribution.csv';
        $grid     = $this->getLayout()->createBlock('rewards/manage_metrics_earnings_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());

        return $this;
    }

    /**
     * Export loyalty members earnings distribution report grid to Excel XML format
     *
     * @return TBT_Rewards_Manage_MetricsController
     */
    public function exportEarningsExcelAction()
    {
        $fileName = 'earnings_distribution.xml';
        $grid     = $this->getLayout()->createBlock('rewards/manage_metrics_earnings_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));

        return $this;
    }

    /**
     * Report action init operations
     *
     * @param array|Varien_Object $blocks
     * @return TBT_Rewards_Manage_MetricsController
     */
    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }

        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData = $this->_filterDates($requestData, array('from', 'to'));
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new Varien_Object();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }

        foreach ($blocks as $block) {
            if ($block) {
                $block->setPeriodType($params->getData('period_type'));
                $block->setFilterData($params);
            }
        }

        return $this;
    }

    /**
     * Check permissions
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'revenue':
                return $this->_getSession()->isAllowed('rewards/metrics/revenue');
                break;
            case 'numberOfMembers':
                return $this->_getSession()->isAllowed('rewards/metrics/numberOfMembers');
                break;
            case 'redemptionRate':
                return $this->_getSession()->isAllowed('rewards/metrics/redemptionRate');
                break;
            case 'earnings':
                return $this->_getSession()->isAllowed('rewards/metrics/earnings');
                break;
            default:
                return $this->_getSession()->isAllowed('rewards/metrics');
                break;
        }
    }

    /**
     * Retrieve admin session model
     *
     * @return Mage_Admin_Model_Session
     */
    protected function _getSession()
    {
        if (is_null($this->_adminSession)) {
            $this->_adminSession = Mage::getSingleton('admin/session');
        }

        return $this->_adminSession;
    }
}
