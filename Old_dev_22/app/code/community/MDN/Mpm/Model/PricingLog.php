<?php

class MDN_Mpm_Model_PricingLog extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('Mpm/PricingLog');
    }


    /**
     * @param $productId
     * @param $channel
     * @param $ruleId
     * @param $formula
     * @param $finalPrice
     * @param int $error
     */
    public function addLog($productId, $channel, $ruleId, $formula, $finalPrice, $status, $behaviour, $error = 0, $debug = '', $bbwName, $bbwPrice, $myRank, $margin, $marginForBbw, $finalCost, $commission)
    {
        $this->unsetCurrent($productId, $channel);

        $log = Mage::getModel('Mpm/PricingLog')
            ->setproduct_id($productId)
            ->setchannel($channel)
            ->setrule_id($ruleId)
            ->setformula($formula)
            ->setstatus(($error ? MDN_Mpm_Model_Pricer::kPricingStatusError : $status))
            ->setbehaviour($behaviour)
            ->setis_current(1)
            ->setfinal_price($finalPrice)
            ->seterror($error)
            ->setdebug($debug)
            ->setbbw_name($bbwName)
            ->setbbw_price($bbwPrice)
            ->setmy_rank($myRank)
            ->setmargin($margin)
            ->setmargin_for_bbw($marginForBbw)
            ->setfinal_cost($finalCost)
            ->setcommission($commission)
            ->setcreated_at(date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time())))
            ->save();
        return $log;
    }

    protected function unsetCurrent($productId, $channel)
    {

        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $tableName = $resource->getTableName('mpm_pricing_log');
        $query = "update  ".$tableName." set is_current = 0 WHERE is_current = 1 and product_id = ".(int)$productId." and channel = '".$channel."'";
        $writeConnection->query($query);

    }

    public function getCurrentLog($productId, $channel)
    {
        $item = Mage::getModel('Mpm/PricingLog')->getCollection()->addFieldToFilter('product_id', $productId)->addFieldToFilter('channel', $channel)->addFieldToFilter('is_current', 1)->getFirstItem();
        if ($item->getId())
            return $item;
        else
            return false;
    }

}