<?php

class TBT_Rewards_Model_Sales_Order_Payment_Observer extends Varien_Object
{
    /**
     * Observes the sales_order_payment_cancel event.
     * Checks for errors placed by rewards/adminhtml_controller_sales_order_observer::cancelPreDispatch.
     * We can't throw exceptions in a predispatch observer and have them result in session errors, so instead
     * we toss them into the rewards session and pull them out here.
     * @param Varien_Event_Observer $observer
     * @return self
     */
    public function cancel($observer)
    {
        $messages = $this->_getRewardsSession()->getMessages();
        $error = $messages->getMessageByIdentifier('rewards_order_cancel_failed');
        if ($error) {
            $messages->deleteMessageByIdentifier('rewards_order_cancel_failed');
            Mage::throwException($error->getText());
        }

        return $this;
    }

    /**
     * Observes the order_cancel_after event.
     * Automatically cancels point transfers, if it a mass admin cancel operation,
     * a payment failure at checkout (paypal, authorize.net), or if Magento prior
     * to 1.4.1.1 and it's a single admin order cancel.
     * @param $observer
     * @return TBT_Rewards_Model_Sales_Order_Payment_Observer
     */
    public function automaticCancel($observer)
    {
        // only if this is not a single admin order cancel operation
        if (Mage::registry('sweet_tooth_single_admin_order_cancel')) {
            return $this;
        }

        $order = $observer->getEvent()->getPayment()->getOrder();

        if (! $order) {
            return $this;
        }

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
                if (! $this->_getRewardsSession ()->isAdminMode ()) {
                    // this means a payment failure while placing the order on the front-end
                    $this->_clearTransferCreatedMessages();
                    $this->_displayFrontendMessages($order);
                } else {
                    // this means a mass admin order cancel operation made by administrator
                    $this->_displayAdminMessages($orderTransfers);
                }
            }
        }
        // dispatching this event so we can hook aditional logic in Referral module
        Mage::dispatchEvent('rewards_sales_order_payment_automatic_cancel',
            array('order' => $order));

        Mage::dispatchEvent('rewards_sales_order_payment_automatic_cancel_done',
            array('order' => $order));

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
        $message->setIdentifier('TBT_Rewards_Model_Sales_Order_Payment_Observer(cancel points)');

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
        $message->setIdentifier('TBT_Rewards_Model_Sales_Order_Payment_Observer(cancel points)');

        Mage::getSingleton('core/session')->addMessage($message);

        return $this;
    }

    /**
     * This will remove any previously set messages from when a transfer was created placing the order
     * Check TBT_Rewards_Model_Observer_Sales_Order_Save_After_Create::createPointsTransfers().
     */
    protected function _clearTransferCreatedMessages()
    {
        $messages = Mage::getSingleton ( 'core/session' )->getMessages();
        $messages->deleteMessageByIdentifier("TBT_Rewards_Model_Observer_Sales_Order_Save_After_Create(pending points)");

        return $this;
    }

    /**
     * Displays appropriate frontend messages to the user. This will be triggered if a payment like Paypal or
     * Authorize.net, will fail during the process of placing an order.
     *
     * @param $order
     * @return TBT_Rewards_Model_Sales_Order_Payment_Observer
     */
    protected function _displayFrontendMessages($order)
    {
        $earned_points_string = Mage::getModel('rewards/points')->set($order->getTotalEarnedPoints());
        $redeemed_points_string = Mage::getModel('rewards/points')->set($order->getTotalSpentPoints());
        $model = Mage::getModel('cybermut/payment');

        if ($order->hasPointsEarning () && !$order->hasPointsSpending ()) {
            $this->_dispatchError ( Mage::helper ( 'rewards' )->__ ( 'You earned %s for the order you just placed.', $earned_points_string ) );
        } elseif (!$order->hasPointsEarning () && $order->hasPointsSpending ()) {
            $this->_dispatchError ( Mage::helper ( 'rewards' )->__ ( 'You spent %s for the order you just placed.', $redeemed_points_string ) );
        } elseif ($order->hasPointsEarning () && $order->hasPointsSpending ()) {
            $this->_dispatchError ( Mage::helper ( 'rewards' )->__ ( 'You earned %s and spent %s for the order you just placed.', $earned_points_string, $redeemed_points_string ) );
        } else {
            // no points earned or spent
        }
      // echo $order->getStatus(); exit;
      $_code = $order->getPayment()->getMethodInstance()->getCode(); 
	  if($_code != 'cybermut_payment' && $_code != 'cybermutforeign_payment')    
	  {
          Mage::getSingleton('checkout/session')->addError(Mage::helper('sales')->__("You did not complete your payment and your order has been cancelled. If you have encountered a problem during your payment, we invite you to either change your payment method or to contact us by at +33 (0) 811 69 69 29 (French cost call) or by e-mail at <a href='mailto:support@az-boutique.fr'>support@az-boutique.fr</a>."));
      }
        //$this->_redirect('checkout/cart');
        return $this ;

       // return $this;
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
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getRewardsSession()
    {
        return Mage::getSingleton('rewards/session');
    }
}
