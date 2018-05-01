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
 * Used to render the Platform/ApiKey field on the Config page which should only appear if
 * no Sweet Tooth Account has been connected
 * 
 * @category   TBT
 * @package    TBT_Rewards
 * @author     Sweet Tooth Team <contact@sweettoothhq.com>
 */
class TBT_Rewards_Block_System_Config_Platform_DevMode extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Renders the element into HTML, only if no Platform account has been connected
     * @param Varien_Data_Form_Element_Abstract $element The element to render
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $isConnected = Mage::getStoreConfig('rewards/platform/is_connected');
        if ($isConnected) {
            return "";
        }
        
        return parent::render($element);
    }
}
