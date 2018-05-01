<?php

class TBT_Milestone_Helper_Data extends Mage_Core_Helper_Abstract
{  
    /**
     * Accepts a timestamp in local timezone, converts the start of that day (midnight) from local time to utc time. 
     * @param int $localTimestamp (optional). The timestampt to convert. Will use now if not supplied
     * @return int
     */
    public function getLocalMidnightInUtcTimestamp($localTimestamp = null)
    {
        $localTimestamp = !empty($localTimestamp) ? $localTimestamp : $this->getLocalTimestamp();
        $midnight = $this->getNormalizedDateString($localTimestamp);
        return $this->getUtcTimestamp($midnight);       
    } 
    
    /**
     * Returns a UTC date string in MySQL format for the given number of days in the past.
     *
     * @param int $threshold. (X). Number of days before startDate
     * @param string|int $startDate. (optional) The date (in UTC) we should start counting backwards from.
     *                               Will use 12am today (local time) if none specified.
     *
     * @return string date in format supported by MySql
     */
    public function getDateStringXDaysAgo($numberOfDaysAgo, $startDate = null)
    {
        $numberOfDaysAgo = max(0, $numberOfDaysAgo);
        if (empty($startDate)){
            $startDate = $this->getLocalMidnightInUtcTimestamp();
        }
    
        if (!is_numeric($startDate)){
            $startDate = strtotime($startDate);
        }
    
        $targetDate = strtotime("-{$numberOfDaysAgo} day", $startDate);
    
        return $this->getMySqlDateString($targetDate);
    }
         
    /**
     * Accepts any date string and returns date in format of "Y-m-d H:i:s" ideal for db queries.
     * Ignores timezone, so if you're using this for DB queries, you should pass in UTC time.
     *
     * @param int|string $dateString any date string parsable by php's strtotime() function. Will use UTC time if not specified
     * @return string db formatted date.
     */    
    public function getMySqlDateString($dateString = null, $format = null)
    {
        $format = !empty ($format) ? $format : "Y-m-d H:i:s";
        
        if (empty($dateString)){
            $timestamp = $this->getUtcTimestamp();
        } else if (is_numeric($dateString)){
            $timestamp = $dateString;
        } else {
            $timestamp = strtotime($dateString);
        }
                
        return date($format, $timestamp);
    }

    /**
     * Accepts any date string and returns date in format of "Y-m-d" (reset to midnight) for db queries.
     * Ignores timezone, so if you're using this for DB queries, you should pass in UTC time.
     *
     * @param int|string $dateString any date string parsable by php's strtotime() function. Will use UTC time if not specified
     * @return string db formatted date.
     */
    public function getNormalizedDateString($dateString = null)
    {
        return $this->getMySqlDateString($dateString, "Y-m-d");
    }

    /**
     * Accepts a utc timestamp and returns a timestamp in local store timezone.
     * UTC time is what is stored in the DB.
     *
     * @see Mage_Core_Model_Date::timestamp()
     * @param int|string $input date in UTC/GMT timezone
     * @return int
     */
    public function getLocalTimestamp($utcTimestamp = null)
    {
        $utcTimestamp = empty($utcTimestamp) ? null : $utcTimestamp;
        return Mage::getModel('core/date')->timestamp($utcTimestamp);
    }    
    /**
     * Accepts a local timestamp (in the store time-zone) and returns a UTC timestamp.
     * UTC time is what is stored in the DB.
     * 
     * @see Mage_Core_Model_Date::gmtTimestamp()
     * @param int|string $localTimestamp date in current timezone 
     * @return int
     */
    public function getUtcTimestamp($localTimestamp = null)
    {
        $localTimestamp = empty($localTimestamp) ? null : $localTimestamp;
        return Mage::getModel('core/date')->gmtTimestamp($localTimestamp);
    }    
        
    /**
     * @return true if we're currently in the back-end. False otherwise.
     */
    public function isInAdminMode()
    {
        return Mage::app()->getStore()->isAdmin();
    }
    
    /**
     * Given an array of website ids, will return an array of store ids for all websites
     * @param array|int $websiteIds
     * @return array
     */
    public function getStoreIdsFromWebsites($websiteIds)
    {
        $websiteIds = is_array($websiteIds) ? $websiteIds : array($websiteIds);
        $storeIds = array();
        foreach ($websiteIds as $websiteId){
            $newStoreIds = Mage::app()->getWebsite($websiteId)->getStoreIds();
            $storeIds = array_merge($storeIds, $newStoreIds);
        }
        
        return $storeIds;
    }
    
}
