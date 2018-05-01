<?php

/**
 *
 *
 */
class MDN_Mpm_Model_Product_Offer extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('Mpm/Product_Offer');
    }

    public function isMe()
    {
        return ($this->getseller_name() == Mage::getStoreConfig('mpm/repricing/seller_id_'.$this->getchannel()));
    }

    protected function _beforeSave() {
        parent::_beforeSave();

        $this->seller_name = trim($this->seller_name, '" ');

        if ($this->isMe())
            $this->setis_me(1);
    }

    public function deleteForOneProduct($productId)
    {
        $offers = Mage::getModel('Mpm/Product_Offer')
                        ->getCollection()
                        ->addFieldToFilter('product_id', $productId);
        foreach($offers as $offer)
        {
            $offer->delete();
        }
    }



    /**
     * Sort offers by total price
     * @param $a
     * @param $b
     * @return int
     */
    public static function sortOffersPerPrice($a, $b)
    {
        $al = strtolower($a->gettotal());
        $bl = strtolower($b->gettotal());
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }

    /**
     * Sort offers by rank
     * @param $a
     * @param $b
     * @return int
     */
    public static function sortOffersPerRank($a, $b)
    {
        $al = strtolower($a->getRank());
        $bl = strtolower($b->getRank());
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }

    /**
     * Return all competitors for one channel
     * @param $channel
     */
    public function getAllCompetitors($channel)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $tableName = $resource->getTableName('Mpm/Product_Offer');

        $sql = 'select distinct seller_id, seller_name from '.$tableName.' where channel = "'.$channel.'"';

        return $readConnection->fetchAll($sql);
    }
}
