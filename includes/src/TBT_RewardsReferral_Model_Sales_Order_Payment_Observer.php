<?php

class TBT_RewardsReferral_Model_Sales_Order_Payment_Observer extends TBT_Rewards_Model_Sales_Order_Payment_Observer
{
    /**
     * Observes 'rewards_sales_order_payment_automatic_cancel'.
     * Automatically cancels affiliate point transfers, if it is a mass admin cancel
     * operation, a payment failure at checkout (paypal, authorize.net), or if
     * Magento prior to 1.4.1.1 and it's a single admin order cancel.
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function automaticCancelAffiliate($observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (!$order) {
            return $this;
        }

        if (Mage::helper ( 'rewards/config' )->shouldRemovePointsOnCancelledOrder ()) {

            $displayMessages = false;

            $affiliateTransferReferences = Mage::getResourceModel( 'rewardsref/referral_order_transfer_reference_collection' )
                ->addTransferInfo()
                ->filterAssociatedWithOrder($order->getId());
            $affiliateTransfers = array();

            foreach ($affiliateTransferReferences as $affiliateReference) {
                $affiliateTransfer = Mage::getModel('rewardsref/transfer')->load($affiliateReference->getRewardsTransferId());
                $affiliateTransfers[] = $affiliateTransfer;

                if (($affiliateTransfer->getStatus () == TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_EVENT) || ($affiliateTransfer->getStatus () == TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_APPROVAL)) {
                    $affiliateTransfer->setStatus ( $affiliateTransfer->getStatus (), TBT_Rewards_Model_Transfer_Status::STATUS_CANCELLED );
                    $affiliateTransfer->save ();
                    $affiliateTransfer->setCanceled(1);
                    $displayMessages = true;
                } else if ($affiliateTransfer->getStatus () == TBT_Rewards_Model_Transfer_Status::STATUS_APPROVED) {
                    try {
                        // try to revoke the transfer and keep track of the new transfer ID to notify admin
                        $revokedTransferId = $affiliateTransfer->revoke()->getId();
                        $affiliateTransfer->setRevokedTransferId($revokedTransferId);
                        $displayMessages = true;
                    } catch ( Exception $ex ) {
                        $affiliateTransfer->setRevokedTransferId(null);
                        continue;
                    }
                } else if ($affiliateTransfer->getStatus () == TBT_Rewards_Model_Transfer_Status::STATUS_CANCELLED) {
                    // transfer was already canceled (by admin, probably), so we just notify him
                    $affiliateTransfer->setCanceled(0);
                    $displayMessages = true;
                }
            }

            if ($displayMessages) {
                if ($this->_getRewardsSession ()->isAdminMode ()) {
                    // this means a mass admin order cancel operation made by administrator
                    $this->_displayAdminMessages($affiliateTransfers);
                }
            }

        }

        return $this;
    }

}