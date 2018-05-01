<?php

/**
 * Mysql Special
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_RewardsReferral_Model_Mysql4_Referral extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('rewardsref/referral', 'rewardsref_referral_id');
    }

    public function loadByEmail($customerEmail) {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('rewardsref/referral'))
                ->where('referral_email = ?', $customerEmail);
        $result = $this->_getReadAdapter()->fetchRow($select);
        if (!$result) {
            return array();
        }

        return $result;
    }

}