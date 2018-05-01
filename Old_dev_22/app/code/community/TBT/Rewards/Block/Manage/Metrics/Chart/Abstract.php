<?php

class TBT_Rewards_Block_Manage_Metrics_Chart_Abstract extends Mage_Core_Block_Template
{

    /**
     * Data Helper.
     *
     * @var TBT_Rewards_Helper_Metrics_Chart_Abstract
     **/
    protected $_helper;

    protected function _construct()
    {
        parent::_construct();
        return $this;
    }

    /**
     * Setter for the data helper to be used to get the data for the chart.
     *
     * @param string $helperClass
     * @return  TBT_Rewards_Block_Manage_Metrics_Chart_Abstract
     */
    public function setDataHelper($helperClass)
    {
        $this->_helper = $this->helper($helperClass);
        return $this;
    }

    /**
     * Getter for the chart data helper class.
     *
     * @return TBT_Rewards_Helper_Metrics_Chart_Abstract
     */
    public function getDataHelper()
    {
        return $this->_helper;
    }

    /**
     * Sets the filter data and prepares data for the chart.
     *
     * @return [type] [description]
     */
    protected function _beforeToHtml()
    {
        $this->setFilterData($this->getParentBlock()->getFilterData());
        $this->_prepareData();

        return parent::_beforeToHtml();
    }

    /**
     * Prepares data for the chart. Overwrite in child classes as needed.
     *
     * @return TBT_Rewards_Block_Manage_Metrics_Chart_Abstract
     */
    protected function _prepareData()
    {
        if ($this->getFilterData()) {
            $this->getDataHelper()->setParams($this->getFilterData()->getData());
        }

        return $this;
    }

    /**
     * Retrieve format needed for chart's X axis (time axis) based on the 'period_type' filter value.
     *
     * @return string
     */
    public function getChartDateFormat()
    {
        $format = '%b %d, %Y';
        if (!$this->getFilterData()) {
            return $format;
        }

        $filterData = $this->getFilterData()->getData();
        if (isset($filterData['period_type'])) {
            if ($filterData['period_type'] == 'month') {
                $format = '%b %Y';
            } elseif ($filterData['period_type'] == 'year') {
                $format = '%Y';
            }
        }

        return $format;
    }

    /**
     * Returns the Y axis format as defined in the data helper class.
     *
     * @return string
     */
    public function getChartYFormat()
    {
        return $this->getDataHelper()->getYAxisFormat();
    }

    /**
     * Returns any symbol that should preceed the Y axis values. For example "$".
     * Should be defined in the helper class.
     *
     * @return string
     */
    public function getPreSymbol()
    {
        return $this->getDataHelper()->getPreSymbol();
    }

    /**
     * Returns any symbol that should succed the Y axis values. For example "$" for currency.
     * Should be defined in the helper class.
     *
     * @return string
     */
    public function getPostSymbol()
    {
        return $this->getDataHelper()->getPostSymbol();
    }

    /**
     * Get allowed store ids array intersected with selected scope in store switcher
     *
     * @return  array
     */
    protected function _getStoreIds()
    {
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
     * If we don't have the filter data set, we won't show any chart.
     *
     * @return bool True, if we should display the chart block or false otherwise.
     */
    public function getDisplayChart()
    {
        if ($this->getFilterData()) {
            $filterData = $this->getFilterData()->getData();
        }

        return !empty($filterData);
    }

    /**
     * Returns the data for the chart, JSON encoded, ready for displaying.
     *
     * @return string
     */
    public function getChartData()
    {
        $data = $this->getDataHelper()->getAllSeries();
        return Zend_Json::encode($data);
    }
}
