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


class Mirasvit_Rma_Model_Resource_Report_Rma_Collection extends Mage_Sales_Model_Mysql4_Report_Collection_Abstract
{
    protected $_periodFormat;
    protected $_reportType;
    protected $_selectedColumns = array();

    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('rma/rma');
        $this->setConnection($this->getResource()->getReadConnection());
    }

    protected function _applyDateRangeFilter()
    {
        if (!is_null($this->_from)) {
            $this->getSelect()->where($this->_periodFormat.' >= ?', $this->_from);
        }
        if (!is_null($this->_to)) {
            $this->getSelect()->where($this->_periodFormat.' <= ?', $this->_to);
        }
        return $this;
    }

    public function _applyStoresFilter()
    {
        $nullCheck = false;
        $storeIds = $this->_storesIds;

        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }

        $storeIds = array_unique($storeIds);

        if ($index = array_search(null, $storeIds)) {
            unset($storeIds[$index]);
            $nullCheck = true;
        }

        if ($nullCheck) {
            $this->getSelect()->where('store_id IN(?) OR store_id IS NULL', $storeIds);
        } elseif ($storeIds[0] != '') {
            $this->getSelect()->where('store_id IN(?)', $storeIds);
        }
        return $this;
    }

    public function setFilterData($filterData)
    {
        if (isset($filterData['report_type'])) {
            $this->_reportType = $filterData['report_type'];
        } else {
            $this->_reportType = 'all';
        }
        return $this;
    }

    protected function _getSelectedColumns()
    {
        if ('month' == $this->_period) {
            $this->_periodFormat = 'DATE_FORMAT(main_table.created_at, \'%Y-%m\')';
        } elseif ('year' == $this->_period) {
            $this->_periodFormat = 'EXTRACT(YEAR FROM main_table.created_at)';
        } else {
            $this->_periodFormat = 'DATE_FORMAT(main_table.created_at, \'%Y-%m-%d\')';
        }

            $this->_selectedColumns = array(
                'created_at' => $this->_periodFormat,
                // 'new_rma_cnt' => 'SUM(if (status_id = 1, 1, 0))',
                // 'approved_rma_cnt' => 'SUM(if (status_id = 2, 1, 0))',
                // 'rejected_rma_cnt' => 'SUM(if (status_id = 3, 1, 0))',
                // 'closed_rma_cnt' => 'SUM(if (status_id = 4, 1, 0))',
                'total_rma_cnt' => 'COUNT(*)',
                'total_product_cnt' => 'SUM(rma_item.qty_requested)',
            );
            foreach(Mage::helper('rma')->getStatusCollection() as $status) {
                $this->_selectedColumns["{$status->getId()}_cnt"] = "SUM(if (status_id = {$status->getId()}, 1, 0))";
            }
            if ($this->_reportType == 'by_product') {
                $this->_selectedColumns['product_id'] = 'rma_item.product_id';
            }

        // if ($this->isTotals()) {
        // }

        // if ($this->isSubTotals()) {
        // }
        return $this->_selectedColumns;
    }

    protected  function _initSelect()
    {
        $select = $this->getSelect();
        $select->from(array('main_table' => $this->getResource()->getMainTable()) , $this->_getSelectedColumns());

        if (!$this->isTotals() && !$this->isSubTotals()) {
            //Ð¿Ð¾Ð»Ñ Ð¿Ð¾ ÐºÐ¾ÑÐ¾ÑÑÐ¼ Ð±ÑÐ´ÑÑ ÑÐ´ÐµÐ»Ð°Ð½Ñ Ð³ÑÑÐ¿Ð¿Ð¸ÑÐ¾Ð²ÐºÐ¸ Ð¿ÑÐ¸ Ð²ÑÐ²Ð¾Ð´Ðµ Ð¾ÑÑÐµÑÐ°
            $select->group(array(
                $this->_periodFormat,
            ));
            if ($this->_reportType == 'by_product') {
                $select->group('product_id');
            }
        }
        if ($this->isSubTotals()) {
            $select->group(array(
                $this->_periodFormat,
            ));
        }
        $select->joinLeft(array('rma_item' => $this->getTable('rma/item')), 'main_table.rma_id = rma_item.rma_id', array());
        // echo $this->getSelect();die;
        return $this;
    }

    /************************/

}