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
class MDN_AdvancedStock_Model_Sales_Order extends Tatva_Attachpdf_Model_Sales_Order {

    /**
     * Rewrite get items collection to join with the erp_sales_flat_order_item table
     * 
     * @param <type> $filterByTypes
     * @param <type> $nonChildrenOnly
     * @return <type> 
     */
    public function getItemsCollection($filterByTypes = array(), $nonChildrenOnly = false)
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getResourceModel('sales/order_item_collection')
                ->setOrderFilter($this);

            //join with erp_sales_flat_order_item
            $this->_items->joinErpTable();

            if ($filterByTypes) {
                $this->_items->filterByTypes($filterByTypes);
            }
            if ($nonChildrenOnly) {
                $this->_items->filterByParent();
            }

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setOrder($this);
                }
            }
        }
        
        /*$debug = "getItemsCollection =".$this->_items->getSelect();
        mage::log($debug, null, 'erp_router.log');*/
        
        return $this->_items;
    }

    /**
     * get total margin
     *
     */
    public function getMargin() {
        $retour = 0;
        foreach ($this->getAllVisibleItems() as $item) {
            $retour += $item->getMargin();
        }
        return $retour;
    }

    /**
     * get total margin in percent
     *
     */
    public function getMarginPercent() {
        if ($this->getsubtotal() > 0)
            return ($this->getMargin()) / $this->getsubtotal() * 100;
        else
            return 0;
    }

    /**
     * Return true if all product are reserved
     *
     * @return unknown
     */
    public function IsFullStock($warehouseId = null) {
        foreach ($this->getItemsCollection() as $item) {
            if (($warehouseId != null) && ($warehouseId != $item->getpreparation_warehouse()))
                continue;

            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($item->getproduct_id());
            if ($stockItem) {
                if ($stockItem->getManageStock()) {
                    $remaining_qty = $item->getRemainToShipQty();
                    if (($item->getreserved_qty() < $remaining_qty) && ($remaining_qty > 0)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Return true if all products are reserved
     *
     */
    public function allProductsAreReserved() {
        foreach ($this->getItemsCollection() as $item) {
            $product = mage::getModel('catalog/product')->load($item->getproduct_id());
            $manageStock = true;
            if ($product->getId())
                $manageStock = $product->getStockItem()->getManageStock();
            if ($manageStock) {
                $remaining_qty = $item->getRemainToShipQty() - $item->getreserved_qty();
                if ($remaining_qty > 0)
                    return false;
            }
        }

        return true;
    }

    /**
     * Return true if an order is completely shipped
     *
     */
    public function IsCompletelyShipped() {
        //recupere la liste des produits de la commande
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getRemainToShipQty() > 0)
                return false;
        }

        return true;
    }

    /**
     * 
     *
     */
    protected function _beforeSave() {
        parent::_beforeSave();

        //update order is valid
        mage::helper('AdvancedStock/Sales_ValidOrders')->updateIsValid($this);

        Mage::dispatchEvent('salesorder_beforesave', array('order' => $this));
    }

    /**
     * Reserve of Un reserve product if validity status change
     *
     * If order is cancelled, unrerved product
     *
     */
    protected function _afterSave() {
        parent::_afterSave();

        Mage::dispatchEvent('salesorder_aftersave', array('order' => $this));

        //if order just being created, exit
        if (!$this->getOrigData('entity_id'))
            return;

        //if order is_valid change, update stock information for products
        if ($this->getis_valid() != $this->getOrigData('is_valid')) {            
         
          foreach ($this->getAllItems() as $item) {

            //unreserve if necessary
            if (mage::getStoreConfig('advancedstock/valid_orders/do_not_consider_invalid_orders_for_stocks')){
                if(!mage::helper('AdvancedStock/Sales_ValidOrders')->orderIsValid($this)){              
                   mage::helper('AdvancedStock/Product_Reservation')->releaseProduct($this, $item);
                }                
            }

            //Will try to reserve in all case by backgroudn task
            mage::helper('AdvancedStock/Product_Base')->planUpdateStocksWithBackgroundTask($item->getproduct_id(),' from order validity change ');
          }
        }

        //if order has been canceled, update products stocks and reserved qties
        if ($this->getstate() != $this->getOrigData('state')) {
            if ($this->getstate() == Mage_Sales_Model_Order::STATE_CANCELED) {
                foreach ($this->getAllItems() as $item) {
                
                    //unreserve product
                    mage::helper('AdvancedStock/Product_Reservation')->releaseProduct($this, $item);

                    //plan product stocks update                                        
                    mage::helper('AdvancedStock/Product_Base')->planUpdateStocksWithBackgroundTask($item->getproduct_id(), 'from order is cancel event');

                    //raise custom event
                    Mage::dispatchEvent('salesorder_just_cancelled', array('order' => $this));
                }
            }
        }
    }

    /**
     * Define if ERP can reserve products for this order
     *
     */
    public function productReservationAllowed() {

        $reservationAllowed = true;
        if (mage::getStoreConfig('advancedstock/valid_orders/do_not_consider_invalid_orders_for_stocks')){
          if(!mage::helper('AdvancedStock/Sales_ValidOrders')->orderIsValid($this)){
            $reservationAllowed = false;
          }
        }
        return $reservationAllowed;
    }

    /**
     * Return order date (depending of magento version)
     *
     * @return unknown
     */
    public function getOrderPlaceDate() {
        $value = $this->getCreatedAtStoreDate();
        if ($value == '')
            $value = $this->getcreated_at();
        return $value;
    }

    /**
     * Return preparation warehouses depending of order item
     */
    public function getPreparationWarehouses() {
        $warehouseIds = array();
        foreach ($this->getAllItems() as $item) {
            if ($item->getpreparation_warehouse())
                $warehouseIds[] = $item->getpreparation_warehouse();
        }

        $collection = mage::getModel('AdvancedStock/Warehouse')
                        ->getCollection()
                        ->addFieldToFilter('stock_id', array('in' => $warehouseIds));
        return $collection;
    }

}

