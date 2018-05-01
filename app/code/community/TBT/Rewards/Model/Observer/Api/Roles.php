<?php

class TBT_Rewards_Model_Observer_Api_Roles extends Varien_Object
{
    const CONFIG_XPATH_ROLE_ID              = 'rewards/platform/soap/role_id';
    const CONFIG_XPATH_PLATFORM_SYNC_ENABLE = 'rewards/platform_sync/enable';

    /**
     * Observers 'model_delete_after' event and checks if the API role deleted
     * is ours, in that case disables Platform Sync configuration option and resets
     * our saved API role
     *
     * @param  Varien_Event $observer
     * @return $this
     */
    public function afterDelete($observer)
    {
        $apiRoles = $observer->getEvent()->getObject();

        if (!$apiRoles) {
            return $this;
        }

        if (!($apiRoles instanceof Mage_Api_Model_Roles)) {
            return $this;
        }

        if (Mage::getStoreConfig(self::CONFIG_XPATH_ROLE_ID) == $apiRoles->getRoleId()) {
            Mage::getConfig()->saveConfig(self::CONFIG_XPATH_ROLE_ID, null);
            Mage::getConfig()->saveConfig(self::CONFIG_XPATH_PLATFORM_SYNC_ENABLE, 0);
        }

        return $this;
    }
}