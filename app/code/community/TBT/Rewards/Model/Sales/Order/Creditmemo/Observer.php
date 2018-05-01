<?php

class TBT_Rewards_Model_Sales_Order_Creditmemo_Observer extends Varien_Object
{
    /**
     * Observes the sales_order_creditmemo_refund event.
     * Checks for errors placed by rewards/adminhtml_controller_sales_order_creditmemo_observer::savePreDispatch.
     * We can't throw exceptions in a predispatch observer and have them result in session errors, so instead
     * we toss them into the rewards session and pull them out here.
     * @param Varien_Event_Observer $observer
     * @return self
     */
    public function refund($observer)
    {
        if (! Mage::helper('rewards/version')->isBaseMageVersionAtLeast('1.4.1.1')) {
            $this->_automaticCancel($observer);
            return $this;
        }

        $messages = $this->_getRewardsSession()->getMessages();
        $error = $messages->getMessageByIdentifier('rewards_creditmemo_failed');
        if ($error) {
            $messages->deleteMessageByIdentifier('rewards_creditmemo_failed');
            Mage::throwException($error->getText());
        }

        return $this;
    }

    protected function _automaticCancel($observer)
    {
        $creditMemo = $observer->getEvent()->getCreditmemo();
        if (! $creditMemo) {
            return $this;
        }
        $order = Mage::getModel('sales/order')->load($creditMemo->getOrderId());

        if (Mage::helper ( 'rewards/config' )->shouldRemovePointsOnCancelledOrder ()) {

            $displayMessages = false;
            $orderTransfers = Mage::getModel ( 'rewards/transfer' )->getTransfersAssociatedWithOrder ( $order->getId () );

            foreach ( $orderTransfers as $transfer ) {
                if (($transfer->getStatus () == TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_EVENT) || ($transfer->getStatus () == TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_APPROVAL)) {
                    $transfer->setStatus ( $transfer->getStatus (), TBT_Rewards_Model_Transfer_Status::STATUS_CANCELLED );
                    $transfer->save ();
                    $transfer->setCanceled(1);
                    $displayMessages = true;
                } else if ($transfer->getStatus () == TBT_Rewards_Model_Transfer_Status::STATUS_APPROVED) {
                    try {
                        // try to revoke the transfer and keep track of the new transfer ID to notify admin
                        $revokedTransferId = $transfer->revoke()->getId();
                        $transfer->setRevokedTransferId($revokedTransferId);
                        $displayMessages = true;
                    } catch ( Exception $ex ) {
                        $transfer->setRevokedTransferId(null);
                        continue;
                    }
                } else if ($transfer->getStatus () == TBT_Rewards_Model_Transfer_Status::STATUS_CANCELLED) {
                    // transfer was already canceled (by admin, probably), so we just notify him
                    $transfer->setCanceled(0);
                    $displayMessages = true;
                }
            }

            if ($displayMessages) {
                // this means a mass admin order cancel operation made by administrator
                $this->_displayAdminMessages($orderTransfers);
            }
        }

        // dispatching this event so we can hook aditional logic in Referral module
        Mage::dispatchEvent('rewards_sales_order_credit_memo_automatic_cancel',
            array('order' => $order));

        return $this;
    }

    /**
     * Displays appropriate message for the administrator in the back-end. This will be triggered if the admin is
     * performing a mass order cancel operation.
     *
     * @param $orderTransfers
     * @return TBT_Rewards_Model_Sales_Order_Payment_Observer
     */
    protected function _displayAdminMessages($orderTransfers)
    {
        foreach ( $orderTransfers as $transfer ) {
            if ($transfer->getStatus() == TBT_Rewards_Model_Transfer_Status::STATUS_CANCELLED) {
                if ($transfer->getCanceled()) {
                    // if point transfer successfully canceled, notify administrator
                    $this->_dispatchSuccess(Mage::helper ( 'rewards' )->__ ( 'Successfully cancelled points transfer ID #' . $transfer->getId ()));
                } else {
                    // if point transfer were already canceled previously to canceling the order, notify administrator
                    $this->_dispatchSuccess(Mage::helper ( 'rewards' )->__ ( 'No action taken on points transfer ID #' . $transfer->getId () . ". Transfer was already canceled prior to this."));
                }
            } else if ($transfer->getStatus() == TBT_Rewards_Model_Transfer_Status::STATUS_APPROVED) {
                if ($transfer->getRevokedTransferId()) {
                    // if points are in Approved status and a new transfer was made to REVOKE the points, notify the admin
                    $this->_dispatchSuccess( Mage::helper ( 'rewards' )->__ ( 'Successfully revoked already approved points from transfer ID #' . $transfer->getId () . " through a new transfer ID #" . $transfer->getRevokedTransferId()) );
                } else {
                    // if creating a revoke transfer failed, let the admin know
                    $this->_dispatchError(Mage::helper ( 'rewards' )->__ ( 'There was a problem revoking already approved points from transfer ID #' . $transfer->getId () . ". Please make any necessary manual adjustments. "));
                }
            }
        }

        return $this;
    }

    /**
     * Adds error message to Session
     * @param string $str_msg
     */
    protected function _dispatchError($str_msg) {
        /* @var $message Mage_Core_Model_Message */
        $message_factory = Mage::getSingleton('core/message');
        $message = $message_factory->error($str_msg);
        $message->setIdentifier('TBT_Rewards_Model_Sales_Order_Creditmemo_Observer(cancel points)');

        Mage::getSingleton('core/session')->addMessage($message);

        return $this;
    }

    /**
     * Adds success message to Session
     * @param string $str_msg
     */
    protected function _dispatchSuccess($str_msg) {
        /* @var $message Mage_Core_Model_Message */
        $message_factory = Mage::getSingleton('core/message');
        $message = $message_factory->success($str_msg);
        $message->setIdentifier('TBT_Rewards_Model_Sales_Order_Creditmemo_Observer(cancel points)');

        Mage::getSingleton('core/session')->addMessage($message);

        return $this;
    }

    /**
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getRewardsSession()
    {
        return Mage::getSingleton('rewards/session');
    }
}
