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
class MDN_Orderpreparation_Helper_Data extends Mage_Core_Helper_Abstract {

    private $_preparationWarehouseSessionKey = 'op_preparation_warehouse';
    private $_operatorSessionKey = 'op_operator';

    /**
     * Notify Shipment
     *
     * @param unknown_type $shipmentId
     */
    public function notifyShipment($shipmentId) {
        $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
        if ($shipment->getId()) {
            if (!$shipment->getEmailSent()) {
                $shipment->sendEmail(true);
                $shipment->setEmailSent(true)->save();
            }
        }
    }

    /**
     * Notify Invoice
     *
     * @param unknown_type $invoiceId
     */
    public function notifyInvoice($invoiceId) {
        $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
        if ($invoice->getId()) {
            if (!$invoice->getEmailSent()) {

                $invoice->sendEmail(true);
                $invoice->setEmailSent(true)->save();
            }
        }
    }

    /**
     * Add an order to selected orders
     *
     * @param unknown_type $orderId
     */
    public function addToSelectedOrders($orderId) {
        //Charge le num�ro de commande � partir du no de l'enregistrement dans le cache
        $RealOrderId = mage::getModel('Orderpreparation/ordertopreparepending')
                        ->load($orderId)
                        ->getopp_order_id();
        Mage::getModel('Orderpreparation/ordertoprepare')->AddSelectedOrder($RealOrderId);
    }

    /**
     * Create invoice & shipment for an order
     *
     * @param unknown_type $orderToPrepareId
     */
    public function createShipmentAndInvoices($orderId) {
        $preparationWarehouseId = mage::helper('Orderpreparation')->getPreparationWarehouse();
        $operatorId = mage::helper('Orderpreparation')->getOperator();

        //Load order to prepare
        $error = '';
        $order = mage::getModel('sales/order')->load($orderId);
        $OrderToPrepare = $this->getOrderToPrepareForCurrentContext($orderId);

        //if order cancelled, return false Mage_Sales_Model_Order::STATE_CANCELED
        if ($order->getstate() == 'canceled')
            return false;

        //si la commande n'a pas de shipment on la traite
        if (mage::getStoreConfig('orderpreparation/create_shipment_and_invoices_options/create_shipment') == 1) {
            if (!Mage::helper('Orderpreparation/Shipment')->ShipmentCreatedForOrder($order->getid(), $preparationWarehouseId, $operatorId)) {
                try {
                    if ($order->canShip()) {
                        $preparationWarehouseId = mage::helper('Orderpreparation')->getPreparationWarehouse();
                        $operatorId = mage::helper('Orderpreparation')->getOperator();
                        Mage::helper('Orderpreparation/Shipment')->CreateShipment($order, $preparationWarehouseId, $operatorId);
                    }
                } catch (Exception $ex) {
                    $error .= 'Error creating Shipment : ' . "\n" . $ex->getMessage();
                }
            }
        }
        
        //si la commande n'a pas de facture, on la traite
        if (mage::getStoreConfig('orderpreparation/create_shipment_and_invoices_options/create_invoice') == 1) {
            if (!Mage::helper('Orderpreparation/Invoice')->InvoiceCreatedForOrder($order->getid())) {
                try {
                    Mage::helper('Orderpreparation/Invoice')->CreateInvoice($order);
                } catch (Exception $ex) {
                    $error .= 'Error creating invoice : ' . "\n" . $ex->getMessage();
                }
            }
        }

        //Upda order to prepare cache details
        $OrderToPrepare = mage::getModel('Orderpreparation/ordertoprepare')->load($orderId, 'order_id');
        $OrderToPrepare->setdetails(Mage::getModel('Orderpreparation/ordertoprepare')->getDetailsForOrder($order))->save();

        //raise error if exists
        if ($error != '')
            throw new Exception($error);
    }

    /**
     * Dispatch pending order to fullstock or stockless tab
     *
     * @param unknown_type $orderId
     */
    public function dispatchOrder($orderId) {
        $order = mage::getModel('sales/order')->load($orderId);
        mage::getmodel('Orderpreparation/ordertoprepare')->DispatchOrder($order);
    }

    /**
     * D�finit si une commande est en cours de pr�paration
     * Si oui, retourne l'objet
     * Si non, retourne false
     *
     * @param unknown_type $order
     */
    public function orderIsBeingPrepared($order) {
        $obj = mage::getModel('Orderpreparation/ordertoprepare')->load($order->getId(), 'order_id');
        if ($obj->getId())
            return $obj;
        else
            return false;
    }

    /**
     * Return preparation warehouse (for current user)
     */
    public function getPreparationWarehouse() {
        $session = Mage::getSingleton('adminhtml/session');
        $warehouseId = $session->getData($this->_preparationWarehouseSessionKey);

        //if not set, force to first preparation warehouse
        if (!$warehouseId) {
            $warehouse = mage::helper('AdvancedStock/Warehouse')
                            ->getWarehousesForPreparation()
                            ->getFirstItem();
            $this->setPreparationWarehouse($warehouse->getId());
            $warehouseId = $warehouse->getId();
        }

        return $warehouseId;
    }

    /**
     * Set preparation warehouse for current user
     */
    public function setPreparationWarehouse($warehouseId) {
        $session = Mage::getSingleton('adminhtml/session');
        $session->setData($this->_preparationWarehouseSessionKey, $warehouseId);
    }

    /**
     * Return operator
     */
    public function getOperator() {
        $operatorId = 1;

        if (!mage::getStoreConfig('orderpreparation/misc/single_user_mode')) {
          $session = Mage::getSingleton('adminhtml/session');
          $operatorId = $session->getData($this->_operatorSessionKey);

          if (!$operatorId) {
              $operatorId = Mage::getSingleton('admin/session')->getUser()->getId();
              $this->setOperator($operatorId);
          }
        }

        return $operatorId;
    }

    /**
     * Set operator id
     */
    public function setOperator($userId) {
        $session = Mage::getSingleton('adminhtml/session');
        $session->setData($this->_operatorSessionKey, $userId);
    }

    /**
     * return order to prepare from order id using context (ie: preparation warehouse & operator)
     */
    public function getOrderToPrepareForCurrentContext($orderId) {
        $preparationWarehouseId = mage::helper('Orderpreparation')->getPreparationWarehouse();
        $operatorId = mage::helper('Orderpreparation')->getOperator();
        $object = mage::getModel('Orderpreparation/ordertoprepare')
                        ->getCollection()
                        ->addFieldToFilter('preparation_warehouse', $preparationWarehouseId)
                        ->addFieldToFilter('order_id', $orderId)
                        ->addFieldToFilter('user', $operatorId)
                        ->getFirstItem();
        return $object;
    }

}

?>