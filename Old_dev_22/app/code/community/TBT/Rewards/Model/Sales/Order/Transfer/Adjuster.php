<?php

class TBT_Rewards_Model_Sales_Order_Transfer_Adjuster extends Varien_Object
{
    const TRANSFER_STATUS_APPROVED         = TBT_Rewards_Model_Transfer_Status::STATUS_APPROVED;
    const TRANSFER_STATUS_PENDING_APPROVAL = TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_APPROVAL;
    const TRANSFER_STATUS_PENDING_EVENT    = TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_EVENT;
    const TRANSFER_STATUS_PENDING_TIME     = TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_TIME;
    const TRANSFER_STATUS_CANCELLED        = TBT_Rewards_Model_Transfer_Status::STATUS_CANCELLED;

    const TRANSFER_TYPE_EARNING  = 'distribution';
    const TRANSFER_TYPE_SPENDING = 'redemption';

    protected $_isInitialized = false;

    protected function _construct()
    {
        parent::_construct();
        $this->setTransferComments("Points adjustment");

        return $this;
    }

    /**
     * Prepares all the transfers from an order for adjustment.
     * Checks the adjusted redemptions to ensure the customer can afford them before
     * allowing the process to continue.
     * @return self
     */
    public function init()
    {
        if (!$this->getOrder()) {
            Mage::throwException($this->__("The points adjuster requires an order to initialize."));
        }

        $orderId = $this->getOrder()->getId();

        $this->setTransfers($this->_initTransfersArray());
        $this->setOrigPoints($this->_initPointsSumArray());

        $currencyIds = Mage::getSingleton('rewards/currency')->getAvailCurrencyIds();
        $currencyId = $currencyIds[0];

        // sum up total distributions & redemptions; group transfers by type & status, sorted by quantity
        $transfers = Mage::getSingleton('rewards/transfer')->getTransfersAssociatedWithOrder($orderId)
            ->sortByAbsoluteQuantity();
        foreach ($transfers as $transfer) {
            if ($transfer->getStatus() == self::TRANSFER_STATUS_CANCELLED) {
                continue;
            }

            // TODO: should we also check if the approved transfer has already been revoked?
            //    maybe it would be smart to set an is_revoked flag on the transfer?

            if ($transfer->getQuantity() > 0) {
                $this->addToOrigEarned($transfer->getQuantity());
                $this->addEarnedTransfer($transfer);
            } else if ($transfer->getQuantity() < 0) {
                $this->addToOrigSpent(-$transfer->getQuantity());
                $this->addSpentTransfer($transfer);
            }
        }

        // dispatching this event so we can hook aditional logic in Referral module
        Mage::dispatchEvent('rewards_sales_order_transfer_ajuster_init',
            array('adjuster' => $this));

        // everything that follows is checking redemptions against customer balance, so if they weren't
        // adjusted, there's no point in continuing
        if ($this->getAdjustedSpent() === null || $this->getAdjustedSpent() <= $this->getOrigSpent()) {
            $this->_isInitialized = true;
            return $this;
        }

        $remainingPoints = $this->getAdjustedSpent() - $this->getOrigSpent();
        $customer = Mage::getModel('rewards/customer')->load($this->getOrder()->getCustomerId());
        if (!$customer->canAfford($remainingPoints, $currencyId)) {
            Mage::throwException(
                $this->__("Failed to adjust points on this order.  The customer has %s but Points Spent was increased by %s.",
                    Mage::getModel('rewards/points')->set($currencyId, $customer->getUsablePointsBalance($currencyId)),
                    Mage::getModel('rewards/points')->set($currencyId, $remainingPoints)
                )
            );
        }

        $this->_isInitialized = true;

        return $this;
    }

    /**
     * Compares the newly adjusted points values (earning and spending) against the existing values
     * from the order, and determines how to cancel/revoke/create transfers to most efficiently
     * reach the adjusted goal.
     * - Since it takes more overhead to revoke approved transfers than to cancel pending transfers,
     *   we give priority to cancellation of pending transfers.
     * - We cancel the bigger transfers first (if they don't fit within the adjusted goal) to
     *   leave room for the smaller transfers, which means hopefully we will cancel fewer transfers.
     * - Any newly-created transfers will be Approved if ALL transfers on this order were approved,
     *   otherwise they will be Pending.
     * @return self
     * @throws Mage_Core_Exception
     */
    public function execute()
    {
        if (!$this->_isInitialized) {
            Mage::throwException($this->__("The points adjuster must be initialized before it can be executed."));
        }

        if (!$this->getOrder()) {
            Mage::throwException($this->__("The points adjuster requires an order to execute."));
        }

        // if merchant specified an earning value and it is different from the original, process it
        if ($this->getAdjustedEarned() !== null && $this->getAdjustedEarned() != $this->getOrigEarned()) {
            $this->_adjustOrderPoints(self::TRANSFER_TYPE_EARNING, $this->getAdjustedEarned());
        }

        // if merchant specified a spending value and it is different from the original, process it
        if ($this->getAdjustedSpent() !== null && $this->getAdjustedSpent() != $this->getOrigSpent()) {
            $this->_adjustOrderPoints(self::TRANSFER_TYPE_SPENDING, $this->getAdjustedSpent());
        }

        // dispatching this event so we can hook aditional logic in Referral module
        Mage::dispatchEvent('rewards_sales_order_transfer_ajuster_execute',
            array('adjuster' => $this));

        // this can be used for any processing, after order has been canceled (we use it for re-indexing points balance)
        Mage::dispatchEvent('rewards_sales_order_transfer_ajuster_done',
            array('order'     => $this->getOrder()
                , 'transfers' => $this->getTransfers()
        ));

        return $this;
    }

    /**
     * Somewhat intelligently decides which transfers to cancel, which to revoke, and
     * whether or not it needs to create a new transfer, in order to reach a new
     * final-sum goal.
     * @param string $type const TRANSFER_TYPE_EARNING|TRANSFER_TYPE_SPENDING
     * @param int $adjustedPointsSum
     * @return self
     */
    protected function _adjustOrderPoints($type, $adjustedPointsSum, $customerId = null)
    {
        $transfers = $this->getTransfers($type);
        $orderId = $this->getOrder()->getId();

        if (! $customerId) {
             $customerId = $this->getOrder()->getCustomerId();
        }

        $currencyIds = Mage::getSingleton('rewards/currency')->getAvailCurrencyIds();
        $currencyId = $currencyIds[0];

        $comments = $this->getTransferComments();

        $hasPending = count($transfers[self::TRANSFER_STATUS_PENDING_APPROVAL]) > 0 ||
            count($transfers[self::TRANSFER_STATUS_PENDING_EVENT]) > 0 ||
            count($transfers[self::TRANSFER_STATUS_PENDING_TIME]) > 0;

        $approvedTransfers = $transfers[self::TRANSFER_STATUS_APPROVED];
        $pendingTransfers = array_merge($transfers[self::TRANSFER_STATUS_PENDING_APPROVAL],
            $transfers[self::TRANSFER_STATUS_PENDING_EVENT],
            $transfers[self::TRANSFER_STATUS_PENDING_TIME]);

        // we only have to revoke/cancel transfers if the new adjusted value is LESS than the original
        if ($adjustedPointsSum < $this->getOrigPoints($type)) {
            $remainingPoints = $adjustedPointsSum;
            $cancellationQueue = array();
            // we go through the approved transfers first so they're less likely to be revoked
            foreach ($approvedTransfers as $transfer) {
                if ($remainingPoints >= abs($transfer->getQuantity())) {
                    $remainingPoints -= abs($transfer->getQuantity());
                } else {
                    $cancellationQueue[] = $transfer;
                }
            }
            // we go through the pending transfers last because they're easier to cancel
            foreach ($pendingTransfers as $transfer) {
                if ($remainingPoints >= abs($transfer->getQuantity())) {
                    $remainingPoints -= abs($transfer->getQuantity());
                } else {
                    $cancellationQueue[] = $transfer;
                }
            }

            // cancel/revoke any transfers that didn't make the cut!
            foreach ($cancellationQueue as $transfer) {
                if ($transfer->getStatus() == self::TRANSFER_STATUS_APPROVED) {
                    $transfer->revoke();
                } else {
                    $transfer->setStatus(null, self::TRANSFER_STATUS_CANCELLED)
                        ->save();
                }
            }

            // no need for a new transfer to make up the difference, nice!
            if ($remainingPoints <= 0) {
                return $this;
            }

            if ($type == self::TRANSFER_TYPE_SPENDING) {
                $remainingPoints *= -1;
            }

            // only use the Approved status if ALL transfer were approved... otherwise use Pending
            $status = $hasPending ? self::TRANSFER_STATUS_PENDING_EVENT :
                self::TRANSFER_STATUS_APPROVED;

            // time to create a new transfer to make up the difference
            $this->_makeAdjustmentTransfer($remainingPoints, $currencyId, $status, $customerId, $orderId, $comments);

            return $this;
        }

        // since the adjusted value is MORE than the original, all we have to do is create a new transfer
        if ($adjustedPointsSum > $this->getOrigPoints($type)) {
            $remainingPoints = $adjustedPointsSum - $this->getOrigPoints($type);
            if ($type == self::TRANSFER_TYPE_SPENDING) {
                // if it's a spending transfer we should double-check that the customer can afford it
                $customer = Mage::getModel('rewards/customer')->load($customerId);
                if (!$customer->canAfford($remainingPoints, $currencyId)) {
                    // TODO: log the error here
                    Mage::throwException(
                        $this->__("Failed to adjust points on this order.  The customer has %s but Points Spent was increased by %s.",
                            Mage::getModel('rewards/points')->set($currencyId, $customer->getUsablePointsBalance($currencyId)),
                            Mage::getModel('rewards/points')->set($currencyId, $remainingPoints)
                        )
                    );
                }
                $remainingPoints *= -1;
            }

            // only use the Approved status if ALL transfer were approved... otherwise use Pending
            $status = $hasPending ? self::TRANSFER_STATUS_PENDING_EVENT :
                self::TRANSFER_STATUS_APPROVED;
            $this->_makeAdjustmentTransfer($remainingPoints, $currencyId, $status, $customerId, $orderId, $comments);

            return $this;
        }

        return $this;
    }

    /**
     * Creates a brand new transfer and associates it with an order.
     * @param int $points The amount of points for the transfer
     * @param int $currencyId The currency of the transfer
     * @param int $status The status of the new transfer (usually Approved or Pending)
     * @param int $customerId The customer ID to whom to give the transfer
     * @param int $orderId The order ID to which the transfer should be associated
     * @param string $comments The comments for the new transfer
     * @return self
     */
    protected function _makeAdjustmentTransfer($points, $currencyId, $status, $customerId, $orderId, $comments = "Points adjustment")
    {
        $newTransfer = Mage::getModel('rewards/transfer')->initTransfer($points, $currencyId, null, $customerId);
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

        $newTransfer->setComments($comments);

        $newTransfer->setOrderId($orderId)
            ->setCustomerId($customerId)
            ->save();

        return $this;
    }

    /**
     * Initializes the array we use to store transfers in this singleton.
     * The array should be formatted such that its top level groups distributions vs.
     * redemptions, within each of those levels we then have groups based on status
     * (Approved, Pending Approval, Pending Event, Pending Time), and those groups
     * contain all the transfers of the appropriate type, ordered by DESC absolute quantity.
     * @return array
     */
    protected function _initTransfersArray()
    {
        $transfersByStatus = array(
            self::TRANSFER_TYPE_EARNING => $this->_getTransferStatuses(),
            self::TRANSFER_TYPE_SPENDING => $this->_getTransferStatuses()
        );

        return $transfersByStatus;
    }

    protected function _getTransferStatuses()
    {
        $transfersStatuses = array(
            self::TRANSFER_STATUS_APPROVED => array(),
            self::TRANSFER_STATUS_PENDING_APPROVAL => array(),
            self::TRANSFER_STATUS_PENDING_EVENT => array(),
            self::TRANSFER_STATUS_PENDING_TIME => array()
        );

        return $transfersStatuses;
    }

    /**
     * Initializes a simple array used to store the total sum of points on the
     * original order.  Formatted with a top level of distributions versus
     * redemptions, which points to the appropriate sum.
     * @return array
     */
    protected function _initPointsSumArray()
    {
        $pointsSum = array(
            self::TRANSFER_TYPE_EARNING => 0,
            self::TRANSFER_TYPE_SPENDING => 0
        );

        return $pointsSum;
    }

    /**
     * Gets the simple sum of points earned on the order.
     * @return int
     */
    public function getOrigEarned()
    {
        return $this->getOrigPoints(self::TRANSFER_TYPE_EARNING);
    }

    /**
     * Gets the simple sum of points spent on the order
     * @return int
     */
    public function getOrigSpent()
    {
        return $this->getOrigPoints(self::TRANSFER_TYPE_SPENDING);
    }

    /**
     * Gets an array of distributions on this order, grouped by status
     * @return int
     */
    public function getEarnedTransfers()
    {
        return $this->getTransfers(self::TRANSFER_TYPE_EARNING);
    }

    /**
     * Gets an array of redemptions on this order, grouped by status
     * @return int
     */
    public function getSpentTransfers()
    {
        return $this->getTransfers(self::TRANSFER_TYPE_SPENDING);
    }

    /**
     * Adds an integer value to the running sum of points earned on the order
     * @param int $quantity
     * @return self
     */
    public function addToOrigEarned($quantity)
    {
        return $this->addToOrigPoints(self::TRANSFER_TYPE_EARNING, $quantity);
    }

    /**
     * Adds an integer value to the running sum of points spent on the order
     * @param int $quantity
     * @return self
     */
    public function addToOrigSpent($quantity)
    {
        return $this->addToOrigPoints(self::TRANSFER_TYPE_SPENDING, $quantity);
    }

    /**
     * Adds a transfer to the list of distributions, grouped by status
     * @param TBT_Rewards_Model_Transfer $transfer
     * @return self
     */
    public function addEarnedTransfer($transfer)
    {
        return $this->addTransfer(self::TRANSFER_TYPE_EARNING, $transfer);
    }

    /**
     * Adds a transfer to the list of redemptions, grouped by status
     * @param TBT_Rewards_Model_Transfer $transfer
     * @return self
     */
    public function addSpentTransfer($transfer)
    {
        return $this->addTransfer(self::TRANSFER_TYPE_SPENDING, $transfer);
    }

    /**
     * Gets the array (grouped by type: distri vs. redem) of the sum of points
     * on the order, or the simple sum if a type is specified.
     * @param string $type const TRANSFER_TYPE_EARNING|TRANSFER_TYPE_SPENDING
     * @return mixed array|int
     */
    public function getOrigPoints($type = null)
    {
        $pointsSum = $this->getData('orig_points');
        return ($type === null) ? $pointsSum : $pointsSum[$type];
    }

    /**
     * Gets the array of type-grouped, the status-grouped transfers...
     * or just an array of status-grouped transfers if a type is specified.
     * @param string $type const TRANSFER_TYPE_EARNING|TRANSFER_TYPE_SPENDING
     * @return array
     */
    public function getTransfers($type = null)
    {
        $transfers = $this->getData('transfers');
        return ($type === null) ? $transfers : $transfers[$type];
    }

    /**
     * Adds an integer value to the running sum of points by type (distri vs. redem)
     * @param string $type const TRANSFER_TYPE_EARNING|TRANSFER_TYPE_SPENDING
     * @param int $quantity The amount to add to the sum
     * @return self
     */
    public function addToOrigPoints($type, $quantity)
    {
        $pointsSum = $this->getOrigPoints();
        $pointsSum[$type] += $quantity;
        $this->setOrigPoints($pointsSum);

        return $this;
    }

    /**
     * Adds a transfer to the list of transfers grouped by type (distri vs. redem)
     * Uses the transfer's status to group it further by status.
     * @param string $type const TRANSFER_TYPE_EARNING|TRANSFER_TYPE_SPENDING
     * @param TBT_Rewards_Model_Transfer $transfer
     * @return self
     */
    public function addTransfer($type, $transfer)
    {
        $transfers = $this->getTransfers();
        $transfers[$type][$transfer->getStatus()][] = $transfer;
        $this->setTransfers($transfers);

        return $this;
    }

    protected function __()
    {
        $helper = Mage::helper('rewards');
        $args = func_get_args();

        return call_user_func_array(array($helper, '__'), $args);
    }

    /**
     * Retrieve adminhtml session model object
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * @return TBT_Rewards_Model_Session
     */
    protected function _getRewardsSession()
    {
        return Mage::getSingleton('rewards/session');
    }
}
