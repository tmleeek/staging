<?php

class TBT_Rewardssocial_Model_Facebook_Share_Observer extends Varien_Object
{
    /**
     * Runs before the customer behavior rule is saved and checks if all dependencies are met for rewarding users for
     * Facebook product shares.
     * @param  Varien_Event $observer
     * @return $this
     */
    public function checkFacebookShareSettings($observer)
    {
        $event = $observer->getEvent();
        $this->setRequest($observer->getControllerAction()->getRequest());
        $this->setResponse($observer->getControllerAction()->getResponse());
        $post_data = $this->getRequest()->getPost();
        if (empty($post_data)) {
            return $this;
        }

        if ($post_data['points_conditions'] != TBT_Rewardssocial_Model_Facebook_Share_Special_Config::ACTION_CODE ) {
            return $this;
        }

         $rewards_kb_share_link = "https://support.sweettoothrewards.com/entries/25943608-Set-Up-a-Rule-for-Facebook-Product-Share-Rewarding";

        $rewards_config_url = Mage::helper('rewardssocial')->getConfigUrl();
        $msg = Mage::helper('rewardssocial')->__("Facebook Product Share rule is not yet completely configured. For more details please check our [rewards_kb_share_link]guide[/rewards_kb_share_link] or go to [fb_share_config_link]<i>Sweet Tooth > Facebook Rewards Settings</i>[/fb_share_config_link] and:<br/>");
        $msg = Mage::helper('rewardssocial')->getTextWithLinks($msg, 'rewards_kb_share_link', $rewards_kb_share_link, array('_target' => '_rewards_fb_share'));
        $msg = Mage::helper('rewardssocial')->getTextWithLinks($msg, 'fb_share_config_link', $rewards_config_url);

        $showNotification = false;

        if (!Mage::helper('rewardssocial/facebook_config')->isFbProductShareButtonEnabled()) {
            $showNotification = true;
            $msg .= Mage::helper('rewardssocial')->__("- set 'Facebook Share Product Button On Frontend' option to 'Show'.<br/>");
        }

        $msg .= Mage::helper('rewardssocial')->__("Your rule was still saved.");

        if ($showNotification) {
            Mage::getSingleton('core/session')->addError($msg);
        }

        return $this;
    }
}