<?php

class MDN_Mpm_Model_System_Config_Backend_SellerReference extends Mage_Core_Model_Config_Data
{

    /**
     * {@inherit}
     * todo Check if the value has changed
     */
    protected function _afterSave()
    {
        foreach($channels = Mage::helper('Mpm/Carl')->getChannelsSubscribed() as $channel) {
            $channelCode = $channel->organization.'_'.$channel->locale.'_'.$channel->subset;
            $value = $this->getData('groups/repricing/fields/seller_id_'.$channelCode.'/value');
            Mage::helper('Mpm/Carl')->postRule(
                'CLIENT-DATA.'.strtoupper($channel->organization).'.'.strtoupper($channel->locale).'.SELLER-REFERENCE',
                sprintf('<?php return "%s";', $value),
                true
            );
        }
    }
}