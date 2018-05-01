<?php

class TBT_Rewards_Helper_Poll_Config extends Mage_Core_Helper_Abstract
{
    /**
     * @param unknown_type $store
     * @return Ambigous <mixed, string, NULL>
     */
    public function getInitialTransferStatusAfterPoll($store = null)
    {
        return Mage::getStoreConfig ('rewards/InitialTransferStatus/AfterPoll', $store);
    }
}