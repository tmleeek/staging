<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_AdvancedStock_Model_SalesHistory extends Mage_Core_Model_Abstract {

    /**
     * Constructor
     */
    public function _construct() {
        parent::_construct();
        $this->_init('AdvancedStock/SalesHistory');
    }

    /**
     * Refresh history
     */
    public function refresh() {
        $productId = $this->getsh_product_id();
        if (!$productId)
            throw new Exception('Unable to refresh sales history on empty product number !');
        $ranges = mage::helper('AdvancedStock/Sales_History')->getRanges();
        foreach ($ranges as $index => $range) {
            $value = $this->getSalesNumber($productId, $range);
            $this->setData('sh_period_' . ($index + 1), $value);
        }
        $this->setsh_updated_at(date('Y-m-d'));

        $this->save();
        return $this;
    }

    /**
     * Return sales for 1 product and a specific number of weeks
     */
    public function getSalesNumber($productId, $weeksCount) {
        $dateStartTimestamp = time() - $weeksCount * 7 * 24 * 3600;
        $dateStart = date('Y-m-d', $dateStartTimestamp);

        //todo : use magento query system instead of sql direct
        $sql = "select
					sum(qty_invoiced) 
				from 
					" . Mage::getConfig()->getTablePrefix() . "sales_flat_order_item
				where 
					product_id = " . $productId . "
					and created_at >= '" . $dateStart . "' ";
        return mage::getResourceModel('sales/order_item_collection')->getConnection()->fetchOne($sql);
    }

    /**
     * Aftersave
     *
     */
    protected function _afterSave() {
        parent::_afterSave();

        //raise event if values has changed
        $raise = false;
        if (!$this->getOrigData('sh_id'))
            $raise = true;

        for ($i = 1; $i <= 3; $i++) {
            if ($this->objectDataHasChanged('sh_period_' . $i))
                $raise = true;
        }

        if ($raise)
            Mage::dispatchEvent('advancedstock_sales_history_change', array('sales_history' => $this));
    }

    /**
     * Return true if data has changed
     */
    protected function objectDataHasChanged($dataName) {
        $origValue = $this->getOrigData($dataName);
        $currentValue = $this->getData($dataName);

        return ($origValue != $currentValue);
    }

}