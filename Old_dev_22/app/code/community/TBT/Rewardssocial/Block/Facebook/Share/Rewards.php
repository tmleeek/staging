<?php

class TBT_Rewardssocial_Block_Facebook_Share_Rewards extends TBT_Rewardssocial_Block_Abstract
{
    public function _toHtml()
    {
        // TODO: check if rule exists
        if (Mage::helper('rewardssocial/facebook_config')->isFbProductShareEnabled()) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * Returns Facebook product share processing controller URL.
     * @return string
     */
    public function getProcessingUrl()
    {
        return $this->getUrl('rewardssocial/index/processFbProductShare');
    }
}
