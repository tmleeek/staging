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
 * @author     : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_ProductReturn_Helper_Stock extends Mage_Core_Helper_Abstract
{

    /**
     * Product back in stock
     * This helper is designed to be rewritten per stock management extension
     *
     * @param unknown_type $productId
     * @param unknown_type $qty
     * @param              $destination
     * @param              $websiteId
     * @param              $description
     * @param null         $rmaId
     */
    public function productBackInStock($productId, $qty, $destination, $websiteId, $description, $rmaId = null)
    {
        switch ($destination) {
            case MDN_ProductReturn_Model_RmaProducts::kDestinationCustomer:
                //nothing
                break;
            case MDN_ProductReturn_Model_RmaProducts::kDestinationDestroy:
                //nothing
                break;
            case MDN_ProductReturn_Model_RmaProducts::kDestinationStock:
                //increase product stock (magento way)
                $product = mage::getModel('catalog/product')->load($productId);
                // TODO : check if param config creditmemo != yes
                if ($product->getId()) {
                    $stockItem = $product->getStockItem();
                    if ($stockItem) {
                        $stockItem->setqty($stockItem->getqty() + $qty);
                        $stockItem->save();
                    }
                }
                break;
            case MDN_ProductReturn_Model_RmaProducts::kDestinationSupplier:
                if ($rmaId != null) {
                    $i   = 0;
                    $rma = Mage::getModel('ProductReturn/Rma')->load($rmaId);
                    while ($i < $qty) {
                        $rmaProduct = mage::getModel('ProductReturn/RmaProducts')->getCollection()->addFieldToFilter('rp_rma_id', $rmaId)->addFieldToFilter('rp_product_id', $productId)->getFirstItem();
                        mage::getModel('ProductReturn/SupplierReturn_Product')->CreateFromRmaProducts($rmaProduct);
                        $i++;
                    }
                    $rma->addHistoryRma('Create Supplier Return for the product ' . $productId . ' (qty: ' . $qty . ')');
                }
                break;
        }
    }

}