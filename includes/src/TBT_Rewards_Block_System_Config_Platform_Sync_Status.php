<?php

class TBT_Rewards_Block_System_Config_Platform_Sync_Status extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected $_account = null;

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        /** @var $webservice TBT_Rewards_Model_Platform_WebService */
        $webservice = Mage::getModel('rewards/platform_webService');

        $isConnected = Mage::getStoreConfig('rewards/platform/is_connected');
        if (!$isConnected) {
            return "<b>Can't Enable Yet</b>:  Before you can enable this feature, you have to connect your account.";
        }

        if (!$webservice->isRoleCreated() || !Mage::getStoreConfig('rewards/platform_sync/enable')) {
            return "<b>Disabled</b>:  Please enable this feature above to start syncing data.";
        }

        return '<b><font color=green>Syncing</font></b>';
    }
}
