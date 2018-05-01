<?php

abstract class MDN_Mpm_Model_Stat_Segment_Abstract {

    protected $_occurrences = null;
    protected $_productIds = array();

    public static $_productIdsWithSuccessFullLogs = null;

    public abstract function getSegmentType();

    protected abstract function _getOccurrences();

    protected abstract function _getProductIds($occurrence);

    public function getOccurrences()
    {
        if ($this->_occurrences == null)
        {
            $this->_occurrences = $this->_getOccurrences();
        }
        return $this->_occurrences;
    }

    public function getProductIds($occurrence)
    {
        if (!isset($this->_productIds[$occurrence]))
        {
            $this->_productIds[$occurrence] = $this->_getProductIds($occurrence);
        }
        return $this->_productIds[$occurrence];
    }

    public function truncate($channel)
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');

        $tableName = $resource->getTableName('Mpm/Stat');

        $sql = 'delete from '.$tableName.' where channel = "'.$channel.'" and segment_type = "'.$this->getSegmentType().'"';

        $writeConnection->query($sql);
    }

    public function getStats($channel, $occurrence, $competitor)
    {
        $productIds = $this->getProductIds($occurrence);
        //echo "\n".$channel." - ".$this->getSegmentType().' - '.$occurrence." - ".$competitor['seller_name'];

        $successfullProductIds = self::getProductIdsWithPricingLogs();
        $productIds = array_intersect($productIds, $successfullProductIds);

        if (count($productIds) == 0)
            return false;

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $data = array();
        $data['offers_count'] = $readConnection->fetchOne('select count(*) from mpm_product_offers where channel = "'.$channel.'"and seller_name = "'.addslashes($competitor['seller_name']).'" and product_id in ('.implode(',', $productIds).')');
        if ($data['offers_count'] == 0)
            return false;

        $data['worst_count'] = 0;
        $data['better_count'] = 0;
        $data['bbw_count'] = $readConnection->fetchOne('select count(*) from mpm_product_offers where channel = "'.$channel.'" and rank = 1 and seller_name = "'.addslashes($competitor['seller_name']).'" and product_id in ('.implode(',', $productIds).')');;

        return $data;
    }

    public static function getProductIdsWithPricingLogs()
    {
        if (self::$_productIdsWithSuccessFullLogs == null)
        {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $tableName = $resource->getTableName('mpm_pricing_log');
            self::$_productIdsWithSuccessFullLogs = $readConnection->fetchCol('select distinct product_id from '.$tableName.' where is_current = 1 and error = 0');
        }
        return self::$_productIdsWithSuccessFullLogs;
    }
}