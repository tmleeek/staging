<?php

class MDN_Mpm_Model_ProductsRuleCollection extends MDN_Mpm_Model_CustomCollection
{
    private $size;
    private $ruleId;
    private $filters;
    private $sort = array();

    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->_isCollectionLoaded) {
            if(!empty($this->ruleId)) {
                $results = Mage::helper('Mpm/Carl')->getProductsRule($this->ruleId,
                    ($this->_curPage - 1) * $this->_pageSize, $this->_pageSize, $this->filters, $this->sort);

                $this->size = $results->total;
                foreach($results->results as $product) {
                    $productData = new Varien_Object();

                    $productData->product_id = $product->product_id;
                    $productData->reference = $product->reference;
                    $productData->channel = $product->channel;
                    $productData->label = $product->name;
                    $productData->updated_at = $product->updated_at;

                    $this->addItem($productData);
                }
            }

            $this->_isCollectionLoaded = true;
        }

        return $this;
    }

    public function setRuleId($ruleId)
    {
        $this->ruleId = $ruleId;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setQueryFilter($filters)
    {
        $this->filters =  $filters;
    }

    public function setSort($sort)
    {
        $this->sort =  $sort;
    }

    private function _getCarlFilters()
    {
        $filters = array();
        foreach($this->_filters as $field => $condition) {
            if(is_string($condition)) {
                $filters[$field] = $condition;
                continue;
            }
            $conditionValue = current($condition);
            $conditionType   = key($condition);

            if($conditionType === 'eq') {
                $filters[$field] = '"'.$conditionValue.'"';
            } elseif($conditionType === 'like') {
                if($conditionValue instanceof Zend_Db_Expr) {
                    $filters[$field] = substr($conditionValue, 2, strlen($conditionValue) - 4);
                }
            } elseif(isset($condition['from']) || $condition['to']) {
                $conditionValue = 'between(%s|%s)';

                $from = isset($condition['from']) ? $condition['from'] : '';
                $to   = isset($condition['to']) ? $condition['to'] : '';

                $filters[$field] = sprintf($conditionValue, $from, $to);
            } else {
                Mage::Log('condtion unknown: '.serialize(array('field' => $field, 'condition' => $condition)));
            }
        }

        return $filters;
    }
}
