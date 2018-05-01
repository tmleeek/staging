<?php

/**
 *
 *
 */
class MDN_Mpm_Model_Product_Setting extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('Mpm/Product_Setting');
    }

    public function getForProductChannel($productId, $channel)
    {
        $result = Mage::getModel('Mpm/Product_Setting')->getCollection()->addFieldToFilter('channel', $channel)->addFieldToFilter('product_id', $productId)->getFirstItem();;
        if (!$result->getId())
        {
            $result->setchannel($channel);
            $result->setproduct_id($productId);
            $result->setuse_config_behaviour(1);
            $result->setuse_config_price(1);
            $result->setuse_config_rule(1);
        }
        return $result;
    }

    public function initOrUpdate($productId, $channel, $useConfigBehaviour, $behaviour, $useConfigPrice, $price, $useConfigRule, $rule)
    {
        $item = $this->getForProductChannel($productId, $channel);
        if (!$item)
            $item = Mage::getModel('Mpm/Product_Setting');

        $item->setproduct_id($productId);
        $item->setchannel($channel);
        $item->setbehaviour($behaviour);
        $item->setuse_config_behaviour($useConfigBehaviour);
        $item->setprice($price);
        $item->setuse_config_price($useConfigPrice);
        $item->setrule($rule);
        $item->setuse_config_rule($useConfigRule);

        $item->save();

        return $item;
    }

    public function updateField($productId, $channel, $field, $value)
    {
        $item = $this->getForProductChannel($productId, $channel);
        if (!$item)
            $item = Mage::getModel('Mpm/Product_Setting');
        $item->setData($field, $value);
        $item->setData('use_config_'.$field, 0);
        $item->save();
    }

    public function getBehaviour()
    {
        if (!$this->getuse_config_behaviour())
            return $this->getData('behaviour');
        else
            return Mage::getStoreConfig('mpm/repricing/behaviour_'.$this->getchannel());
    }


    /**
     * After save
     */
    protected function _afterSave() {
        parent::_afterSave();

        $fields = array('behaviour', 'use_config_behaviour', 'price', 'use_config_price', 'rule', 'use_config_rule');
        foreach($fields as $field)
        {
            if ($this->getData($field) != $this->getOrigData($field))
            {
                Mage::helper('Mpm')->log('Settings for product id '.$this->getProductId().' have changed, launch repricing');
                Mage::getModel('Mpm/Pricer')->processProduct($this->getProductId(), $this->getChannel());
                return;
            }
        }
    }

}