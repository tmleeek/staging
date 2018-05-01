<?php

/**
 * @see TBT_Common_Model_Resource_Mysql4_Setup
 */
class TBT_Rewards_Model_Mysql4_Setup extends TBT_Common_Model_Resource_Mysql4_Setup
{

    /**
     * Runs after additional data update scripts have been executed
     * @return TBT_Rewards_Model_Mysql4_Setup
     */
    protected function _postApplyData()
    {
        parent::_postApplyData();
        $this->_updateVersionInfo();

        return $this;
    }

    /**
     * If this store is connected to a Platform account, this method will send the latest
     * version information about Magento and Sweet Tooth up to Platform.
     * @return TBT_Rewards_Model_Mysql4_Setup
     */
    protected function _updateVersionInfo()
    {
        $apiKey = Mage::getStoreConfig('rewards/platform/apikey');
        if (!$apiKey) {
            return $this;
        }

        $channelData['channel_type'] = 'magento';
        $channelData['channel_version'] = (string) Mage::getConfig()->getNode('modules/TBT_Rewards/version');
        $channelData['platform_version'] = Mage::getVersion();
        $channelData['frontend_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $channelData['backend_url'] = Mage::getUrl('adminhtml');

        try {
            $platform = Mage::getModel('rewards/platform_instance');
            $platform->channel()->update($channelData);
        } catch (Exception $ex) {

        }

        return $this;
    }

    /**
     * This method will create a backend notification regarding a successful
     * Sweet Tooth installation, with the appropriate version number.
     * @return TBT_Rewards_Model_Mysql4_Setup
     */
    protected function _createSuccessfulUpdateNotice()
    {
        $version = Mage::getConfig()->getNode('modules/TBT_Rewards/version');
        $msgTitle = "Sweet Tooth was successfully updated to v{$version}!";
        $msgDesc = "Sweet Tooth was successfully updated to v{$version} on your store.";
        $this->createInstallNotice($msgTitle, $msgDesc);

        return $this;
    }

    /**
     * This method will create a backend notification regarding a successful
     * Sweet Tooth installation, with the appropriate version number.
     * @return TBT_Rewards_Model_Mysql4_Setup
     */
    protected function _createSuccessfulInstallNotice()
    {
        $version = Mage::getConfig()->getNode('modules/TBT_Rewards/version');
        $msgTitle = "Sweet Tooth v{$version} was successfully installed!";
        $msgDesc = "Sweet Tooth v{$version} was successfully installed on your store.";
        $this->createInstallNotice($msgTitle, $msgDesc);

        return $this;
    }
}
