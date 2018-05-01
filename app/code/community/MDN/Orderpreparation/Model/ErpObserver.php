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
class MDN_Orderpreparation_Model_ErpObserver {

    /**
     * Dispatch order when preparaiton warehouse change for order item
     * @param Varien_Event_Observer $observer
     */
    public function advancedstock_order_item_preparation_warehouse_changed(Varien_Event_Observer $observer)
    {
        $orderItem = $observer->getEvent()->getorder_item();
        $orderId = $orderItem->getorder_id();
        $this->dispatchOrder($orderId);
    }

    /**
     * Handle reserved qty changes on order item
     * @param Varien_Event_Observer $observer
     */
    public function advancedstock_order_item_reserved_qty_changed(Varien_Event_Observer $observer)
    {
        $orderItem = $observer->getEvent()->getorder_item();
        $orderId = $orderItem->getorder_id();
        $this->dispatchOrder($orderId);
    }

    /**
     *
     *
     */
    public function salesorder_aftersave(Varien_Event_Observer $observer) {
        $order = $observer->getEvent()->getorder();

        //dispatch order if validity change
        if ($order->getis_valid() != $order->getOrigData('is_valid')) {
            $orderId = $order->getId();
            $this->dispatchOrder($orderId);
            return;
        }

        //dispatch order if payment change
        if ($order->getpayment_validated() != $order->getOrigData('payment_validated')) {
            $orderId = $order->getId();
            $this->dispatchOrder($orderId);
            return;
        }
    }

    /**
     * Dispatch order if reserved qty change or warehouse change
     *
     * @param Varien_Event_Observer $observer
     */
    public function salesorderitem_aftersave(Varien_Event_Observer $observer) {
        $salesOrderItem = $observer->getEvent()->getsalesorderitem();

        //dispatch order if reserved qty change
        $reservedQtyAfterSave = $salesOrderItem->getreserved_qty();
        $reservedQtyBeforeSave = $salesOrderItem->getOrigData('reserved_qty');
        if ($reservedQtyAfterSave != $reservedQtyBeforeSave) {
            $orderId = $salesOrderItem->getorder_id();
            $this->dispatchOrder($orderId);
            return true;
        }

    }

    /**
     * Dispatch order when cancelled
     *
     * @param Varien_Event_Observer $observer
     */
    public function salesorder_just_cancelled(Varien_Event_Observer $observer) {
        $order = $observer->getEvent()->getorder();
        $orderId = $order->getId();

        //plan dispatch order
        $this->dispatchOrder($orderId);
    }

    /**
     * Plan dispatch order task
     * @param <type> $orderId
     */
    protected function dispatchOrder($orderId) {
        mage::helper('BackgroundTask')->AddTask('Dispatch order #' . $orderId,
                'Orderpreparation',
                'dispatchOrder',
                $orderId,
                null,
                true
        );
    }

}