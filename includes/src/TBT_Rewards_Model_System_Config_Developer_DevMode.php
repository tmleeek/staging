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
 * Used to save the Developer/DevMode field on the Config page to its non-default config path
 * @category   TBT
 * @package    TBT_Rewards
 * @author     Sweet Tooth Team <contact@sweettoothhq.com>
 */
class TBT_Rewards_Model_System_Config_Developer_DevMode extends Mage_Core_Model_Config_Data
{
    /**
     * Save the Developer Mode flag to its (custom) config path
     *
     * @return self
     */
    public function save()
    {
        if ($this->isValueChanged()) {
            Mage::getModel('core/config')->saveConfig('rewards/platform/dev_mode', $this->getValue());
        }

        return parent::save();
    }
}
