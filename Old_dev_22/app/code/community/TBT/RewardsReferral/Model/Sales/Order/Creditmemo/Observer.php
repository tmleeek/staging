<?php

class TBT_RewardsReferral_Model_Sales_Order_Creditmemo_Observer extends TBT_Rewards_Model_Sales_Order_Creditmemo_Observer
{
    /**
     * Observes event 'rewards_sales_order_credit_memo_automatic_cancel' that
     * will be only fired in Magento prior to 1.4.1.1 when doing a credit memo refund.
     * If option Order Fulfillment > Automatically Remove Points is enabled,
     * will cancel/revoke any affiliate transfers for an order.
     * @param   Varien_Event_Observer $observer
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
                // this means a mass admin order cancel operation made by administrator
                $this->_displayAdminMessages($affiliateTransfers);
            }

        }

        return $this;
    }

}