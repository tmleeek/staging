<?php

abstract class TBT_Rewards_Helper_Metrics_Chart_Abstract extends Mage_Core_Helper_Abstract
{

    /**
     * All chart series
     *
     * @var array
     **/
    protected $_allSeries;

    /**
     * Paramaters (filters) to apply to the data retrieved for the chart.
     *
     * @var array
     **/
    protected $_params = array();

    /**
     * undocumented class variable
     *
     * @var string
     **/
    protected $_collection;

    protected $_currentCurrencyCode = null;
    protected $_storeIds            = array();

    /**
     * Initializes collection of data. Implement this in the cild classes.
     *
     * @return TBT_Rewards_Helper_Metrics_Chart_Abstract
     */
    abstract protected  function _initSeries();

    /**
     * Getter for all set parameters.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Setter for chart data parameters.
     *
     * @param array $params
     */
    public function setParams($params = array())
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Getter for a parameter by name.
     *
     * @param  string $name The name of the parameter key to be retrieved.
     * @return null|string
     */
    public function getParam($name)
    {
        if (isset($this->_params[$name])){
            return $this->_params[$name];
        }

        return null;
    }

    /**
     * Setter for a parameter by name
     *
     * @param string $name
     * @param string $value
     * @return  TBT_Rewards_Helper_Metrics_Chart_Abstract
     */
    public function setParam($name, $value)
    {
        $this->_params[$name] = $value;
        return $this;
    }

    /**
     * Get all chart series.
     *
     * @return array
     */
    public function getAllSeries()
    {
        if (is_null($this->_allSeries)) {
            $this->_initSeries();
        }

        return $this->_allSeries;
    }

    /**
     * Setter for all chart series.
     *
     * @param array $allSeries
     * @return  TBT_Rewards_Helper_Metrics_Chart_Abstract
     */
    public function setAllSeries($allSeries = array())
    {
        $this->_allSeries = $allSeries;
        return $this;
    }

    /**
     * Add series
     *
     * @param array $options
     * @return  TBT_Rewards_Helper_Metrics_Chart_Abstract
     */
    public function addSeries(array $options)
    {
        $this->_allSeries[] = $options;
        return $this;
    }

    /**
     * Get series
     *
     * @param int $seriesId
     * @return mixed
     */
    public function getSeries($seriesId)
    {
        if (isset($this->_allSeries[$seriesId])) {
            return $this->_allSeries[$seriesId];
        } else {
            return false;
        }
    }

    /**
     * Get allowed store ids array intersected with selected scope in store switcher
     *
     * @return  array
     */
    protected function _getStoreIds()
    {
        if (!empty($this->_storeIds)) {
            return $this->_storeIds;
        }

        $storeIds = $this->getParam('store_ids');
        if ($storeIds) {
            $storeIds = explode(',', $storeIds);
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
        $this->_storeIds = array_values($storeIds);

        return $this->_storeIds;
    }

    /**
     * Merge the result with empty data for the 'period' that doesn't contain any data.
     *
     * @param  string $period       The Period type ('day'|'month'|'year')
     * @param  string $startDate    Starting period date.
     * @param  string $endDate      Ending period date.
     * @param  string $column       This is the column from the series that represents out chart Y value.
     * @return TBT_Rewards_Helper_Metrics_Chart_Abstract
     */
    protected function _mergeWithEmptyData($period, $startDate, $endDate, $column)
    {
        $allSeries = $this->getAllSeries();
        foreach ($allSeries as $index => &$series) {
            $seriesData = $series['values'];
            $period = $period ? $period : 'day';

            $current = $this->_getFormatedDate($startDate, $period);
            $end     = $this->_getFormatedDate($endDate, $period);

            $result = array();

            $data = array_shift($seriesData);
            while ($current <= $end) {
                if ($data['period'] == $current) {
                    $newData = array();
                    $newData['x'] = $this->_getEpochTimestamp($data['period']);
                    $newData['y'] = (float) $data[$column];
                    array_push($result, $newData);
                    $data = array_shift($seriesData);
                } else {
                    $newData = array();
                    $newData['x'] = $this->_getEpochTimestamp($current);
                    $newData['y'] = 0;
                    array_push($result, $newData);
                }
                $current = $this->_getFormatedDate($this->_addPeriod($current, $period), $period);
            }

            $series['values'] = $result;
        }
        $this->setAllSeries(($allSeries));

        return $this;
    }

    /**
     * Gets a date string or an int representing a timestamp and returns a date string format as required by $format.
     *
     * @param string|int $date     Date string or timestamp.
     * @param string $format       Format that is used for calculating the timestamp. For example, if format is 'year'
     *  it will only get the year part from $date and use '01' as month and day.
     * @return string
     */
    protected function _getFormatedDate($date, $format = 'day')
    {
        if (!$date) {
            return false;
        }

        // strtotime will fail on a date like '2014'
        if (strpos($date, '-') === false && strlen($date) == 4) {
            $date = $date . '-01';
        }

        if (!is_int($date)) {
            $date = strtotime($date);
        }

        switch ($format) {
            case 'year':
                $dateTimeFormat = 'Y';
                break;
            case 'month':
                $dateTimeFormat = 'Y-m';
                break;
            default:
                $dateTimeFormat = 'Y-m-d';
                break;
        }

        $result = date($dateTimeFormat, $date);

        return $result;
    }

    /**
     * Returns a Epoch timestamp for a passed in string date
     *
     * @param  string $date
     * @return int
     */
    protected function _getEpochTimestamp($date)
    {
        // strtotime will fail on a date like '2014'
        if (strpos($date, '-') === false && strlen($date) == 4) {
            $date = $date . '-01';
        }

        if (!is_int($date)) {
            $date = strtotime($date);
        }
        $timestamp = $date * 1000;

        return $timestamp;
    }

    /**
     * Adds 1 year, month, day to the date specified.
     *
     * @param $date
     * @param $period   Period to add (year, month, date).
     *
     * @return int
     */
    protected function _addPeriod($date, $period)
    {
        // strtotime will fail on a date like '2014'
        if (strpos($date, '-') === false && strlen($date) == 4) {
            $date = $date . '-01';
        }
        if (!is_int($date)) {
            $date = strtotime($date);
        }

        return strtotime("+1 {$period}", $date);
    }

    /**
     * Get currency rate (base to given currency)
     *
     * @param string|Mage_Directory_Model_Currency $currencyCode
     * @return double
     */
    public function getRate($toCurrency)
    {
        return Mage::app()->getStore()->getBaseCurrency()->getRate($toCurrency);
    }

    /**
     * Return current currency code.
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        $storeIds = $this->_getStoreIds();
        if (is_null($this->_currentCurrencyCode)) {
            $this->_currentCurrencyCode = (count($storeIds) > 0)
                ? Mage::app()->getStore(array_shift($storeIds))->getBaseCurrencyCode()
                : Mage::app()->getStore()->getBaseCurrencyCode();
        }
        return $this->_currentCurrencyCode;
    }

    /**
     * Returns current currency symbol.
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        $currencyCode = $this->getCurrentCurrencyCode();
        $currencySymbol = Mage::app()->getLocale()->currency($currencyCode)->getSymbol();

        return $currencySymbol;
    }

    /**
     * Overwrite in child classes to define any pre symbol to be used for chart's Y axis value formatting.
     * As a possible example '$'
     *
     * @return null|string
     */
    public function getPreSymbol()
    {
        return null;
    }

    /**
     * Overwrite in child classes to define any post symbol to be used for chart's Y axis value formatting.
     * As a possible example '%'
     *
     * @return null|string
     */
    public function getPostSymbol()
    {
        return null;
    }

    /**
     * Sets the format for the chart's Y axis values. Overwrite in child classes as needed.
     * For possible values check @link https://github.com/mbostock/d3/wiki/Formatting
     *
     * @return null|string
     */
    public function getYAxisFormat()
    {
        return null;
    }

    /**
     * Formats a number as currency and returns it.
     *
     * @param  int|float $content
     * @return float
     */
    protected function _formatContentAsCurrency($content)
    {
        $currency_code = $this->getCurrentCurrencyCode();

        if (!$currency_code) {
            return $content;
        }

        $content = floatval($content) * $this->getRate($currency_code);
        $content = sprintf("%f", $content);
        $content = Mage::app()->getLocale()->currency($currency_code)->toCurrency($content);

        return $content;
    }
}
