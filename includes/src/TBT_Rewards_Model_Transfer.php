<?php

/**
 * WDCA - Sweet Tooth
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS
 * License, which extends the Open Software License (OSL 3.0).
 * The Sweet Tooth License is available at this URL:
 * https://www.sweettoothrewards.com/terms-of-service
 * The Open Software License is available at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * By adding to, editing, or in any way modifying this code, WDCA is
 * not held liable for any inconsistencies or abnormalities in the
 * behaviour of this code.
 * By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by WDCA, outlined in the
 * provided Sweet Tooth License.
 * Upon discovery of modified code in the process of support, the Licensee
 * is still held accountable for any and all billable time WDCA spent
 * during the support process.
 * WDCA does not guarantee compatibility with any other framework extension.
 * WDCA is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to
 * support@sweettoothrewards.com or call 1.855.699.9322, so we can send you a copy
 * immediately.
 *
 * @category   [TBT]
 * @package    [TBT_Rewards]
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Transfer
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 *
 * @method     TBT_Rewards_Model_Mysql4_Transfer_Collection getCollection()
 */
class TBT_Rewards_Model_Transfer extends TBT_Rewards_Model_Abstract
{
    /** Properties for event transfers **/
    protected $_eventPrefix = 'rewards_transfer';
    protected $_eventObject = 'rewards_transfer';

    protected $_references = null;

    protected $_isNew    = false;

    /**
     * When a new transfer is saved, in _afterSave() we need to re-save the transfer again to save 'source_reference_id'
     * field. We use $_isResave to skip re-running some unnecesary code in _beforeSave, _afterSave and _afterLoad methods.
     *
     * @var bool
     **/
    protected $_isResave = false;

    public function _construct() {
        parent::_construct ();
        $this->_init ( 'rewards/transfer' );
        $this->loadReferenceInformation ();
    }

    /**
     * Cancels this points tranfer if possible.
     * If success, saves the cancellation.
     * @return true if success, false otherwise.
     */
    public function cancel() {
        $status_change_result = $this->setStatus($this->getStatus(), TBT_Rewards_Model_Transfer_Status::STATUS_CANCELLED);

        if($status_change_result === false) {
            return false;
        }

        $this->save();

        return true;
    }

    /**
     * Adds to the comments of this points transfer, separated by a \n\r (does not save)
     * @param string $new_comment
     */
    public function appendComments($new_comment) {
        $old_comments = $this->getComments();
        $this->setComments( $old_comments . "\n\r" . $new_comment );
        return $this;
    }

    public function setOrderId($id) {
        $this->clearReferences ();
        $this->setReferenceType ( TBT_Rewards_Model_Transfer_Reference::REFERENCE_ORDER );
        $this->setReferenceId ( $id );
        $this->_data ['order_id'] = $id;

        return $this;
    }


    public function setRatingId($id) {
        $this->clearReferences ();
        $this->setReferenceType ( TBT_Rewards_Model_Transfer_Reference::REFERENCE_RATING );
        $this->setReferenceId ( $id );
        $this->_data ['rating_id'] = $id;

        return $this;
    }


    public function setPollId($id) {
        $this->clearReferences ();
        $this->setReferenceType ( TBT_Rewards_Model_Transfer_Reference::REFERENCE_POLL );
        $this->setReferenceId ( $id );
        $this->_data ['poll_id'] = $id;

        return $this;
    }

    public function setReferenceTransferId($id) {
        $this->clearReferences ();
        $this->setReferenceType ( TBT_Rewards_Model_Transfer_Reference::REFERENCE_TRANSFER );
        $this->setReferenceId ( $id );
        $this->_data ['reference_transfer_id'] = $id;

        return $this;
    }

    public function setAsSignup() {
        $this->clearReferences ();
        $this->setReferenceType ( TBT_Rewards_Model_Transfer_Reference::REFERENCE_SIGNUP );
        $this->setReferenceId ( - 1 );

        return $this;
    }

    public function setToFriendId($id) {
        $this->clearReferences ();
        $this->setReferenceType ( TBT_Rewards_Model_Transfer_Reference::REFERENCE_TO_FRIEND );
        $this->setReferenceId ( $id );
        $this->_data ['friend_id'] = $id;

        return $this;
    }

    public function setFromFriendId($id) {
        $this->clearReferences ();
        $this->setReferenceType ( TBT_Rewards_Model_Transfer_Reference::REFERENCE_FROM_FRIEND );
        $this->setReferenceId ( $id );
        $this->_data ['friend_id'] = $id;

        return $this;
    }

    public function getTransferId() {
        return $this->_data ['reference_transfer_id'];
    }

    public function isOrder() {
        return ($this->getReferenceType () == TBT_Rewards_Model_Transfer_Reference::REFERENCE_ORDER) || isset ( $this->_data ['order_id'] );
    }


    public function isRating() {
        return ($this->getReferenceType () == TBT_Rewards_Model_Transfer_Reference::REFERENCE_RATING) || isset ( $this->_data ['rating_id'] );
    }


    public function isPoll() {
        return ($this->getReferenceType () == TBT_Rewards_Model_Transfer_Reference::REFERENCE_POLL) || isset ( $this->_data ['poll_id'] );
    }

    /**
     * True if transfer references transfer
     *
     * @return boolean
     */
    public function isTransfer() {
        return ($this->getReferenceType () == TBT_Rewards_Model_Transfer_Reference::REFERENCE_TRANSFER) || isset ( $this->_data ['reference_transfer_id'] );
    }

    public function isSignup() {
        return ($this->getReferenceType () == TBT_Rewards_Model_Transfer_Reference::REFERENCE_SIGNUP);
    }

    /**
     * Is this is a transfer to a friend? (-)
     *
     * @return boolean
     */
    public function isToFriend() {
        return ($this->getReferenceType () == TBT_Rewards_Model_Transfer_Reference::REFERENCE_TO_FRIEND);
    }

    /**
     * Is this a transfer from a friend (+)
     *
     * @return boolean
     */
    public function isFromFriend() {
        return ($this->getReferenceType () == TBT_Rewards_Model_Transfer_Reference::REFERENCE_FROM_FRIEND);
    }

    /**
     * Is this any kind of friend-to-friend transfer?
     *
     * @return boolean
     */
    public function isFriendTransfer() {
        return (($this->getReferenceType () == TBT_Rewards_Model_Transfer_Reference::REFERENCE_TO_FRIEND) || ($this->getReferenceType () == TBT_Rewards_Model_Transfer_Reference::REFERENCE_FROM_FRIEND)) || isset ( $this->_data ['friend_id'] );
    }

    /**
     * Checks to see if this points transfer contains a reference that
     * is of the provided numeric reference type.
     *
     * @param int $reference_type_id numeric reference type ID
     * @return boolean true if a reference of the specified type exists for this points transfer.
     */
    public function isReferenceType($reference_type_id) {
        $refs = $this->getAllReferences( true )->load();
        foreach ($refs as $reference) {
            if ( $reference->getReferenceType() == $reference_type_id ) {
                return true;
            }
        }
        return false;
    }

    /**
     * If a reference type ID is provided, this method will try to return back the
     * reference ID of of that type.  If it is not provided, then it will return
     * back the reference_id stored in the model's data.
     *
     * @param int $reference_type   numeric reference type ID
     * @return int referece_ID of the reference found or NULL if no reference is found.
     */
    public function getReferenceId($reference_type = null) {
        if ( ! $reference_type ) {
            return parent::getData( 'reference_id' );
        }

        $refs = $this->getAllReferences( true )->load();
        foreach ($refs as $reference) {
            if ( $reference->getReferenceType() == $reference_type ) {
                return $reference->getReferenceId();
            }
        }
        return null;
    }

    public function getTransfersAssociatedWithOrder($order_id) {
        return $this->getCollection ()->addFilter ( 'reference_type', TBT_Rewards_Model_Transfer_Reference::REFERENCE_ORDER )->addFilter ( 'reference_id', $order_id );
    }


    public function getTransfersAssociatedWithRating($rating_id) {
        return $this->getCollection ()->addFilter ( 'reference_type', TBT_Rewards_Model_Transfer_Reference::REFERENCE_RATING )->addFilter ( 'reference_id', $rating_id );
    }


    /**
     * @deprecated from version 1.7.6.3+
     */
    public function getTransfersAssociatedWithPoll($poll_id) {
        return $this->getCollection ()->addFilter ( 'reference_type', TBT_Rewards_Model_Transfer_Reference::REFERENCE_POLL )->addFilter ( 'reference_id', $poll_id );
    }

    public function getTransfersAssociatedWithTransfer($transfer_id) {
        return $this->getCollection ()->addFilter ( 'reference_type', TBT_Rewards_Model_Transfer_Reference::REFERENCE_TRANSFER )->addFilter ( 'reference_id', $transfer_id );
    }

    public function getTransfersAssociatedWithSignup($customer_id) {
        return $this->getCollection ()->addFilter ( 'reference_type', TBT_Rewards_Model_Transfer_Reference::REFERENCE_SIGNUP )->addFilter ( 'customer_id', $customer_id );
    }

    public function getTransfersSentToFriend($friend_id) {
        return $this->getCollection ()->addFilter ( 'reference_type', TBT_Rewards_Model_Transfer_Reference::REFERENCE_TO_FRIEND )->addFilter ( 'reference_id', $friend_id );
    }

    public function getTransfersSentFromFriend($friend_id) {
        return $this->getCollection ()->addFilter ( 'reference_type', TBT_Rewards_Model_Transfer_Reference::REFERENCE_FROM_FRIEND )->addFilter ( 'reference_id', $friend_id );
    }

    public function getTransfersAssociatedWithFriend($friend_id) {
        return $this->getCollection ()->addFilter ( 'reference_type', TBT_Rewards_Model_Transfer_Reference::REFERENCE_FROM_FRIEND || TBT_Rewards_Model_Transfer_Reference::REFERENCE_TO_FRIEND )->addFilter ( 'reference_id', $friend_id );
    }

    /**
     * Sets the status of the transfer if possible.
     * If the new transfer is illegal, returns false,
     * otherwise, updates the status and returns $this.
     * @return mixed boolean if false, or $this if OK
     */
    public function setStatus($oldStatusId, $newStatusId) {
        $availStatuses = Mage::getSingleton ( 'rewards/transfer_status' )->getAvailableNextStatuses ( $this->getStatus () );
        if (array_search ( $newStatusId, $availStatuses ) !== false) {
            //@nelkaake Changed on Thursday May 27, 2010:  changed parent::setStatus to $this->setData since some servers have trouble reading the former.
            return $this->setData ( "status", $newStatusId );
        } else {
            return false;
        }
    }

    public function _beforeSave()
    {
        // automatically set the last updated timestamps to the current date/time
        $this->setLastUpdateTs ( now () );

        if ($this->_isResave) {
            return parent::_beforeSave();
        }

        if ($this->getQuantity () == 0) {
            throw new Exception ( "You must select a quantity of points not equal to (0)." . " If you want to void a transfer, set it's status to cancelled or revoked." );
        }
        if ($this->getCustomerId () == null || $this->getCustomerId () == '') {
            throw new Exception ( "Please select a customer for this transfer." );
        }

        if ($customer = Mage::getModel ( 'rewards/customer' )->load ( $this->getCustomerId () )) {
            $old_balance = $customer->getPointsBalance ( $this->getCurrencyId () );
            $new_balance = $old_balance + $this->getQuantity ();
            if ($new_balance < 0 && $old_balance >= 0) {
                if (Mage::helper ( 'rewards/config' )->canHaveNegativePtsBalance ()) {
                    // warning, going into negative points!!
                } else {
                    // not allowed to go into negative points!
                    throw new Exception ( "The transfer cannot be completed because the customer will have less than zero (0) points." );
                }
            }
        } else {
            throw new Exception ( "Transfer could not be completed because customer no longer exists!" );
        }

        if ($this->getStatus () == - 1) {
            $this->setReferenceTransferId ( $this->getId () )->setStatus ( null, TBT_Rewards_Model_Transfer_Status::STATUS_APPROVED )->setReasonId ( TBT_Rewards_Model_Transfer_Reason::REASON_SYSTEM_REVOKED )->setQuantity ( $this->getQuantity () * - 1 )->setId ( null );
        }

        $this->storeReferenceData ();

        if ($this->getId ()) {
            $this->_isNew = false;

            $om = Mage::getModel ( 'rewards/transfer' )->load ( $this->getId () );
            $s = Mage::getSingleton ( 'rewards/transfer_status' );

            if (! $this->getStatus ()) {
                $this->setStatus ( null, $om->getStatus () );
            }

            $current_status = $om->getStatus ();
            $next_status = $this->getStatus ();

            $availStat = $s->getAvailStatuses ( $current_status );
            if (! isset ( $availStat [$next_status] )) {
                throw new Exception ( "You cannot change the status from " . $s->getStatusCaption ( $current_status ) . " to " . $s->getStatusCaption ( $next_status ) . " for this transfer." );
            }

            try {
                if (! $s->canAdjustQty ( $current_status ) && ($om->getQuantity () != $this->getQuantity ())) {
                    throw new Exception ( "quantity" );
                }
                if (! $s->canAdjustComments ( $current_status ) && ($om->getComments () != $this->getComments ())) {
                    throw new Exception ( "comments" );
                }
                if (! $s->canAdjustCustomer ( $current_status ) && ($om->getCustomerId () != $this->getCustomerId ())) {
                    throw new Exception ( "customer" );
                }
                if (! $s->canAdjustReference ( $current_status ) && ($om->getReferenceId () != $this->getReferenceId ())) {
                    //throw new Exception("{Current: [". $om->getReferenceId() ."]  New: [". $this->getReferenceId() ."]}");
                    throw new Exception ( "associated reference" );
                }
            } catch ( Exception $e ) {
                $attr = $e->getMessage ();
                throw new Exception ( "You cannot change the $attr for this transfer because of the " . "current status that it is in.  Instead, make a new transfer as an adjustment." );
            }
            if (! $s->canAdjustStatus ( $current_status, $next_status )) {
                throw new Exception ( "You cannot change the status from $current_status to $next_status." );
            }
        } else {
            $this->_isNew = true;

            // if this is a new transfer then automatically set the created timestamps to the current date/time
            $creation_ts = $this->getCreationTs ();
            if (empty ( $creation_ts )) {
                $this->setCreationTs ( now () );
            }

            //get On-Hold initial status override
            if ($this->getRuleId() && $this->getReferenceType() != TBT_Rewards_Model_Transfer_Reference::REFERENCE_ORDER) {
                $rule = Mage::getModel('rewards/special')->load($this->getRuleId());
                if ($rule->getOnholdDuration() > 0) {
                    $this->setEffectiveStart(date('Y-m-d H:i:s', strtotime("+{$rule->getOnholdDuration()} days")));
                    $this->setData('status', TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_TIME);
                }
            }
        }


        return parent::_beforeSave ();
    }

    public function _afterSave()
    {
        if ($this->_isResave) {
            return parent::_afterSave();
        }

        if ($this->_isNew) {
            Mage::getSingleton ( 'rewards/transfer_types' )->transferBeforeSave ( $this );

            $ref = Mage::getModel ( 'rewards/transfer_reference' )->loadReferenceByTransferId($this->getId())
                ->setData($this->getData())
                ->setId($this->_data['reference_data']['id'])
                ->setReferenceId($this->getReferenceId())
                ->setReferenceType($this->getReferenceType())
                ->setRuleId($this->_data['reference_data']['rule_id'])
                ->save();

            Mage::getSingleton ( 'rewards/transfer_types' )->transferAfterSave ( $this );

            // set _isResave flag
            $this->_isResave = true;

            //if the transfer is new, save the 'source_reference_id' field in 'rewards_transfer' table
            $this->setSourceReferenceId($ref->getId())
                ->save();

            Mage::dispatchEvent('rewards_transfer_new', array('rewards_transfer' => $this));
        }

        if ($this->getReasonId() == TBT_Rewards_Model_Transfer_Reason_SystemRevoked::REASON_TYPE_ID) {
            Mage::dispatchEvent('rewards_transfer_revoke', array(
                'rewards_transfer' => Mage::getModel('rewards/transfer')->load($this->getReferenceId()),
                'new_transfer' => $this
            ));
        }

        return parent::_afterSave ();
    }

    public function _afterLoad()
    {
        if ($this->_isResave) {
            // reset isResave flag
            $this->_isResave = false;
            return parent::_afterLoad();
        }

        $this->loadReferenceInformation ();

        return parent::_afterLoad ();
    }

    public function _beforeDelete() {
        throw new Exception ( "You cannot delete a transfer. You may however cancel or revoke an " . "existing transfer to achieve the same effect." );
        return parent::_beforeSave ();
    }

    // TODO: Finish this method...
    public function setReference(TBT_Rewards_Model_Transfer_Reference $ref) {
        $ref_data = array ('id' => $ref->getId (), 'rule_id' => $ref->getRuleId () );
        $this->setData ( 'reference_data', $ref_data );
        return $this;
    }

    private function loadReferenceInformation() {
        if ($this->getId ()) {
            $this->clearReferences ();

            $ref = Mage::getModel ( 'rewards/transfer_reference' )->loadReferenceByTransferId ( $this->getId () );

            $this->storeReferenceData ();

            $this->setReferenceType ( $ref->getReferenceType () );
            $this->setReferenceId ( $ref->getReferenceId () );
            Mage::getSingleton ( 'rewards/transfer_types' )->loadReferenceInformation ( $this );
        }
    }

    /**
     * Fetches the transfer reference collection for this transfer.
     * If useFreshQuery is false (default) then it will just use the stored reference collection for optimization.
     *
     * @param [$useFreshQuery=false] if true, fetches a fresh query, otherwise uses the stored one.
     * @return TBT_Rewards_Model_Mysql4_Transfer_Reference_Collection
     */
    public function getAllReferences($useFreshQuery = false) {
        if ( $useFreshQuery ) {
            return $this->_fetchAllReferences();
        }

        if ( $this->_references == null ) {
            $this->_references = $this->_fetchAllReferences();
        }
        return $this->_references;
    }

        /**
         * Fetches a transfer reference collection for this transfer
         * @return TBT_Rewards_Model_Mysql4_Transfer_Reference_Collection
         */
        protected function _fetchAllReferences() {
            return Mage::getModel( 'rewards/transfer_reference' )->getCollection()->filterByTransfer( $this->getId() );
        }

    private function storeReferenceData() {
        $reference_data = array ();
        $reference_data ['rule_id'] = null;
        $reference_data ['id'] = null;


        if (! $this->getId ()) {
            $this->_data ['reference_data'] = $reference_data;
            return $this;
        }

        $ref = Mage::getModel ( 'rewards/transfer_reference' )->loadReferenceByTransferId ( $this->getId () );

        if (! $ref->getId ()) {
            $this->_data ['reference_data'] = $reference_data;
            return $this;
        }

        $reference_data ['reference_type'] = $ref->getReferenceType ();
        $reference_data ['reference_id'] = $ref->getReferenceId ();
        $reference_data ['rule_id'] = $ref->getRuleId ();
        $reference_data ['id'] = $ref->getId ();

        $this->_data ['reference_data'] = $reference_data;
        $this->_data ['reference_type'] = $ref->getReferenceType ();
        $this->_data ['reference_id'] = $ref->getReferenceId ();



        return $this;
    }

    protected function clearReferences() {
        Mage::getSingleton ( 'rewards/transfer_types' )->clearReferences ( $this );
        return $this;
    }

    /**
     * Initiates a transfer model based on given criteria and verifies usage.
     *
     * @param integer $num_points
     * @param integer $currency_id
     * @param integer $rule_id
     * @return TBT_Rewards_Model_Transfer
     */
    public function initTransfer($num_points, $currency_id, $rule_id, $customerId = null, $skipChecks = false)
    {
        if (!Mage::getSingleton('rewards/session')->isCustomerLoggedIn() && !$skipChecks
            && !Mage::getSingleton ( 'rewards/session' )->isAdminMode ()) {
            return null;
        }

        // ALWAYS ensure that we only give an integral amount of points
        $num_points = floor ( $num_points );

        if ($num_points == 0) {
            return null;
        }

        /**
         * the transfer model to work with is this model (because this function was originally from the transfer helper)
         * @var TBT_Rewards_Model_Transfer
         */
        $transfer = &$this;

        if ($num_points > 0) {
            $transfer->setReasonId ( TBT_Rewards_Model_Transfer_Reason::REASON_CUSTOMER_DISTRIBUTION );
        } else {
            $customerId = $customerId ? $customerId : Mage::getSingleton('customer/session')->getCustomerId();
            $customer = Mage::getModel ( 'rewards/customer' )->load ( $customerId );
            if (($customer->getUsablePointsBalance ( $currency_id ) + $num_points) < 0) {
                $points_balance_str = Mage::getModel ( 'rewards/points' )->set ( $currency_id, $customer->getUsablePointsBalance ( $currency_id ) );
                $req_points_str = Mage::getModel ( 'rewards/points' )->set ( $currency_id, $num_points * - 1 );
                $error = Mage::helper('rewards')->__ ( 'Not enough points for transaction. You have %s, but you need %s.', $points_balance_str, $req_points_str );
                throw new Exception ( $error );
            }

            $transfer->setReasonId ( TBT_Rewards_Model_Transfer_Reason::REASON_CUSTOMER_REDEMPTION );
        }

        $transfer->setId(null)
            ->setCreationTs(now())
            ->setLastUpdateTs(now())
            ->setCurrencyId($currency_id)
            ->setQuantity($num_points)
            ->setCustomerId($customerId)
            ->setRuleId($rule_id);

        return $transfer;
    }

    /**
     * Revokes a points transfer by creating an oposite, linked points transfer.
     * @throws Exception
     * @return TBT_Rewards_Model_Transfer the resulting REVOKED reason type points transfer
     */
    public function revoke() {
        $transfer = Mage::getModel('rewards/transfer');

        // get the default starting status - usually Pending
        if ( ! $transfer->setStatus(null, TBT_Rewards_Model_Transfer_Status::STATUS_APPROVED) ) {
            // we tried to use an invalid status... is getInitialTransferStatusAfterReview() improper ??
            return $this;
        }

        $customer_id = $this->getCustomerId();
        $num_points = $this->getQuantity() * (-1);
        $currency_id = $this->getCurrencyId();

        $customer = Mage::getModel('rewards/customer')->load($customer_id);
        if ( ($customer->getUsablePointsBalance($currency_id) + $num_points) < 0 ) {
            $error = Mage::helper('rewards')->__('Not enough points for transaction. You have %s, but you need %s',
            Mage::getModel('rewards/points')->set($currency_id, $customer->getUsablePointsBalance($currency_id)),
            Mage::getModel('rewards/points')->set($currency_id, $num_points * - 1));
            throw new Exception($error);
        }

        $comments = Mage::getStoreConfig('rewards/transferComments/revoked');
        $comments = str_replace('\n', "\n", $comments);
        $comments = Mage::helper('rewards')->__($comments, $this->getComments());

        $transfer->setId(null)
            ->setCurrencyId($currency_id)
            ->setQuantity($num_points)
            ->setCustomerId($customer_id)
            ->setReferenceTransferId($this->getId())
            ->setReasonId(TBT_Rewards_Model_Transfer_Reason::REASON_SYSTEM_REVOKED)
            ->setComments($comments)
            ->save();

        return $transfer;
    }

    /**
     * This should be VERY avoided.
     * @param string $msg
     */
    protected function __($msg) {
        return Mage::helper ( 'rewards' )->__ ( $msg );
    }


    /**
     * @deprecated
     */
    public function setReviewId($id) {
        $this->clearReferences ();
        $this->setReferenceType ( TBT_Rewards_Model_Review_Reference::REFERENCE_TYPE_ID );
        $this->setReferenceId ( $id );
        $this->_data ['review_id'] = $id;

        return $this;
    }
    /**
     * @deprecated
     */
    public function setTagId($id) {
        $this->clearReferences ();
        $this->setReferenceType ( TBT_Rewards_Model_Tag_Reference::REFERENCE_TYPE_ID );
        $this->setReferenceId ( $id );
        $this->_data ['tag_id'] = $id;

        return $this;
    }
    /**
     * @deprecated
     */
    public function setNewsletterId($id) {
        $this->clearReferences ();
        $this->setReferenceType ( TBT_Rewards_Model_Newsletter_Subscription_Reference::REFERENCE_TYPE_ID );
        $this->setReferenceId ( $id );
        $this->_data ['newsletter_id'] = $id;

        return $this;
    }


    /**
     * @deprecated
     */
    public function getTransfersAssociatedWithTag($tag_id) {
        return $this->getCollection ()->addFilter ( 'reference_type', TBT_Rewards_Model_Tag_Reference::REFERENCE_TYPE_ID )->addFilter ( 'reference_id', $tag_id );
    }

    /**
     * @deprecated
     */
    public function getTransfersAssociatedWithNewsletter($newsletter_id) {
        return $this->getCollection ()->addFilter ( 'reference_type', TBT_Rewards_Model_Newsletter_Subscription_Reference::REFERENCE_TYPE_ID )->addFilter ( 'reference_id', $newsletter_id );
    }

    /**
     * @deprecated
     */
    public function getTransfersAssociatedWithReview($review_id) {
        return $this->getCollection ()->addFilter ( 'reference_type', TBT_Rewards_Model_Review_Reference::REFERENCE_TYPE_ID )->addFilter ( 'reference_id', $review_id );
    }


    /**
     * @deprecated
     */
    public function isNewsletter() {
        return ($this->getReferenceType () == TBT_Rewards_Model_Newsletter_Subscription_Reference::REFERENCE_TYPE_ID) || isset ( $this->_data ['newsletter_id'] );
    }

    /**
     * @deprecated
     */
    public function isTag() {
        return ($this->getReferenceType () == TBT_Rewards_Model_Tag_Reference::REFERENCE_TYPE_ID) || isset ( $this->_data ['tag_id'] );
    }

    /**
     * @deprecated
     */
    public function isReview() {
        return ($this->getReferenceType () == TBT_Rewards_Model_Review_Reference::REFERENCE_TYPE_ID) || isset ( $this->_data ['review_id'] );
    }
}
