<?php

class MDN_Mpm_Model_PricingCollection extends MDN_Mpm_Model_CustomCollection
{

    private $size;

    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->_isCollectionLoaded) {
            $this->_pageSize = $this->_pageSize === false ? 1 : $this->_pageSize;
            $products = Mage::helper('Mpm/Carl')->getProducts($this->_getCarlFilters(), $this->_orders, $this->_pageSize, $this->_curPage);
            $this->size = $products->total;
            foreach($products->results as $product) {
                $productData = new Varien_Object();
                $this->addPricingInformation($productData, $product);
                $this->addItem($productData);
            }

            $this->_isCollectionLoaded = true;
        }



        return $this;
    }

    private function addPricingInformation(Varien_Object $productData, $product)
    {
        $productData->id = uniqid();
        $productData->matching_status = $product->matching_status;
        $productData->channel = $product->channel;
        $productData->reference = $product->reference;
        $productData->product_id = $product->product_id;
        $productData->status = $product->status;
        if (($pos = strpos($product->error, ":")) !== FALSE) {
            $productData->error = substr($product->error, $pos+1);
        }else{
            $productData->error = $product->error;
        }
        $productData->bbw_name = $product->bbw_name;
        $productData->my_position = $product->my_position == 0 ? 'NC' : $product->my_position;
        $productData->target_position = $product->target_position == 0 ? 'NC' : $product->target_position;
        $productData->best_offer = $product->best_offer;
        $productData->base_cost = $product->base_cost;
        $productData->tax_amount = $product->tax_amount;
        $productData->commission_amount = $product->commission_amount;
        $productData->final_margin = $product->final_margin;
        $productData->margin_for_bbw = $product->margin_for_bbw;
        $productData->final_price = $product->final_price + (isset($product->shipping_price) ? $product->shipping_price : 0);
        $productData->created_at = $product->created_at;
        $productData->updated_at = $product->updated_at;
        $productData->name = $product->name;
        $productData->stock = $product->stock;
        $productData->supplier = $product->supplier;
        $productData->brand = $product->brand;
        $productData->category = $product->category;
        $productData->behavior = $product->behavior;
        $productData->rules_result = $product->rules_result;
        $productData->debug = $product->debug;

        return $productData;
    }

    public function getSize()
    {
        return $this->size;
    }

    private function _getCarlFilters()
    {
        $filters = array();
        foreach($this->_filters as $field => $condition) {
            if($condition === null) {
                continue;
            }

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
                else
                    $filters[$field] = $conditionValue;
            } elseif(isset($condition['from']) || $condition['to']) {
                $conditionValue = 'between(%s|%s)';

                $from = isset($condition['from']) ? $condition['from'] : '';
                $to   = isset($condition['to']) ? $condition['to'] : '';

                $filters[$field] = sprintf($conditionValue, $from, $to);
            } else {
                Mage::Log('condtion unknown: '.serialize(array('field' => $field, 'condition' => $condition)));
            }
        }

        foreach($filters as $k => $v)
            $filters[$k] = str_replace("\\\\", "", $filters[$k]);

        return $filters;
    }
}
