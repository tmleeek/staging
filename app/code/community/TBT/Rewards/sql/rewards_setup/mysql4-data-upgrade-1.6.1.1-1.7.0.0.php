<?php

require_once(Mage::getBaseDir('lib'). DS. 'SweetTooth'. DS .'SweetTooth.php');

$key = Mage::getStoreConfig('rewards/registration/license_key');
if (!$key) {
    Mage::helper('rewards')->log("ST License Key not found. Merchant can create or connect their account in the Sweet Tooth configuration.");
} else {
    try {
        $client = new SweetTooth("", "");
        $client->setBaseDomain(Mage::getStoreConfig(TBT_Rewards_Model_Platform_Instance::CONFIG_API_URL));
        $account = $client->account()->get($key);
        Mage::getModel('core/config')->saveConfig('rewards/platform/apisubdomain', $account['username']);


        $client = new SweetTooth("", "");
        $client->setBaseDomain(Mage::getStoreConfig(TBT_Rewards_Model_Platform_Instance::CONFIG_API_URL));
        $client->setSubdomain($account['username']);
        $channel = $client->channel()->st_key($key);
        Mage::getModel('core/config')->saveConfig('rewards/platform/apikey', $channel['api_key']);
        Mage::getModel('core/config')->saveConfig('rewards/platform/secretkey', Mage::helper('core')->encrypt($channel['api_secret']));
        Mage::getModel('core/config')->saveConfig('rewards/platform/is_connected', "1");
        Mage::getModel('core/config')->saveConfig('rewards/platform/dev_mode', "0");

        $msgTitle = "Your Sweet Tooth account has been connected.";
        $msgDesc = "We have automatically converted your existing Sweet Tooth license into a brand new " .
            "Sweet Tooth App account!  Check out the <i>Sweet Tooth Account Details</i> section of the " .
            "<b>Rewards &gt; Configuration &gt; Other Configuration</b> page for more details.";
        Mage::helper( 'rewards/mysql4_install' )->createInstallNotice( $msgTitle, $msgDesc );

    } catch (Exception $e) {
        Mage::helper('rewards')->log(Mage::helper('rewards')->__("Problem obtaining channel key, stack trace: " . $e->getMessage() . ": \r\n" . $e->getTraceAsString()));
    }
}

// Clear cache.
Mage::getConfig()->cleanCache();