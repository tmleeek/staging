<?php

class TBT_Rewardssocial_Model_Twitter_Follow_Observer extends Varien_Object
{
    /**
     * Runs before the customer behavior rule is saved and checks if option to
     * show Twitter Follow button and Twitter username are set in configuration section.
     *
     * @param  Varien_Event $observer [description]
     * @return $this
     */
    public function checkFollowSettings($observer)
    {
        $event = $observer->getEvent();
        $this->setRequest ( $observer->getControllerAction ()->getRequest () );
        $this->setResponse ( $observer->getControllerAction ()->getResponse () );
        $post_data = $this->getRequest ()->getPost ();
        if (empty($post_data)) {
            return $this;
        }

        if($post_data ['points_conditions'] != TBT_Rewardssocial_Model_Twitter_Follow_Special_Config::ACTION_CODE ) {
            return $this;
        }

        $rewards_kb_twitter_link = "https://support.sweettoothrewards.com/entries/24311472-Setting-Up-Twitter-Follow-Rewarding";

        $twitter_rewards_config_url = Mage::helper('rewardssocial')->getConfigUrl();
        $msg = Mage::helper('rewardssocial')->__("Twitter Follow rule is not yet completely configured. For more details please check our [rewards_kb_twitter_link]guide[/rewards_kb_twitter_link] or go to [twitter_follow_config_link]Twitter Rewards Settings[/twitter_follow_config_link] and:<br/>");
        $msg = Mage::helper('rewardssocial')->getTextWithLinks($msg, 'rewards_kb_twitter_link', $rewards_kb_twitter_link, array('_target' => '_rewards_kb_twitter'));
        $msg = Mage::helper('rewardssocial')->getTextWithLinks($msg, 'twitter_follow_config_link', $twitter_rewards_config_url);

        $showNotification = false;

        if (!Mage::helper('rewardssocial/twitter_follow')->isFollowButtonEnabled()) {
            $showNotification = true;
            $msg .= Mage::helper('rewardssocial')->__("- set 'Twitter Follow Button on Frontend' option to 'Show'.<br/>");
        }

        if (!Mage::helper('rewardssocial/twitter_follow')->getStoreTwitterUsername() ) {
            $showNotification = true;
            $msg .= Mage::helper('rewardssocial')->__("- set your store's twitter account username.<br/>");
        }

        $msg .= Mage::helper('rewardssocial')->__("Your rule was still saved.");

        if ($showNotification) {
            Mage::getSingleton('core/session')->addError($msg);
        }

        return $this;
    }
}