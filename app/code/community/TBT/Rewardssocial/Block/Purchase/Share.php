<?php

class TBT_Rewardssocial_Block_Purchase_Share extends TBT_Rewardssocial_Block_Abstract
{
    public function getShowSharePurchase()
    {
        if (!Mage::helper('rewardssocial/purchase_config')->isSharePurchaseFeatureEnabled()) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve the proccessing action controller url.
     * @return string
     */
    public function getProcessingUrl()
    {
        return $this->getUrl('rewardssocial/index/processPurchaseShare');
    }
}
