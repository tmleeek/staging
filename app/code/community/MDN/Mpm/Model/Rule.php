<?php

/**
 *
 *
 */
class MDN_Mpm_Model_Rule extends Mage_Core_Model_Abstract {

    const kFormulaAggressiveMinPrice = 'aggressive_minimum_price';
    const kFormulaHarakiriMinPrice = 'harakiri_minimum_price';

    const kFormulaCost = 'cost_formula';

    const kFormulaMarginStandard = 'standard_margin';
    const kFormulaMarginAggressive = 'agressive_margin';
    //const kFormulaMarginNoCompetitor = 'no_competitor_margin';

    const kFormulaShippingMethod = 'shipping_method';
    const kFormulaShippingCoefficient = 'shipping_coefficient';
    const kFormulaShippingAllowZero = 'shipping_allow_zero';

    const kFormulaAdditionalCostParam = 'additional_cost_param';

    const kFormulaEnable = 'enable';

    const kFormulaAdjustmentCompeteWith = 'adjustment_compete_with';
    const kFormulaAdjustmentMethod = 'adjustment_method';
    const kFormulaAdjustmentValue = 'adjustment_value';

    const kFormulaMinPrice = 'minimum_price';
    const kFormulaMaxPrice = 'maximum_price';

    const kFormulaNoCompetitorMode = 'no_competitor_mode';
    const kFormulaNoCompetitorValue = 'no_competitor_value';

    const kFormulaCommission = 'commission';

    const kFormulaShippingPriceMode = 'shipping_price_mode';
    const kFormulaShippingPriceValue = 'shipping_price_value';

    //rules types
    const kTypeCost = 'cost';
    const kTypeMargin = 'margin';
    const kTypeEnable = 'enable';
    const kTypeShipping = 'shipping';
    const kTypeAdditionalCost = 'additional_cost';
    const kTypeAdjustment = 'adjustment';
    const kTypeCommission = 'commission';
    const kTypeNoCompetitor = 'no_competitor';
    const kTypeMinPrice = 'minimum_price';
    const kTypeMaxPrice = 'maximum_price';
    const kTypeProductsToMonitor = 'products_to_monitor';
    const kTypeShippingPrice = 'shipping_price';

    public function _construct() {
        parent::_construct();
        $this->_init('Mpm/Rule');
    }

    public function getProductsCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection();

        foreach($this->getPerimeterCondition('*') as $field => $list)
        {
            if ($list != '0') {
                if (($list == '') || ($this->isWildcard($list))) {
                    continue;
                }
            }

            switch($field)
            {
                case 'stock':
                    $collection->joinField('qty',
                        'cataloginventory/stock_item',
                        'qty',
                        'product_id=entity_id',
                        '{{table}}.stock_id=1',
                        'left');
                    $collection->addFieldToFilter('qty', array('gteq' => $list['from']));
                    $collection->addFieldToFilter('qty', array('lteq' => $list['to']));
                    break;
                case 'sku':
                    $list = explode(',', $list);
                    $list = array_map('trim', $list);
                    if (count($list) > 0)
                        $collection->addFieldToFilter('sku', array('in' => $list));
                    break;
                case 'categories':
                    $productIds = array();
                    foreach($list as $item) {
                        $productIds = array_merge($productIds, Mage::helper('Mpm/Category')->getProductIds($item));
                    }
                    $collection->addFieldToFilter('entity_id', $productIds);
                    break;
                case 'attributesets':
                    $collection->addFieldToFilter('attribute_set_id', array('in' => $list));
                    break;
                default:    //attributes !
                    $collection->addAttributeToSelect($field);
                    switch(Mage::helper('Mpm/Attribute')->getFrontEndInput($field))
                    {
                        case 'boolean':
                            $collection->addAttributeToFilter($field, $list);
                            break;
                        case 'date':
                        case 'datetime':
                        case 'weight':
                        case 'price':
                            $collection->addAttributeToFilter($field, array('gteq' => $list['from']));
                            $collection->addAttributeToFilter($field, array('lteq' => $list['to']));
                            break;
                        case 'select':
                        case 'multiselect':
                            $collection->addAttributeToFilter($field, array('in' => $list));
                            break;
                        case 'text':
                            $collection->addAttributeToFilter($field, array('like' => '%'.$list.'%'));
                            break;
                    }
                    break;
            }
        }

        return $collection;
    }

    public function getFormula($type)
    {
        if ($this->getcontent())
        {
            $contents = unserialize($this->getcontent());
            if (isset($contents[$type]))
                return $contents[$type];
        }
        return false;
    }

    public function getPerimeterCondition($field)
    {
        if ($this->getperimeter())
        {
            $perimeterConditions = unserialize($this->getperimeter());
            if ($field == '*')
                return $perimeterConditions;
            if (isset($perimeterConditions[$field]))
                return $perimeterConditions[$field];
        }
        return false;
    }

    public function isWildcard($values)
    {
        if (is_array($values))
        {
            if ((count($values) == 1) && ($values[0] == '*'))
                return true;
        }
        else
            return ($values == '*');
        return false;
    }

    /**
     * Before delete
     * @return type
     */
    protected function _beforeDelete()
    {
        $this->deleteRelatedProduct();

        return parent::_beforeDelete();
    }

    protected function deleteRelatedProduct()
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $tableName = $resource->getTableName('mpm_rules_products');
        $query = "delete from  ".$tableName." WHERE rule_id = ".(int)$this->getId();
        $writeConnection->query($query);
    }


    public function indexProducts()
    {
        Mage::helper('Mpm')->log('START index for rule #'.$this->getId());

        $productIds = $this->getProductsCollection()->getAllIds();

        $this->deleteRelatedProduct();
        if (!$this->getEnabled())
            return false;

        $resource = Mage::getSingleton('core/resource');
        $tableName = $resource->getTableName('mpm_rules_products');
        $writeConnection = $resource->getConnection('core_write');
        $channels = $this->getPerimeterCondition('channels');
        if ($this->isWildcard($channels) || !$channels) {
            $channels = array();
            foreach(Mage::helper('Mpm/Carl')->getChannelsSubscribed(true) as $k => $v)
            {
                $channels[] = $k;
            }
        }

        foreach($channels as $channelKey)
        {
            //fill table..
            foreach($productIds as $productId)
            {
                $query = "insert into  ".$tableName." (rule_id, product_id, channel) values (".(int)$this->getId().', '.$productId.', "'.$channelKey.'")';
                $writeConnection->query($query);
            }
        }

        Mage::helper('Mpm')->log('END index for rule #'.$this->getId().' ('.count($productIds).' products)');

        $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
        $this->setPreventIndex(1)->setlast_index(date('Y-m-d H:i:s', $currentTimestamp))->save();

        return count($productIds);
    }

    public function getHumanReadablePerimeter()
    {
        $html = array();

        //main fields
        foreach($this->getPerimeterCondition('*') as $field => $list)
        {
            if (!$this->isWildcard($list))
            {
                if ($list != '') {
                    $list = $this->convertListToHuman($field, $list);
                    $html[] = $field . ' : ' . implode(', ', $list);
                }
            }
        }

        if (count($html) > 0)
            return implode('<br>', $html);
        else
            return Mage::helper('Mpm')->__('All products');
    }

    public function convertListToHuman($field, $list)
    {
        $finalList = array();

        switch($field)
        {
            case 'stock':
                $finalList[] = 'between '.$list['from'].' and '.$list['to'];
                break;
            case 'sku':
                $finalList[] = 'in '.$list;
                break;
            case 'categories':
                foreach($list as $item) {
                    $finalList[] = Mage::getModel('catalog/category')->load($item)->getName();
                }
                break;
            case 'attributesets':
                foreach($list as $item) {
                    $finalList[] = Mage::getModel('eav/entity_attribute_set')->load($item)->getAttributeSetName();
                }
                break;
            case 'channels':
                foreach($list as $item) {
                    $finalList[] = $item;
                }
                break;
            default:    //attributes !
                switch(Mage::helper('Mpm/Attribute')->getFrontEndInput($field))
                {
                    case 'text':
                        $finalList[] = 'Contains "'.$list.'"';
                        break;
                    case 'boolean':
                        $finalList[] = ($list ? 'Yes' : 'No');
                        break;
                    case 'date':
                    case 'weight':
                    case 'datetime':
                    case 'price':
                        $finalList[] = 'between '.$list['from'].' and '.$list['to'];
                        break;
                    case 'select':
                    case 'multiselect':
                        foreach($list as $item) {
                            $finalList[] = Mage::helper('Mpm/Attribute')->getAttributeValueLabel($field, $item);
                        }
                        break;
                }
                break;
        }

        return $finalList;
    }

    public function calculate($product, $channel, $formula, $additionalCodes = array())
    {
        $result = array();

        $formula = $this->getFormula($formula);
        $result['initial_formula'] = $formula;

        $allDatas = array_merge($product->getData(), $additionalCodes);

        preg_match_all('/\{([^\}]+)\}/', $formula, $matches);
        $codes = $matches[1];
        foreach($codes as $code)
        {
            $value = '';
            if (isset($allDatas[$code]))
                $value = $allDatas[$code];
            if ($value == '')
                throw new Exception('No value available for '.$code);
            $formula = str_replace('{'.$code.'}', $value, $formula);
        }

        $result['final_formula'] = $formula;

        $result['result'] = 0;

        if ($result === false)
            throw new exception('Unable to calculate formula : '.$formula);

        return $result;
    }

    /**
     * After save
     */
    protected function _afterSave() {
        parent::_afterSave();

        if (!$this->getPreventIndex())
            $this->indexProducts();
    }

    public function getPerimeterSpecificFields()
    {
        $fields = array();
        $fields[] = array('value' => 'channels', 'label' => 'Channels');
        $fields[] = array('value' => 'sku', 'label' => 'Sku');
        $fields[] = array('value' => 'categories', 'label' => 'Categories');
        $fields[] = array('value' => 'attributesets', 'label' => 'Attributesets');
        $fields[] = array('value' => 'stock', 'label' => 'Stock');
        return $fields;
    }
}