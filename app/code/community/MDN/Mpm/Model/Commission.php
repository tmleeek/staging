<?php

class MDN_Mpm_Model_Commission extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('Mpm/Commission');
    }

    public function getForProductChannel($productId, $channel)
    {
        $result = Mage::getModel('Mpm/Commission')->getCollection()
                        ->addFieldToFilter('channel', $channel)
                        ->addFieldToFilter('product_id', $productId)
                        ->getFirstItem();
        if (!$result->getId())
        {
            $result->setchannel($channel);
            $result->setproduct_id($productId);
        }

        return $result;
    }


    public function insertOrUpdate($channel, $sku, $percent)
    {
        $productId = Mage::getSingleton('catalog/product')->getIdBySku($sku);

        $item = $this->getForProductChannel($productId, $channel);
        if (!$item)
            $item = Mage::getModel('Mpm/Product_Commission');

        $item->setpercent($percent);
        $item->save();

        return $item;

    }

    public function getPercent($productId, $channel)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $sql = "select percent from ".$resource->getTableName('mpm_commission')." where channel = '".$channel."' and product_id = ".$productId;
        $value = $readConnection->fetchOne($sql);

        //if no custom value, find it from rules
        if (!$value)
        {
            $rule = Mage::getModel('Mpm/Pricer')->getRulesForProduct($productId, $channel, MDN_Mpm_Model_Rule::kTypeCommission, true);
            if ($rule)
            {
                $value = $rule->getFormula(MDN_Mpm_Model_Rule::kFormulaCommission);
            }
        }

        return $value;
    }

}
