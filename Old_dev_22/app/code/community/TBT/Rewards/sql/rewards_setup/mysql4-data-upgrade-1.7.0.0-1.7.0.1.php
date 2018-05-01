<?php

/* This script should convert old-style API URLs into the new style that the SDK requires.
 * Previously we would set the full URL to which to point the SDK, but now we only set
 * the base domain, so we must convert our full URLs into base domains. */

// get all the saved API URLs (current, default live, and default staging)
$apiUrl            = Mage::getStoreConfig('rewards/developer/apiurl');
$defaultApiUrl     = Mage::getStoreConfig('rewards/developer/defaultapiurl');
$defaultStagingUrl = Mage::getStoreConfig('rewards/developer/defaultstagingurl');

// convert the current API URL into the base domain of the same URL
if (strpos($apiUrl, "sweettoothdev.com") !== false) {
    $apiUrl = "sweettoothdev.com";
} else if (strpos($apiUrl, "sweettoothapp.com") !== false) {
    $apiUrl = "sweettoothapp.com";
} else if (strpos($apiUrl, "sweettoothdevstaging.com") !== false) {
    $apiUrl = "sweettoothdevstaging.com";
} else if (strpos($apiUrl, "sweettoothstaging.com") !== false) {
    $apiUrl = "sweettoothstaging.com";
}

// convert the default "live" API URL into the base domain of the same URL
if (strpos($defaultApiUrl, "sweettoothdev.com") !== false) {
    $defaultApiUrl = "sweettoothdev.com";
} else if (strpos($defaultApiUrl, "sweettoothapp.com") !== false || is_null($defaultApiUrl)) {
    $defaultApiUrl = "sweettoothapp.com";
}

// convert the default staging API URL into the base domain of the same URL
if (strpos($defaultStagingUrl, "sweettoothdevstaging.com") !== false) {
    $defaultStagingUrl = "sweettoothdevstaging.com";
} else if (strpos($defaultStagingUrl, "sweettoothstaging.com") !== false || is_null($defaultStagingUrl)) {
    $defaultStagingUrl = "sweettoothstaging.com";
}

// save the converted URLs back into the config
Mage::getConfig()->saveConfig('rewards/developer/apiurl', $apiUrl);
Mage::getConfig()->saveConfig('rewards/developer/defaultapiurl', $defaultApiUrl);
Mage::getConfig()->saveConfig('rewards/developer/defaultstagingurl', $defaultStagingUrl);


/* This script should get a collection of all test transfers, based on the existence
 * of the [DEVELOPER MODE] prefix on the transfers' Comments.  It will then set the is_dev_mode
 * flag of the transfer to true, so that it can be reconciled later. */
$this->attemptQuery("
    UPDATE `{$this->getTable('rewards/transfer')}`
    SET `is_dev_mode` = '1'
    WHERE `comments` LIKE '[DEVELOPER MODE]%';
");


// ensure that the next call on the API will pick up the config changes
Mage::getConfig()->cleanCache();
