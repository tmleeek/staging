<?php
class TBT_RewardsReferral_Model_Observer_Sales_Order_Transfer_Observer extends TBT_Rewards_Model_Sales_Order_Transfer_Adjuster
{
    const TRANSFER_TYPE_AFFILIATE_EARNING = 'affiliate';

    /**
     * Observers event 'rewards_adjust_points_init_before' and sets adjusted points
     * earned by the affiliate on the Adjuster singleton
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function beforeInit($observer)
    {
        $adjuster = $observer->getEvent()->getAdjuster();
        $params = Mage::app()->getFrontController()->getAction()->getRequest()->getParams();
        $adjustedAffiliateEarned = $this->_getFromParams($params, 'adjustment_affiliate_points_earned');

        $adjuster->setAdjustedAffiliateEarned($adjustedAffiliateEarned);

        return $this;
    }

    /**
     * Observes event 'rewards_sales_order_transfer_ajuster_init' and prepares
     * all the affiliate transfers from an order for adjustment.
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function initAffiliate($observer)
    {
        $adjuster = $observer->getEvent()->getAdjuster();

        if (!$adjuster) {
            return $this;
        }
        $this->setData($adjuster->getData());

        $this->setTransfers($this->_initAffiliateTransfers($adjuster->getTransfers()));
        $this->setOrigPoints($this->_initAffiliatePointsSum($adjuster->getOrigPoints()));

        $orderId = $adjuster->getOrder()->getId();

        $affiliateTransferReferences = Mage::getResourceModel( 'rewardsref/referral_order_transfer_reference_collection' )
                ->addTransferInfo()
                ->filterAssociatedWithOrder($orderId);


        foreach ($affiliateTransferReferences as $affiliateReference) {
            $affiliateTransfer = Mage::getModel('rewardsref/transfer')->load($affiliateReference->getRewardsTransferId());

            if ($affiliateTransfer->getStatus() == self::TRANSFER_STATUS_CANCELLED) {
                continue;
            }

            if ($affiliateTransfer->getQuantity() > 0) {
                $this->addToOrigAffiliateEarned($affiliateTransfer->getQuantity());
                $this->addAffiliateEarnedTransfer($affiliateTransfer);
            }
        }

        $adjuster->setData($this->getData());

        // if Adjuster was not yet initialized, we'll do it here
        if (!$this->_isInitialized) {
            $this->_isInitialized = true;
        }

        return $this;
    }

    /**
     * Observes event 'rewards_sales_order_transfer_ajuster_execute' and adds
     * logic to do the same thing as we do in TBT_Rewards_Model_Sales_Order_Transfer_Adjuster::execute()
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function executeAffiliate($observer)
    {
        $adjuster = $observer->getEvent()->getAdjuster();

        if (!$adjuster) {
            return $this;
        }
        $this->setData($adjuster->getData());

        $affiliateId = $this->_getAffiliateId();

        if (!$affiliateId) {
            return $this;
        }

        // setting a comment for new transfers to reflect that an order for a referred customer was canceled
        $this->setTransferComments($this->__("Points adjustment made during the process of cancelling an order for a reffered customer."));

        // if admin specified an affiliate earning value and it is different from the original, process it
        if ($this->getAdjustedAffiliateEarned() !== null && $this->getAdjustedAffiliateEarned() != $this->getOrigAffiliateEarned()) {
            $this->_adjustOrderPoints(self::TRANSFER_TYPE_AFFILIATE_EARNING, $this->getAdjustedAffiliateEarned(), $affiliateId);
        }

        $adjuster->setData($this->getData());

        return $this;
    }

    /**
     * Creates a brand new affiliate order transfer and associates it with an order.
     * @param int $points The amount of points for the transfer
     * @param int $currencyId The currency of the transfer
     * @param int $status The status of the new transfer (usually Approved or Pending)
     * @param int $customerId The customer ID to whom to give the transfer, in this case, the affiliate customer ID
     * @param int $orderId The order ID to which the transfer should be associated
     * @param string $comments The comments for the new transfer
     * @return self
     */
    protected function _makeAdjustmentTransfer($points, $currencyId, $status, $customerId, $orderId, $comments = "Points adjustment")
    {
        $affiliateId = $customerId;
        // the $customerID is the referred customer's ID
        $customerId = $this->getOrder()->getCustomerId();

        $reasonId = TBT_RewardsReferral_Model_Transfer_Reason_Order::REASON_TYPE_ID;

        $newTransfer = Mage::getModel('rewardsref/transfer')->create($points, $currencyId, $affiliateId, $customerId, $comments, $reasonId, $status, $orderId);

        if (!$newTransfer) {
            Mage::throwException(
                $this->__("Failed to adjust points on this order.  Please contact Sweet Tooth support.")
            );
        }

        if (!$newTransfer->setStatus(null, $status)) {
            Mage::throwException(
                $this->__("Failed to adjust points on this order.  Please contact Sweet Tooth support.")
            );
        }

        return $this;
    }

    /**
     * Retrieves affiliate customer ID from the affiliate transfers data.
     * Because an order can have only one affiliate, we'll stop when one found.
     * @return int The customer ID of the affiliate
     */
    protected function _getAffiliateId()
    {
        $affiliateId = null;
        $affiliateTransfers = $this->getTransfers(self::TRANSFER_TYPE_AFFILIATE_EARNING);
        foreach ($affiliateTransfers as $transferStatus => $transfers) {
            foreach ($transfers as $transfer) {
                if (!$transfer) {
                    continue;
                }

                $affiliateId = $transfer->getCustomerId();
                break;
            }
        }

        return $affiliateId;
    }

    /**
     * Performs a bunch of checks when getting our values out of the controller params,
     * so it doesn't muck up the primary code up above.
     * @param array $params
     * @param string $key
     * @return mixed
     */
    protected function _getFromParams($params, $key)
    {
        if (!isset($params['creditmemo'])) {
            return null;
        }

        if (!is_array($params['creditmemo'])) {
            return null;
        }

        if (!array_key_exists($key, $params['creditmemo'])) {
            return null;
        }

        return $params['creditmemo'][$key];
    }

    /**
     * Initializes the array we use to store the affiliate transfers in the Adjuster singleton.
     * Same as we do in TBT_Rewards_Model_Sales_Order_Transfer_Adjuster::_initTransfersArray(),
     * but for the affiliate transfers
     * @return array
     */
    protected function _initAffiliateTransfers($transfers)
    {
        $transfers[self::TRANSFER_TYPE_AFFILIATE_EARNING] = $this->_getTransferStatuses();
        return $transfers;
    }

    /**
     * Initializes a simple array used to store the total sum of points on the
     * original order earned by a affiliate. Same as we do in
     * TBT_Rewards_Model_Sales_Order_Transfer_Adjuster::_initPointsSumArray(),
     * but for the affiliate earned points
     * @return array
     */
    protected function _initAffiliatePointsSum($pointsSum)
    {
        $pointsSum[self::TRANSFER_TYPE_AFFILIATE_EARNING] = 0;
        return $pointsSum;
    }

    /**
     * Adds an integer value to the running sum of affiliate points earned on the order
     * @param int $quantity
     * @return $this
     */
    public function addToOrigAffiliateEarned($quantity)
    {
        return $this->addToOrigPoints(self::TRANSFER_TYPE_AFFILIATE_EARNING, $quantity);
    }

    /**
     * Adds a transfer to the list of affiliate distributions, grouped by status
     * @param TBT_Rewards_Model_Transfer $transfer
     * @return $this
     */
    public function addAffiliateEarnedTransfer($transfer)
    {
        return $this->addTransfer(self::TRANSFER_TYPE_AFFILIATE_EARNING, $transfer);
    }

    /**
     * Gets the simple sum of points earned on the order by the affiliate.
     * @return int
     */
    public function getOrigAffiliateEarned()
    {
        return $this->getOrigPoints(self::TRANSFER_TYPE_AFFILIATE_EARNING);
    }
}