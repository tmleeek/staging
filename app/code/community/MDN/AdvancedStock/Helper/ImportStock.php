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
class MDN_AdvancedStock_Helper_ImportStock extends Mage_Core_Helper_Abstract {

    /**
     *
     * @param type $lines
     * @param type $warehouseId
     * @param type $date
     * @param type $smCaption
     * @return string 
     */
    public function process($lines, $warehouseId, $date, $smCaption) {
        $debug = '';

        //parse lines
        $i = 0;
        foreach ($lines as $line) {

            //explode fields
            $fields = explode(';', $line);
            if (count($fields) != 4) {
                $debug .= '<font color="red">Line "' . $line . '" is not correct</font><br>';
                continue;
            }

            //get data
            $sku = trim($fields[1]);
            $qty = trim($fields[3]);
            if (!$qty)
                $qty = 0;

            //process
            $productId = mage::getModel('catalog/product')->getIdBySku($sku);

            if (!$productId)
                $debug .= '<font color="red">Sku "' . $sku . '" does not exist (id=' . $productId . ')</font><br>';
            else {
                $stockLevelAtDate = 0;
                $stockItem = $this->loadByProductWarehouse($productId, $warehouseId);
                if ($stockItem) {

                    $stockLevelAtDate = $stockItem->getQtyFromStockMovement($date);
                    if (!$stockLevelAtDate)
                        $stockLevelAtDate = 0;
                }

                //if stocks are different, create stock movement
                if ($stockLevelAtDate != $qty) {
                    $debug .= '<font color="black">Stock level for Sku=' . $sku . ' changed from ' . $stockLevelAtDate . ' to ' . $qty . '</font><br>';
                    $diff = $qty - $stockLevelAtDate;
                    if ($diff > 0) {
                        $sourceWarehouseId = null;
                        $targetWarehouseId = $warehouseId;
                    } else {
                        $sourceWarehouseId = $warehouseId;
                        $targetWarehouseId = null;
                        $diff = - $diff;
                    }

                    //create stock movement
                    $additionalDatas = array('sm_date' => $date, 'sm_type' => 'adjustment');
                    mage::getModel('AdvancedStock/StockMovement')->createStockMovement($productId, $sourceWarehouseId, $targetWarehouseId, $diff, $smCaption, $additionalDatas
                    );
                }
            }

            $i++;
        }

        $debug = '<p><b>' . $i . ' lines processed</b></p>' . $debug;

        return $debug;
    }

    /**
     *
     * @param type $productId
     * @param type $warehouseId
     * @return null 
     */
    private function loadByProductWarehouse($productId, $warehouseId) {
        $item = mage::getModel('cataloginventory/stock_item')->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('stock_id', $warehouseId)
                ->getFirstItem();
        if ($item->getId())
            return $item;
        else
            return null;
    }

}