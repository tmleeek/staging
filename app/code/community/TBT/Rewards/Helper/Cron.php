<?php

/**
 * Sweet Tooth Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the SWEET TOOTH POINTS AND REWARDS
 * License, which extends the Open Software License (OSL 3.0).
 * The Sweet Tooth License is available at this URL:
 * https://www.sweettoothrewards.com/terms-of-service
 * The Open Software License is available at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * By adding to, editing, or in any way modifying this code, Sweet Tooth Inc. is
 * not held liable for any inconsistencies or abnormalities in the
 * behaviour of this code.
 * By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by Sweet Tooth Inc., outlined in the
 * provided Sweet Tooth License.
 * Upon discovery of modified code in the process of support, the Licensee
 * is still held accountable for any and all billable time Sweet Tooth Inc. spent
 * during the support process.
 * Sweet Tooth Inc. does not guarantee compatibility with any other framework extension.
 * Sweet Tooth Inc. is not responsible for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by another framework extension.
 * If you did not receive a copy of the license, please send an email to
 * contact@sweettoothhq.com or call 1-855-699-9322, so we can send you a copy
 * immediately.
 *
 * @copyright  Copyright (c) 2012 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper for cron related things
 * @category   TBT
 * @package    TBT_Rewards
 * @author     Sweet Tooth Team <contact@sweettoothhq.com>
 */
class TBT_Rewards_Helper_Cron extends Mage_Core_Helper_Abstract
{
    /**
     * Returns curent timestamp
     *
     * @return string Current timestamp
     */
    public function getCurrentTimestamp() {
        $timestamp = (string)time();
        return $timestamp;
    }

    /**
     * Returns timestamp saved in DB by 'testsweet_cron' cron job, used to check whether cron is running or not
     *
     * @return string timestamp
     */
    public function getCronTimestamp() {
        $timestamp = Mage::getStoreConfig('testsweet/crontest/timestamp');
        return $timestamp;
    }

    /**
     * Returns true if cron is running
     *
     * @return bool
     */
    public function isWorking() {
        $timestamp = $this->getCronTimestamp();
        if (empty($timestamp))
            return false;

        $seconds = $this->getCurrentTimestamp() - $timestamp;

        // if the timestamp is within 30 minuets return true
        return $seconds < (60 * 30);
    }

    /*
     *  Has any active Birthday Points rules
     *  @return boolean
     */
    public function hasBirthdayPointRules()
    {
        if (count(Mage::getSingleton('rewards/birthday_validator')->getApplicableRulesOnBirthday()) > 0) {
            return true;
        }

        return false;
    }

    /*
     *  Has any active catalog rules
     *  @return boolean
     */
    public function hasCatalogRules()
    {
        $collection = Mage::getModel('catalogrule/rule')
                        ->getResourceCollection()
                        ->addFieldToFilter("is_active", "1");

        if ($collection->count() > 0) {
            return true;
        }

        return false;
    }

    /*
     *  Has onhold rules
     *  @return boolean
     */
    public function hasOnholdRules()
    {
        $collection = Mage::getModel('rewards/special')
                        ->getResourceCollection()
                        ->addFieldToFilter("is_active", "1")
                        ->addFieldToFilter("onhold_duration", array("gt"=>"0"));

        if ($collection->count() > 0) {
            return true;
        }

        return false;
    }
}