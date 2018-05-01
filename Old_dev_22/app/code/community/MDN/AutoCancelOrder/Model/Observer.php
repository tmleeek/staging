<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_AutoCancelOrder_Model_Observer {
	/**
	 * Called by cron to execute tasks
	 *
	 */
	public function ExecuteTasks() { 
		// if cron is activate then execute tasks
        if( Mage::getStoreConfig('autocancelorder/general/enable_cron')){
           mage::helper('AutoCancelOrder')->apply();
        }
    }
}
	