<?php

/**
 * WDCA - Sweet Tooth
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS 
 * License, which extends the Open Software License (OSL 3.0).
 * The Sweet Tooth License is available at this URL: 
 *      https://www.sweettoothrewards.com/terms-of-service
 * The Open Software License is available at this URL: 
 *      http://opensource.org/licenses/osl-3.0.php
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
 * Theme Helper Data
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Helper_Theme extends Mage_Core_Helper_Abstract {

    /**
     * Tries to determine the real path of the design file
     * @param unknown_type $original_path
     * @param unknown_type $alternate_paths
     */
    public function getViewPath($original_path, $alternate_paths = array()) {
        $design = Mage::getBaseDir('design');

        // If the original path exists, then just do that. 
        $realpath = realpath($design . DS . $original_path);
        if (file_exists($realpath)) {
            return $original_path;
        }

        // Add defaults to list.
        $alternate_paths [] = str_replace(DS . 'base' . DS . 'default' . DS, DS . 'default' . DS . 'default' . DS, $original_path);
        $alternate_paths [] = str_replace(DS . 'default' . DS . 'default' . DS, DS . 'base' . DS . 'default' . DS, $original_path);

        // Check alternate paths
        foreach ($alternate_paths as $path) {
            $realpath = realpath($design . DS . $path);
            if (file_exists($realpath)) {
                return $path;
            }
        }
        return $original_path;
    }
    
    public function getPackageName()
    {
        return Mage::getSingleton('core/design_package')->getPackageName();    
    }
    
    public function getFrontendThemeName()
    {
        return Mage::getSingleton('core/design_package')->getTheme('frontend');
    }
    

}
