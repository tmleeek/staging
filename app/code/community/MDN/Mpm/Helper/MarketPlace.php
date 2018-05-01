<?php

class MDN_Mpm_Helper_MarketPlace extends Mage_Core_Helper_Abstract {


    public function getMpReference($channel, $product) {

        if ($this->marketPlaceExtensionIsInstalled())
            return $this->getMpReferenceFromBmsExtension($channel, $product);

        if ($this->m2eProIsInstalled())
            return $this->getMpReferenceFromM2ePro($channel, $product);

        $mappedAttribute = Mage::getStoreConfig('mpm/channel_references/' . $channel);
        if ($mappedAttribute)
            return $product->getData($mappedAttribute);

    }

    /**
     * Check if boostmyshop extension is installed
     *
     * @return bool
     */
    public function marketPlaceExtensionIsInstalled()
    {
        if (Mage::getStoreConfig('marketplace/logs/max') > 1)
        {
            $countryModel = Mage::getModel('MarketPlace/Countries');
            if ($countryModel)
                return true;
        }
        return false;
    }

    /**
     * check if m2epro is installed
     *
     * @return bool
     */
    public function m2eProIsInstalled()
    {
        $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $prefix = Mage::getConfig()->getTablePrefix();
        $query = 'select count(*) from '.$prefix.'core_resource where code = "M2ePro_setup"';
        $result = $readConnection->fetchOne($query);

        return ($result == 1);

    }

    protected function getMpReferenceFromM2ePro($marketplaceId, $product)
    {
        $t = explode('_', $marketplaceId);
        if ($t[0] != 'amazon')
            return;

        $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $prefix = Mage::getConfig()->getTablePrefix();

        $query = 'select
                      MAX(general_id)
                  from
                      '.$prefix.'m2epro_amazon_listing_product
                      inner join '.$prefix.'m2epro_listing_product on (id = '.$prefix.'m2epro_amazon_listing_product.listing_product_id)
                  where
                        product_id = '.$product->getId().'';
        $result = $readConnection->fetchOne($query);

        return $result;
    }

    protected function getMpReferenceFromBmsExtension($marketplaceId, $product)
    {
        $t = explode('_', $marketplaceId);
        if (count($t) > 0)
            $marketplaceId = $t[0];
        else
            return '';

        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $prefix = Mage::getConfig()->getTablePrefix();

        $select = $read->select()
            ->from($prefix . 'market_place_data', array('mp_reference'))
            ->join(

                $prefix . 'market_place_accounts_countries',
                'mp_marketplace_id = mpac_id',
                array()
            )
            ->join(

                $prefix . 'market_place_accounts',
                'mpac_account_id = mpa_id',
                array()
            )
            ->where('mp_product_id = ?', $product->getid())
            ->where('mpa_mp = ?', $marketplaceId);

        $res = $select->query()->fetchAll();


        if ((count($res) > 0))
        {
            return $res[0]['mp_reference'];
        }
    }

}