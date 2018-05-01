<?php

class MDN_Mpm_Model_CustomCollection extends Varien_Data_Collection
{
    protected $_filters = array();
    protected $_source = array();
    public static $_sortFields;

    public function initCollection($source)
    {
        $this->_source = $source;

    }

    public function addFieldToFilter($column, $condition)
    {
        $this->_filters[$column] = $condition;
    }

    public function load($printQuery = false, $logQuery = false)
    {

        if (!$this->_isCollectionLoaded)
        {
            //filter collection
            $items = array();
            foreach($this->_source as $item)
            {
                if ($this->itemFulfilFilters($item, $this->_filters))
                {
                    $data = new Varien_Object();
                    $data->setData($item);
                    $items[] = $data;
                }
            }

            //sort collection
            self::$_sortFields = $this->_orders;
            usort($items, array('MDN_Mpm_Model_CustomCollection', 'sortItem'));

            //fill collection
            foreach($items as $item)
            {
                $this->addItem($item);
            }

            $this->_isCollectionLoaded = true;
        }

        return $this;
    }

    /**
     * @param $a
     * @param $b
     */
    public static function sortItem($a, $b)
    {
        if (count(self::$_sortFields) > 0)
        {
            foreach(self::$_sortFields as $field => $direction)
            {
                $operator = ($direction == 'DESC' ? 1 : -1);
                if ($a[$field] == $b[$field]) {
                    return 0;
                }
                return ($a[$field] > $b[$field]) ? +1 * $operator : -1 * $operator;
            }
        }
        else
            return 0;
    }

    protected function itemFulfilFilters($item, $filters)
    {
        foreach($filters as $column => $condition)
        {
            $columnValue = (isset($item[$column]) ? $item[$column] : '');
            foreach($condition as $operator => $expr)
            {
                switch($operator)
                {
                    case 'like':
                        $expr = str_replace("'%", '', $expr);
                        $expr = str_replace("%'", '', $expr);
                        if (!preg_match('/'.$expr.'/', $columnValue))
                            return false;
                        break;
                }
            }

        }

        return true;
    }

}
