<?php

class TBT_Rewardssocial_Block_Facebook_Share_Purchase_Button extends TBT_Rewardssocial_Block_Purchase_Share_Abstract
    implements TBT_Rewardssocial_Block_Facebook_Share_Button_Interface
{
    protected $_buttonType = 'Facebook';

    public function getOnClickAction()
    {
        $action = "fbShareAction(this, {url: '{$this->getProductUrl()}', eventName: 'fb_purchase_share:response'}); return false;";
        return $action;
    }

    /**
     * Checks if counter is enabled. Facebook Share has no counter.
     * @return boolean False, as Facebook share has no counter.
     */
    public function isCounterEnabled()
    {
        return false;
    }
}
