<?php

class TBT_Rewards_Block_Widget_Grid extends Mage_Core_Block_Template
{
    protected $_defaultLimit = 15;

    protected $_defaultPage = 1;

    protected $_defaultSort = false;

    protected $_defaultDir = 'desc';

    protected $_defaultFilter = null;

    /**
     * @var array
     */
    protected $_columns = array();

    /**
     * @var Varien_Data_Collection
     */
    protected $_collection = null;

    /**
     * Empty grid text
     * @var sting|null
     */
    protected $_emptyText = null;

    /**
     * Empty grid text CSS class
     * @var sting|null
     */
    protected $_emptyTextCss = 'a-center';

    /**
     * @var boolean
     */
    protected $_pagerVisibility = true;

    /**
     * @var boolean
     */
    protected $_headersVisibility = true;

    /**
     * @var boolean
     */
    protected $_filterVisibility = false;

    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('rewards/widget/grid.phtml');
        $this->_emptyText = $this->__('No records found.');

        //$this->setRowClickCallback('openGridRow');

        return $this;
    }

    /**
     * @param Varien_Data_Collection $collection
     * @return self
     */
    public function setCollection(Varien_Data_Collection $collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    /**
     * @return Varien_Data_Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Add column to grid
     *
     * @param string $columnId
     * @param array  $column
     * 
     * @return self
     */
    public function addColumn($columnId, $column)
    {
        if (is_array($column)) {
            $this->_columns[$columnId] = $this->getLayout()->createBlock('adminhtml/widget_grid_column')
                ->setData($column)
                ->setGrid($this);
        } else {
            throw new Exception(Mage::helper('adminhtml')->__('Wrong column format.'));
        }

        $this->_columns[$columnId]->setId($columnId);

        return $this;
    }

    /**
     * Remove existing column
     * @param  string $columnId
     * @return self
     */
    public function removeColumn($columnId)
    {
        if (isset($this->_columns[$columnId])) {
            unset($this->_columns[$columnId]);
        }

        return $this;
    }

    /**
     * Gets the number of columns in this grid
     * @return int
     */
    public function getColumnCount()
    {
        return count($this->getColumns());
    }

    /**
     * Retrieve grid column by column id
     * @param  string $columnId
     * @return Mage_Adminhtml_Block_Widget_Grid_Column || false
     */
    public function getColumn($columnId)
    {
        if (!empty($this->_columns[$columnId])) {
            return $this->_columns[$columnId];
        }
        return false;
    }

    /**
     * Retrieve all grid columns
     * @return array
     */
    public function getColumns()
    {
        return $this->_columns;
    }

    /**
     * Prepare grid collection object
     * @return self
     */
    protected function _prepareCollection()
    {
        if (!$this->getCollection()) {
            return $this;
        }

        $this->_preparePage();

        $columnIds = $this->getSort();
        if (!is_array($columnIds)) {
            $columnIds = array($columnIds);
        }

        $dirs = $this->getDir();
        if (!is_array($dirs)) {
            $dirs = array($dirs);
        }

        $filter = $this->getFilter();
        if (is_null($filter)) {
            $filter = array();
        }

        if (is_string($filter)) {
            $data = $this->helper('adminhtml')->prepareFilterString($filter);
            $this->_setFilterValues($data);
        }
        else if ($filter && is_array($filter)) {
            $this->_setFilterValues($filter);
        }

        foreach ($columnIds as $key => $columnId) {
            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = isset($dirs[$key]) ? $dirs[$key] : 'asc';
                $dir = (strtolower($dir) == 'desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $this->_setCollectionOrder($this->_columns[$columnId]);
            }
        }

        $this->_beforeLoadCollection();
        $this->getCollection()->load();
        $this->_afterLoadCollection();

        return $this;
    }

    protected function _preparePage()
    {
        $this->getCollection()->setPageSize($this->getLimit());
        $this->getCollection()->setCurPage($this->getPage());
        return $this;
    }

    protected function _prepareColumns()
    {
        return $this;
    }

    protected function _prepareGrid()
    {
        $this->_prepareColumns();
        $this->_prepareCollection();
        return $this;
    }

    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();

        $this->_prepareGrid();

        return $this;
    }

    protected function _beforeLoadCollection()
    {
        return $this;
    }

    protected function _afterLoadCollection()
    {
        return $this;
    }

    /**
     * Sets sorting order by some column
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return self
     */
    protected function _setCollectionOrder($column)
    {
        $collection = $this->getCollection();
        if ($collection) {
            $columnIndex = $column->getFilterIndex() ?
                $column->getFilterIndex() : $column->getIndex();
            $collection->setOrder($columnIndex, strtoupper($column->getDir()));
        }
        return $this;
    }

    protected function _setFilterValues($data)
    {
        foreach ($this->getColumns() as $columnId => $column) {
            if (isset($data[$columnId])
                && (!empty($data[$columnId]) || strlen($data[$columnId]) > 0)
                && $column->getFilter()
            ) {
                $column->getFilter()->setValue($data[$columnId]);
                $this->_addColumnFilterToCollection($column);
            }
        }
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
            if ($column->getFilterConditionCallback()) {
                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
            } else {
                $cond = $column->getFilter()->getCondition();
                if ($field && isset($cond)) {
                    $this->getCollection()->addFieldToFilter($field , $cond);
                }
            }
        }
        return $this;
    }

    /**
     * @param string $text
     * @return self
     */
    public function setEmptyText($text)
    {
        $this->_emptyText = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmptyText()
    {
        return $this->_emptyText;
    }

    /**
     * @param string $cssClass
     * @return self
     */
    public function setEmptyTextClass($cssClass)
    {
        $this->_emptyTextCss = $cssClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmptyTextClass()
    {
        return $this->_emptyTextCss;
    }

    /**
     * Check whether should render cell
     *
     * @param Varien_Object $item
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return boolean
     */
    public function shouldRenderCell($item, $column)
    {
        if ($this->isColumnGrouped($column) && $item->getIsEmpty()) {
            return true;
        }
        if (!$item->getIsEmpty()) {
            return true;
        }
        return false;
    }

    /**
     * Check whether should render empty cell
     *
     * @param Varien_Object $item
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return boolean
     */
    public function shouldRenderEmptyCell($item, $column)
    {
        return ($item->getIsEmpty() && in_array($column['index'], $this->_groupedColumn));
    }

    /**
     * Retrieve colspan for empty cell
     * @param Varien_Object $item
     * @return int
     */
    public function getEmptyCellColspan()
    {
        return $this->getColumnCount() - count($this->_groupedColumn);
    }

    /**
     * Retrieve label for empty cell
     * @return string
     */
    public function getEmptyCellLabel()
    {
        return $this->_emptyCellLabel;
    }

    /**
     * Retrieve rowspan number
     *
     * @param Varien_Object $item
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return integer|boolean
     */
    public function getRowspan($item, $column)
    {
        if ($this->isColumnGrouped($column)) {
            return count($this->getMultipleRows($item)) + count($this->_groupedColumn);
        }
        return false;
    }

    /**
     * @param string|object $column
     * @param string $value
     * @return boolean|Mage_Adminhtml_Block_Widget_Grid
     */
    public function isColumnGrouped($column, $value = null)
    {
        return false;
        
        if (null === $value) {
            if (is_object($column)) {
                return in_array($column->getIndex(), $this->_groupedColumn);
            }
            return in_array($column, $this->_groupedColumn);
        }
        $this->_groupedColumn[] = $column;
        return $this;
    }

    public function getLimit()
    {
        if ($this->getData('limit')) {
            return $this->getData('limit');
        }

        return $this->_defaultLimit;
    }

    public function getPage()
    {
        if ($this->getData('page')) {
            return $this->getData('page');
        }

        return $this->_defaultPage;
    }

    public function getSort()
    {
        if ($this->getData('sort')) {
            return $this->getData('sort');
        }

        return $this->_defaultSort;
    }

    public function getDir()
    {
        if ($this->getData('dir')) {
            return $this->getData('dir');
        }

        return $this->_defaultDir;
    }

    public function getFilter()
    {
        if ($this->getData('filter')) {
            return $this->getData('filter');
        }

        return $this->_defaultFilter;
    }

    /**
     * @param boolean $headersVisibility
     * @return self
     */
    public function setHeadersVisibility($headersVisibility = true)
    {
        $this->_headersVisibility = $headersVisibility;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getHeadersVisibility()
    {
        return $this->_headersVisibility;
    }

    /**
     * @param boolean $pagerVisibility
     * @return self
     */
    public function setPagerVisibility($pagerVisibility = true)
    {
        $this->_pagerVisibility = $pagerVisibility;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getPagerVisibility()
    {
        return $this->_pagerVisibility;
    }

    /**
     * @param boolean $filterVisibility
     * @return self
     */
    public function setFilterVisibility($filterVisibility = true)
    {
        $this->_filterVisibility = $filterVisibility;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getFilterVisibility()
    {
        return $this->_filterVisibility;
    }

    /**
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getId().'JsObject';
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getCurrentUrl();
    }
}
