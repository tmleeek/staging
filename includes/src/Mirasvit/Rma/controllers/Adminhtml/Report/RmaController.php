<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   RMA
 * @version   1.0.1
 * @revision  135
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Rma_Adminhtml_Report_RmaController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('rma');
        return $this;
    }

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


    public function indexAction()
    {
        $this->_title($this->__('RMAs Report'));
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_rma.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
        $chartBlock = $this->getLayout()->getBlock('grid.chart')->setGrid($gridBlock);
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock,
            $chartBlock,
        ));
        $this->renderLayout();
    }

    /**
     * Export report grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'report_rma.csv';
        $grid       = $this->getLayout()->createBlock('rma/adminhtml_report_rma_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Exportreport grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'report_rma.xml';
        $grid       = $this->getLayout()->createBlock('rma/adminhtml_report_rma_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcel($fileName));
    }
}