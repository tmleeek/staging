<?php

class TBT_Rewards_Model_Observer_Api_User extends Varien_Object
{
    const CONFIG_XPATH_USER_ID              = 'rewards/platform/soap/user_id';
    const CONFIG_XPATH_PLATFORM_SYNC_ENABLE = 'rewards/platform_sync/enable';

    /**
     * Observers 'api_user_delete_after' event and checks if the API user deleted
     * is ours, in that case disables Platform Sync configuration option and resets
     * our saved API user
     *
     * @param  Varien_Event $observer
     * @return $this
     */
    public function afterDelete($observer)
    {
        $apiUser = $observer->getEvent()->getObject();
        if (!$apiUser) {
            return $this;
        }

        if (Mage::getStoreConfig(self::CONFIG_XPATH_USER_ID) == $apiUser->getUserId()) {
            Mage::getConfig()->saveConfig(self::CONFIG_XPATH_USER_ID, null);
            Mage::getConfig()->saveConfig(self::CONFIG_XPATH_PLATFORM_SYNC_ENABLE, 0);
        }

        return $this;
    }
}