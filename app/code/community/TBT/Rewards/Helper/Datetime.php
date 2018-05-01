<?php

/**
 * WDCA - Sweet Tooth
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS 
 * License, which extends the Open Software License (OSL 3.0).

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
 * Helper Data
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Helper_Datetime extends Mage_Core_Helper_Abstract {
	
	/**
	 * Fetches the current date in the format 'Y-m-d'
	 * and based on the currently loaded store.    
	 * @nelkaake Moved on Wednesday September 22, 2010: moved from Data helper
	 *
	 * @return string
	 */
	public function now($dayOnly = TRUE) {
		return date ( $dayOnly ? 'Y-m-d' : 'Y-m-d H:i:s', Mage::app ()->getLocale ()->storeTimeStamp () );
	}
	
	/**
	 * Fetches tomorrow's date in the format 'Y-m-d'
	 * and based on the currently loaded store.    
	 * @nelkaake Moved on Wednesday September 22, 2010: moved from Data helper
	 *
	 * @return string
	 */
	public function tomorrow($dayOnly = TRUE) {
		return date ( $dayOnly ? 'Y-m-d' : 'Y-m-d H:i:s', Mage::app ()->getLocale ()->storeTimeStamp () + 86400 );
	}
	
	/**
	 * Fetches yesterday's date in the format 'Y-m-d'
	 * and based on the currently loaded store.    
	 * @nelkaake Moved on Wednesday September 22, 2010: moved from Data helper
	 *
	 * @return string
	 */
	public function yesterday($dayOnly = TRUE) {
		return date ( $dayOnly ? 'Y-m-d' : 'Y-m-d H:i:s', Mage::app ()->getLocale ()->storeTimeStamp () - 86400 );
	}
	
	// @nelkaake Moved on Wednesday September 22, 2010: moved from Data helper
	public function getCurrentFromDate() {
		$fromDate = mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ) - 1 );
		if (is_string ( $fromDate )) {
			$fromDate = strtotime ( $fromDate );
		}
		return $fromDate;
	}
	
	// @nelkaake Moved on Wednesday September 22, 2010: moved from Data helper
	public function getCurrentToDate() {
		$toDate = mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ) + 1 );
		if (is_string ( $toDate )) {
			$toDate = strtotime ( $toDate );
		}
		return $toDate;
	}
	
    /**
     * Covert seconds into Days Hours Minutes Seconds
     * @param int $seconds seconds
     * @param boolean $returnString
     * @return Format string or array
     */
    public function secondsToDayFormat($seconds)
    {
        $secondsInAMinute = 60;
        $secondsInAnHour = 60 * $secondsInAMinute;
        $secondsInADay = 24 * $secondsInAnHour;
        $days = $hours = $minutes = 0;
        $remainSec = $seconds;

		// Check input seconds has days
        if ($seconds >= $secondsInADay) {
            $days = floor($seconds / $secondsInADay);
            $remainSec = $seconds - ($days * $secondsInADay);
        }

		// Check remaining seconds has hours
        if ($remainSec >= $secondsInAnHour) {
            $hours = floor($remainSec / $secondsInAnHour);
            $remainSec = $remainSec - ($hours * $secondsInAnHour);
        }

		// Check remaining seconds has minute
        if ($remainSec >= $secondsInAMinute) {
            $minutes = floor($remainSec / $secondsInAMinute);
            $remainSec = $remainSec - ($minutes * $secondsInAMinute);
        }

        return array(
	                'd' => (int) $days,
	                'h' => (int) $hours,
	                'm' => (int) $minutes,
	                's' => (int) $remainSec,
               );
    }
}