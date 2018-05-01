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
class MDN_AdvancedStock_Helper_Sales_History extends Mage_Core_Helper_Abstract {

    /**
     * Launch update for all products
     */
    public function updateForAllProducts() {
        //create group task
        $taskGroup = 'refresh_sales_history';
        mage::helper('BackgroundTask')->AddGroup($taskGroup,
                mage::helper('SalesOrderPlanning')->__('Refresh Sales History'),
                'adminhtml/system_config/edit/section/advancedstock');

        //get product ids
        $productIds = mage::helper('AdvancedStock/Product_Base')->getProductIds();
        foreach ($productIds as $productId) {
            mage::helper('BackgroundTask')->AddTask('Update sales history for product #' . $productId,
                    'AdvancedStock/Sales_History',
                    'RefreshForOneProduct',
                    $productId,
                    $taskGroup
            );
        }

        //execute task group
        mage::helper('BackgroundTask')->ExecuteTaskGroup($taskGroup);
    }

    /**
     * Launch update for all products
     */
    public function scheduleUpdateForAllProducts() {

        //get product ids
        $productIds = mage::helper('AdvancedStock/Product_Base')->getProductIds();
        foreach ($productIds as $productId) {
            mage::helper('BackgroundTask')->AddTask('Update sales history for product #' . $productId,
                    'AdvancedStock/Sales_History',
                    'RefreshForOneProduct',
                    $productId
            );
        }
    }

    /**
     * Refresh one sale history
     */
    public function RefreshForOneProduct($productId) {
        
        //Avoid Multiple entries on each refresh
        $collection = mage::getModel('AdvancedStock/SalesHistory')
                      ->getCollection()
                      ->addFieldToFilter('sh_product_id', $productId);
        
        foreach($collection as $saleHistoryEntry){
          $saleHistoryEntry->delete();
        }

        $obj = mage::getModel('AdvancedStock/SalesHistory');
        $obj->setsh_product_id($productId);
        $obj->refresh();

        return $obj;
    }

    /**
     * Return sales history for 1 product
     */
    public function getForOneProduct($productId, $createIfNotExist = false) {
        $model = mage::getModel('AdvancedStock/SalesHistory')->load($productId, 'sh_product_id');
        if (!$model->getId() && $createIfNotExist) {
            $model = $this->RefreshForOneProduct($productId);
        }
        return $model;
    }

    /**
     * Return ranges for periods
     */
    public function getRanges() {
        $ranges = array();

        for ($i = 1; $i <= 3; $i++)
            $ranges[] = mage::getStoreConfig('advancedstock/sales_history/period_' . $i);

        return $ranges;
    }

}