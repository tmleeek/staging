<?php
class TBT_Rewardssocial_Model_Twitter_Tweet_Special_Config
    extends TBT_Rewards_Model_Special_Configabstract
{
    const ACTION_CODE = 'social_twitter_tweet';
    
    public function _construct()
    {
        return parent::_construct();
        $this->setCaption(Mage::helper('rewardssocial')->__("Twitter Tweet"));
        $this->setDescription(Mage::helper('rewardssocial')->__("Customer will get points when they tweet a page with Twitter."));
        $this->setCode('social_twitter_tweet');
    }
    
    public function visitAdminActions(&$fieldset)
    {
        return $this;
    }
    
    public function visitAdminConditions(&$fieldset)
    {
        return $this;
    }
    
    public function getNewCustomerConditions()
    {
        return array(
            self::ACTION_CODE => Mage::helper('rewardssocial')->__("Tweets a page with Twitter")
        );
    }
    
    public function getNewActions()
    {
        return array ();
    }
    
    public function getAdminFormScripts()
    {
        return array ();
    }
    
    public function getAdminFormInitScripts()
    {
        return array ();
    }
}
