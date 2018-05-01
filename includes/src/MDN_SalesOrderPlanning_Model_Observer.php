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
class MDN_SalesOrderPlanning_Model_Observer {

    /**
     * Parse plannings to update and plan background tasks
     *
     */
    public function UpdatePlannings() {
        $collection = mage::getModel('SalesOrderPlanning/Planning')
                        ->getCollection()
                        ->addFieldToFilter('psop_need_update', 1);
        foreach ($collection as $item) {
            $planningId = $item->getId();
            mage::helper('BackgroundTask')->AddTask('Update planning #' . $planningId,
                    'SalesOrderPlanning/Planning',
                    'updatePlanning',
                    $planningId
            );
        }
    }

    /**
     * Store anounces date
     *
     * @param Varien_Event_Observer $observer
     */
    public function sales_convert_quote_to_order(Varien_Event_Observer $observer) {

        $order = $observer->getEvent()->getorder();
        $quote = $observer->getEvent()->getquote();

        if ($order && $quote) {
            $order->setanounced_date($quote->getanounced_date());
            $order->setanounced_date_max($quote->getanounced_date_max());
        }
    }

    /**
     * Fast product availability status after order is placed
     * @param Varien_Event_Observer $observer
     */
    public function sales_order_afterPlace(Varien_Event_Observer $observer) {

        $order = $observer->getEvent()->getOrder();
        $orderId = $order->getId();

        $start = time();
        foreach ($order->getAllItems() as $item) {
            $productId = $item->getproduct_id();
            $storeId = 0;
            $productAvailability = Mage::getModel('SalesOrderPlanning/ProductAvailabilityStatus')->load($productId, 'pa_product_id');
            if ($productAvailability->getId())
            {
                $orderedQty = $item->getRemainToShipQty();
                $productAvailability->fastUpdate(-$orderedQty);
            }
        }
        $stop = time();
        Mage::log('Duration for fast PAS update after order : '.($stop - $start).' ms');

    }


    /**
     * called on product duplication
     * @param Varien_Event_Observer $observer
     */
    public function catalog_model_product_duplicate(Varien_Event_Observer $observer) {
        $newProduct = $observer->getEvent()->getnew_product();

        //reset out of stock period
        $newProduct->setoutofstock_period_enabled(0);
        $newProduct->setoutofstock_period_from();
        $newProduct->setoutofstock_period_to();
    }

    /**
     * Update availability status for product with out of stock period
     *
     */
    public function RefreshForProductWithOutOfStockPeriod() {
        mage::helper('SalesOrderPlanning/ProductAvailabilityStatus')->RefreshForProductWithOutOfStockPeriod();
    }
    
    /**
     * Delete relative data when a product is deleting in magento
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalog_product_delete_before(Varien_Event_Observer $observer) {
        $product = $observer->getEvent()->getProduct();
        $productId = $product->getId();        
        if($productId>0){
            //Delete Product Availability Status            
            $paCollection = mage::getModel('SalesOrderPlanning/ProductAvailabilityStatus')
                        ->getCollection()
                        ->addFieldToFilter('pa_product_id', $productId);
            foreach ($paCollection as $pa){
                $pa->delete();
            }
        }
    }

}