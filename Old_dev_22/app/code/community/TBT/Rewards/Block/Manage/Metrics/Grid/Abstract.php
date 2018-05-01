<?php

class TBT_Rewards_Block_Manage_Metrics_Grid_Abstract extends Mage_Adminhtml_Block_Report_Grid_Abstract
{
    /**
     * The name of the resource collection that should be used.
     *
     * @var string
     **/
    protected $_resourceCollectionName;

    protected $_columnGroupBy = 'period';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);

        return $this;
    }

    public function getResourceCollectionName()
    {
        return $this->_resourceCollectionName;
    }

    /**
     * Adds custom points statuses filter to resource collections.
     *
     * @param Mage_Reports_Model_Resource_Report_Collection_Abstract $collection
     * @param Varien_Object $filterData
     * @return TBT_Rewards_Block_Manage_Metrics_Abstract
     */
    protected function _addCustomFilter($collection, $filterData)
    {
        $transferStatuses = $filterData->getData('transfer_statuses');
        if (is_array($transferStatuses)) {
            if (count($transferStatuses) == 1 && strpos($transferStatuses[0],',') !== false) {
                $filterData->setData('transfer_statuses', explode(',',$transferStatuses[0]));
            }
        }

        $collection->addTransferStatusFilter($filterData->getData('transfer_statuses'));

        return $this;
    }

    /**
     * Get allowed store ids array intersected with selected scope in store switcher.
     * Implemented only for compatibility with Magento pre 1.6.
     *
     * @return  array
     */
    protected function _getStoreIds()
    {
        if (Mage::helper('rewards/version')->isBaseMageVersionAtLeast('1.6.0.0')) {
            return parent::_getStoreIds();
        }

        $filterData = $this->getFilterData();
        if ($filterData) {
            $storeIds = explode(',', $filterData->getData('store_ids'));
        } else {
            $storeIds = array();
        }
        // By default storeIds array contains only allowed stores
        $allowedStoreIds = array_keys(Mage::app()->getStores());
        // And then array_intersect with post data for prevent unauthorized stores reports
        $storeIds = array_intersect($allowedStoreIds, $storeIds);
        // If selected all websites or unauthorized stores use only allowed
        if (empty($storeIds)) {
            $storeIds = $allowedStoreIds;
        }
        // reset array keys
        $storeIds = array_values($storeIds);

        return $storeIds;
    }

    /**
     * Overwriting only for compatibility with Magento pre 1.6.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        if (Mage::helper('rewards/version')->isBaseMageVersionAtLeast('1.6.0.0')) {
            return parent::_prepareCollection();
        }

        $this->getFilterData()->setData('store_ids', implode(',', $this->_getStoreIds()));

        return parent::_prepareCollection();
    }
}
