<?php

class TBT_Milestone_Model_Rule_Condition_Orders extends TBT_Milestone_Model_Rule_Condition
{
    // @deprecated use TBT_Milestone_Model_Rule_Condition_Orders_Reference::REFERENCE_TYPE_ID
    const POINTS_REFERENCE_TYPE_ID = 601;

    public function getMilestoneName()
    {
        return Mage::helper('tbtmilestone')->__("Number of Orders Milestone");
    }

    public function getMilestoneDescription()
    {
        if (intval($this->getThreshold() == 1)){
            return Mage::helper('tbtmilestone')->__("milestone for placing %s order", $this->getThreshold());

        }  else {
            return Mage::helper('tbtmilestone')->__("milestone for placing %s orders", $this->getThreshold());
        }
    }

    public function isSatisfied($customerId)
    {
        $threshold = intval($this->getThreshold());
        $fromDate = $this->getFromDate();
        $toDate = $this->getToDate();
        $storeIds = $this->_getHelper()->getStoreIdsFromWebsites($this->getRule()->getWebsiteIds());

        $ordersBeforeStartDate = Mage::getModel('sales/order')->getCollection()
                                    ->addFieldToFilter('main_table.customer_id', $customerId)
                                    ->addFieldToFilter('main_table.store_id',   array("in" => $storeIds))
                                    ->addFieldToFilter('main_table.created_at', array("lt" => $fromDate));


        $ordersAfterStartDate = Mage::getModel('sales/order')->getCollection()
                                    ->addFieldToFilter('main_table.customer_id', $customerId)
                                    ->addFieldToFilter('main_table.store_id',   array("in" => $storeIds))
                                    ->addFieldToFilter('main_table.created_at', array("gteq" => $fromDate));
        if (!empty($toDate)){
            $ordersAfterStartDate->addFieldToFilter("main_table.created_at", array("lt" => $toDate));
        }

        $this->_addCountingConstraints($ordersBeforeStartDate);
        $this->_addCountingConstraints($ordersAfterStartDate);

        $countBeforeStartDate = $ordersBeforeStartDate->getSize();
        $countAfterStartDate = $ordersAfterStartDate->getSize();
        $countTotal = $countBeforeStartDate + $countAfterStartDate;

        return ( $countBeforeStartDate < $threshold && $countTotal >= $threshold );
    }

    /**
     * Accepts a Sales Order Collection and places count restrictions on it based on config settings
     * Aka. What should we count as an order?
     *
     * @param Mage_Sales_Model_Mysql4_Order_Collection $collection
     * @return Mage_Sales_Model_Mysql4_Order_Collection $collection. The same collection, just modified.
     */
    protected function _addCountingConstraints(&$collection)
    {
        $orderCountTrigger = $this->_getHelper('config')->getOrdersTrigger();
        switch ($orderCountTrigger){
            case "payment":
                // Count everything that has an invoice
                $collection->getSelect()->join(
                                               array("invoice" => $this->_getInvoiceTableName()),
                                               "main_table.entity_id = invoice.order_id"
                                              );
                break;

            case "shipment":
                // Count everything that has a shipment
                $collection->getSelect()->join(
                                               array("shipment" => $this->_getShipmentTableName()),
                                               "main_table.entity_id = shipment.order_id"
                                              );
                break;

            case "create":
                // Notuing specific
            default:
                break;
        }

        // Make sure we're always looking at orders which are not canceled
        $collection->addFieldToFilter('main_table.state',
                array("nin" => array(
                        Mage_Sales_Model_Order::STATE_CANCELED
                )));

        return $collection;
    }

    /**
     * Get the table name for the sales/invoice table
     * @return string
     */
    protected function _getInvoiceTableName()
    {
        if (!isset($this->_invoiceTable)){
            $this->_invoiceTable = Mage::getSingleton('core/resource')->getTableName('sales/invoice');
        }

        return $this->_invoiceTable;
    }

    /**
     * Get the table name for the sales/shipment table
     * @return string
     */
    protected function _getShipmentTableName()
    {
        if (!isset($this->_shipmentTable)){
            $this->_shipmentTable = Mage::getSingleton('core/resource')->getTableName('sales/shipment');
        }

        return $this->_shipmentTable;
    }

    public function validateSave()
    {
        if (!$this->getThreshold()) {
            throw new Exception("The milestone threshold is a required field.");
        }

        return $this;
    }

    /**
     * @return int. The Transfer Refrence Type ID used to identify this type of rule.
     * @see TBT_Milestone_Model_Rule_Condition::getPointsReferenceTypeId()
     */
    public function getPointsReferenceTypeId()
    {
        return TBT_Milestone_Model_Rule_Condition_Orders_Reference::REFERENCE_TYPE_ID;
    }
}
