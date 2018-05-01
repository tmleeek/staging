<?php

class TBT_Milestone_Model_Rule_Condition_Revenue extends TBT_Milestone_Model_Rule_Condition
{
    // @deprecated check TBT_Milestone_Model_Rule_Condition_Revenue_Reference::REFERENCE_TYPE_ID
    const POINTS_REFERENCE_TYPE_ID = 605;

    public function getMilestoneName()
    {
        return Mage::helper('tbtmilestone')->__("Revenue Milestone");
    }

    public function getMilestoneDescription()
    {
        $threshold = Mage::app()->getStore()->getBaseCurrency()->format($this->getThreshold(), array(), false);
        return Mage::helper('tbtmilestone')->__("milestone for reaching %s in revenue", $threshold);
    }

    public function isSatisfied($customerId)
    {
        $storeIds = $this->_getHelper()->getStoreIdsFromWebsites($this->getRule()->getWebsiteIds());

        $invoiceCollectionBeforeStart = Mage::getResourceModel('sales/order_invoice_collection')
            ->addFieldToFilter('main_table.store_id', array('in' => $storeIds))
            ->addFieldToFilter('main_table.state', Mage_Sales_Model_Order_Invoice::STATE_PAID)
            ->addFieldToFilter('main_table.created_at', array('lt' => $this->getFromDate()));
        $invoiceCollectionBeforeStart->getSelect()->join(
            array('order_table' => Mage::getSingleton('core/resource')->getTableName('sales/order')),
            "main_table.order_id = order_table.entity_id",
            array('order_table.customer_id')
        );
        $invoiceCollectionBeforeStart->addFieldToFilter('order_table.customer_id', $customerId);

        $invoiceCollectionAfterStart = Mage::getResourceModel('sales/order_invoice_collection')
            ->addFieldToFilter('main_table.store_id', array('in' => $storeIds))
            ->addFieldToFilter('main_table.state', Mage_Sales_Model_Order_Invoice::STATE_PAID)
            ->addFieldToFilter('main_table.created_at', array('gteq' => $this->getFromDate()));
        $invoiceCollectionAfterStart->getSelect()->join(
            array('order_table' => Mage::getSingleton('core/resource')->getTableName('sales/order')),
            "main_table.order_id = order_table.entity_id",
            array('order_table.customer_id')
        );
        $invoiceCollectionAfterStart->addFieldToFilter('order_table.customer_id', $customerId);

        if ($this->getToDate()) {
            $invoiceCollectionAfterStart->addFieldToFilter('main_table.created_at', array('lt' => $this->getToDate()));
        }

        $totalRevenueBeforeStart = $this->_fetchRevenue($invoiceCollectionBeforeStart);
        $totalRevenueAfterStart = $this->_fetchRevenue($invoiceCollectionAfterStart);

        $totalRevenue = $totalRevenueBeforeStart + $totalRevenueAfterStart;

        // Convert currency amounts to integers to circumvent any ugly floating-point headaches.
        $totalRevenueBeforeStart = (int) round($totalRevenueBeforeStart * 4, 0);
        $totalRevenue = (int) round($totalRevenue * 4, 0);
        $threshold = (int) round($this->getThreshold() * 4, 0);

        return $totalRevenueBeforeStart < $threshold && $totalRevenue >= $threshold;
    }

    public function validateSave()
    {
        if (!$this->getThreshold()) {
            throw new Exception("Revenue amount is a required field.");
        }

        return $this;
    }

    /**
     * @see TBT_Milestone_Model_Rule_Condition::getPointsReferenceTypeId()
     */
    public function getPointsReferenceTypeId()
    {
        return TBT_Milestone_Model_Rule_Condition_Revenue_Reference::REFERENCE_TYPE_ID;
    }

    protected function _fetchRevenue($collection)
    {
        $collection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS);

        $collection->getSelect()
            ->group('order_table.customer_id');

        $collection->addExpressionFieldToSelect('total_revenue', "SUM(main_table.base_grand_total)", array());

        return $collection->getFirstItem()->getData('total_revenue');
    }
}
