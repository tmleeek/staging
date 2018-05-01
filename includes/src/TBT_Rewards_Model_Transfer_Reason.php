<?php

/**
 * WDCA - Sweet Tooth
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS
 * License, which extends the Open Software License (OSL 3.0).
 * The Sweet Tooth License is available at this URL:
 * http://www.wdca.ca/sweet_tooth/sweet_tooth_license.txt
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
 * contact@wdca.ca or call 1-888-699-WDCA(9322), so we can send you a copy
 * immediately.
 *
 * @category   [TBT]
 * @package    [TBT_Rewards]
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Transfer Reason
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Model_Transfer_Reason extends Varien_Object {
    // status values less than 1 means that transfer is ignored in
    // customer point calculations.

    public function getDistributionReasons() {
        return $this->_getTypes ()->getDistributionReasons ();
    }

    public function getRedemptionReasons() {
        return $this->_getTypes ()->getRedemptionReasons ();
    }

    public function getOtherReasons() {
        return $this->_getTypes ()->getOtherReasons ();
    }

    public function getDistributionReasonIds() {
        return array_keys ( $this->getDistributionReasons () );
    }

    public function getRedemptionReasonIds() {
        return array_keys ( $this->getRedemptionReasons () );
    }

    public function getOtherReasonIds() {
        return array_keys ( $this->getOtherReasons () );
    }

    public function getOptionArray() {
        return $this->_getTypes ()->getAllReasons ();
    }

    public function getReasonCodes() {
        return $this->_getTypes()->getReasonCodes();
    }

    public function getManualReasons() {
        return $this->_getTypes ()->getManualReasons ();
    }

    public function getAvailReasons($current_reason) {

        $availR = array ($current_reason );
        //@nelkaake Added on Sunday August 15, 2010: Removed & reference to fix bug #335 in Mantis
        $availR = $this->_getTypes ()->getAvailReasons ( $current_reason, $availR );

        $allR = $this->getOptionArray ();
        $ret = array ();
        foreach ( $availR as $r ) {
            $ret [$r] = $allR [$r];
        }
        return $ret;
    }

    public function getReasonCaption($id) {
        $reason = $this->getOptionArray ();
        if (isset ( $reason [$id] )) {
            return $reason [$id];
        } else {
            return null;
        }
    }

    protected function _getTypes() {
        return Mage::getSingleton ( 'rewards/transfer_types' );
    }

    /**
     * @deprecated
     */
    const REASON_CUSTOMER_REDEMPTION = TBT_Rewards_Model_Transfer_Reason_Redemption::REASON_TYPE_ID;
    /**
     * @deprecated
     */
    const REASON_CUSTOMER_DISTRIBUTION = TBT_Rewards_Model_Transfer_Reason_Distribution::REASON_TYPE_ID;
    /**
     * @deprecated
     */
    const REASON_SYSTEM_ADJUSTMENT = TBT_Rewards_Model_Transfer_Reason_SystemAdjustment::REASON_TYPE_ID;
    /**
     * @deprecated
     */
    const REASON_FROM_CUSTOMER = TBT_Rewards_Model_Transfer_Reason_FromCustomer::REASON_TYPE_ID;
    /**
     * @deprecated
     */
    const REASON_TO_CUSTOMER = TBT_Rewards_Model_Transfer_Reason_ToCustomer::REASON_TYPE_ID;
    /**
     * @deprecated
     */
    const REASON_SYSTEM_REVOKED = TBT_Rewards_Model_Transfer_Reason_SystemRevoked::REASON_TYPE_ID;
    /**
     * @deprecated
     */
    const REASON_ADMIN_ADJUSTMENT = TBT_Rewards_Model_Transfer_Reason_AdminAdjustment::REASON_TYPE_ID;
    /**
     * @deprecated
     */
    const REASON_UNSPECIFIED = TBT_Rewards_Model_Transfer_Reason_Unspecified::REASON_TYPE_ID;

}
