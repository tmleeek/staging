<?php

// making sure that correct API url is used if customer updates Sweet Tooth from an older version of ST, prior to 1.7.7.3
Mage::getConfig()->saveConfig(TBT_Rewards_Model_Platform_Instance::CONFIG_API_URL, "sweettoothapp.com");
// remove deprecated API configs
Mage::getConfig()->deleteConfig('rewards/developer/defaultapiurl');
Mage::getConfig()->deleteConfig('rewards/developer/defaultstagingurl');

// clean config cache
Mage::getConfig()->cleanCache();