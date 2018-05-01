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
class MDN_AdvancedStock_Helper_Product_PreferedStockLevel extends Mage_Core_Helper_Abstract {

    /**
     * return sum of ideal stock for all warehouses
     * @param <type> $productId
     */
    public function getIdealStockLevelForAllStocks($productId)
    {
        $value = 0;
        $stocks = mage::helper('AdvancedStock/Product_Base')->getStocks($productId);
        foreach ($stocks as $stock) {
            if ($stock->getstock_disable_supply_needs() != 1)
                $value += $stock->getIdealStockLevel();
        }
        return $value;
    }

    /**
     * Calculate prefered stock level for product and force history creation
     */
    public function updateForProductWithHistoryInit($productId) {
        $this->updateForProduct($productId, true);
    }

    /**
     * Calculate prefered stock level for product
     */
    public function updateForProduct($productId, $forceSalesHistoryCreation = false) {
        
        //get suggestion
        $data = $this->getSuggestion($productId);

        //Minimum Ideal Stock level
        $minimumLevel = mage::getStoreConfig('advancedstock/prefered_stock_level/minimum_levels_to_apply');
        if(!isset($minimumLevel)){
            $minimumLevel = 0;
        }

        //update warehouses
        $stocks = mage::helper('AdvancedStock/Product_Base')->getStocks($productId);
        foreach ($stocks as $stock) {
            if ($this->canModifyWarehouse($stock->getstock_id())) {
                $updated = false;

                //update warning level
                if ($stock->getWarningStockLevel() != $data['warning_stock_level']) {
                    if($data['warning_stock_level']>=0){
                        if($data['warning_stock_level'] > $minimumLevel){
                            $stock->setuse_config_notify_stock_qty(0);
                            $stock->setnotify_stock_qty($data['warning_stock_level']);
                            $updated = true;
                        }
                    }
                }

                //update ideal stock
                if ($stock->getIdealStockLevel() != $data['ideal_stock_level']) {
                    if($data['ideal_stock_level']>=0){
                        if($data['ideal_stock_level'] > $minimumLevel){
                            $stock->setuse_config_ideal_stock_level(0);
                            $stock->setideal_stock_level($data['ideal_stock_level']);
                            $updated = true;
                        }
                    }
                }

                //save
                if ($updated){
                    if (Mage::getStoreConfig('advancedstock/general/avoid_magento_auto_reindex')) {
                      $stock->setProcessIndexEvents(false);
                    }
                    $stock->save();
                }
            }
        }
    }

    /**
     * Return suggestion based on sales history
     */
    public function getSuggestion($productId) {

        $data = array('warning_stock_level' => null, 'ideal_stock_level' => null);

        //calculate value
        $helper = mage::helper('AdvancedStock/Sales_History');
        $salesHistory = $helper->getForOneProduct($productId);
        if (!$salesHistory->getId())
            return $data;

        $formula = mage::getStoreConfig('advancedstock/prefered_stock_level/formula');
        $forecastWeeks = mage::getStoreConfig('advancedstock/prefered_stock_level/calculation_weeks');
        $formula = str_replace('duration', $forecastWeeks, $formula);
        foreach ($helper->getRanges() as $index => $range) {
            //replace code for the number of sales
            $formula = str_replace('s' . ($index + 1), $salesHistory->getData('sh_period_' . ($index + 1)), $formula);

            //replace code for the number of weeks
            $formula = str_replace('w' . ($index + 1), $range, $formula);
        }

        //calculate
        $value = eval('return ' . $formula . ';');

        //affect values
        $data['ideal_stock_level'] = $value;
        $percent = Mage::getStoreConfig('advancedstock/prefered_stock_level/substract_percent_to_calculate_warning_stock_level');
        $data['warning_stock_level'] = (int)($value - ($value * $percent / 100)) ;

        return $data;
    }

    /**
     * Return true if we can change preferd stock level for warehouse
     */
    public function canModifyWarehouse($stock_id) {
        $warehouses = mage::getStoreConfig('advancedstock/prefered_stock_level/enable_for_warehouses');
        $t = explode(',', $warehouses);
        return in_array($stock_id, $t);
    }

}