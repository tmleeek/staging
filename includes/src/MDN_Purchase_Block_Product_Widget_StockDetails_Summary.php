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
class MDN_Purchase_Block_Product_Widget_StockDetails_Summary extends Mage_Adminhtml_Block_Template {

    private $_stocks = null;
    private $_supplyNeed = null;
    /**
     * Product get/set
     *
     * @var unknown_type
     */
    private $_product = null;
    public function setProduct($Product) {
        $this->_product = $Product;
        return $this;
    }

    public function getProduct() {
        return $this->_product;
    }

    public function getManualSupplyNeedQty() {
        $retour = $this->getProduct()->getmanual_supply_need_qty();
        if ($retour == '')
            $retour = 0;
        return $retour;
    }

    /**
     * Return waiting for delivery qty
     *
     */
    public function getWaitingForDeliveryQty() {
        return $this->getProduct()->getwaiting_for_delivery_qty();
    }

    /**
     * Return stocks for product
     *
     * @return unknown
     */
    private function getStocks() {
        if ($this->_stocks == null) {
            $this->_stocks = mage::helper('AdvancedStock/Product_Base')->getStocks($this->getProduct()->getId());
        }
        return $this->_stocks;
    }

    public function getGeneralStatus() {
        return $this->__($this->getSupplyNeed()->getstatus());
    }

    private function getSupplyNeed() {
        if ($this->_supplyNeed == null) {
            $this->_supplyNeed = mage::getModel('Purchase/SupplyNeeds')->load($this->getProduct()->getId());
        }
        return $this->_supplyNeed;
    }

    public function getSupplyNeedMinQty() {
        $sn = $this->getSupplyNeed();
        return (int)$sn->getqty_min();
    }

    public function getSupplyNeedMaxQty() {
        $sn = $this->getSupplyNeed();
        return (int)$sn->getqty_max();
    }

}