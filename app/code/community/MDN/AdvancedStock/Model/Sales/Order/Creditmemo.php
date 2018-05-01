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
class MDN_AdvancedStock_Model_Sales_Order_Creditmemo extends Tatva_Attachpdf_Model_Sales_Order_Creditmemo {

    /**
     * Unreserve qty for evry product of a credit memo
     *  - instantly at order level
     *  - plan unreservation at product level using background task
     *
     */
    protected function _afterSave() {
        parent::_afterSave();

        //browse products of the credit memo
        foreach ($this->getAllItems() as $item) {

            //unreserve products on the order
            $orderItem = $item->getOrderItem();
            $oldReservedQty = $orderItem->getreserved_qty();
            $newReservedQty = $oldReservedQty - $item->getqty();
            if ($newReservedQty < 0){
                $newReservedQty = 0;
            }

            //update reserved qty at order level
            $orderItem->getErpOrderItem()->setreserved_qty($newReservedQty)->save();

            //plan stock updates to adjust reserved qty at product level
            mage::helper('AdvancedStock/Product_Base')->planUpdateStocksWithBackgroundTask($item->getProductId(), 'from credit memo aftersave');
        }

        //dispatch event to allow other extension to catch creditmemo after save event
        Mage::dispatchEvent('advancedstock_creditmemo_aftersave', array('creditmemo' => $this));
    }

}