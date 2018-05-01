<?php

$installer = $this;
$installer->startSetup();

// If TBT_Rewardssocial installed first time skip this
if (! $installer->getIsFirstInstall()) {
    $safeMode = ini_get('safe_mode');
    $disabledFunctions = explode(',', ini_get('disable_functions'));

    $canUse = array(
        'file_exists' => array_search('file_exists', $disabledFunctions) === false,
        'copy' => array_search('copy', $disabledFunctions) === false
    );

    $allOverwritesSuccessful = true;

    if ($safeMode || !$canUse['file_exists'] || !$canUse['copy']) {
        // notify merchant of failure
        $installer->createInstallNotice("Could not update TBT_Rewardssocial.csv",
            "System commands have been disabled on your system, so we could not update Sweet Tooth's " .
                "TBT_Rewardssocial.csv file.  Click " .
                "<a href='https://support.sweettoothrewards.com/entries/21405842-rewardssocial-csv-updated-in-1-6-1#Could_not_update'>here</a> " .
                "for more information.",
            "https://support.sweettoothrewards.com/entries/21405842-rewardssocial-csv-updated-in-1-6-1#Could_not_update",
            Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR
        );
        $allOverwritesSuccessful = false;
    } else {
        $locales = scandir(Mage::getRoot() . DS . "locale");
        foreach ($locales as $locale) {
            if ($locale === "." || $locale === "..") {
                continue;
            }

            $isSafeToOverwrite = true;
            $source = Mage::getRoot() . DS . "locale" . DS . $locale . DS . "TBT_Rewardssocial.csv";
            $dest = Mage::getRoot() . DS . "locale" . DS . $locale . DS . "TBT_Rewardssocial.ST-backup.csv";
            if (file_exists($source)) {
                try {
                    $isSafeToOverwrite = copy($source, $dest);
                } catch (Exception $ex) {
                    $isSafeToOverwrite = false;
                }
            }

            if (!$isSafeToOverwrite) {
                // notify merchant of failure
                $installer->createInstallNotice("Could not backup TBT_Rewardssocial.csv ({$locale})",
                    "Sweet Tooth does not have write permissions to backup your {$locale}/TBT_Rewardssocial.csv " .
                        "file, so we could not update it.  Click " .
                        "<a href='https://support.sweettoothrewards.com/entries/21405842-rewardssocial-csv-updated-in-1-6-1#Could_not_backup'>here</a> " .
                        "for more information.",
                    "https://support.sweettoothrewards.com/entries/21405842-rewardssocial-csv-updated-in-1-6-1#Could_not_backup",
                    Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR
                );
                $allOverwritesSuccessful = false;
            } else {
                $source = Mage::getRoot() . DS . "locale" . DS . $locale . DS . "TBT_Rewards.csv";
                $dest = Mage::getRoot() . DS . "locale" . DS . $locale . DS . "TBT_Rewardssocial.csv";
                if (file_exists($source)) {
                    $isOverwriteSuccessful = true;
                    try {
                        $isOverwriteSuccessful = copy($source, $dest);
                    } catch (Exception $ex) {
                        $isOverwriteSuccessful = false;
                    }
                    if (!$isOverwriteSuccessful) {
                        // notify merchant of failure
                        $installer->createInstallNotice("Could not overwrite TBT_Rewardssocial.csv ({$locale})",
                            "Sweet Tooth does not have permissions to overwrite your {$locale}/TBT_Rewardssocial.csv " .
                                "file, so we could not update it.  Click " .
                                "<a href='https://support.sweettoothrewards.com/entries/21405842-rewardssocial-csv-updated-in-1-6-1#Could_not_overwrite'>here</a> " .
                                "for more information.",
                            "https://support.sweettoothrewards.com/entries/21405842-rewardssocial-csv-updated-in-1-6-1#Could_not_overwrite",
                            Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR
                        );
                        $allOverwritesSuccessful = false;
                    }
                }
            }
        }
    }

    if ($allOverwritesSuccessful) {
        $installer->createInstallNotice("TBT_Rewardssocial.csv has been replaced",
            "The TBT_Rewardssocial.csv files have been replaced by their corresponding TBT_Rewards.csv files in order to " .
                "retain the translations for the TBT_Rewardssocial module which have, until now, been entered in the " .
                "TBT_Rewards csv.  If a TBT_Rewardssocial.csv file already existed, it has been backed up to " .
                "TBT_Rewardssocial.ST-backup.csv",
            "https://support.sweettoothrewards.com/entries/21405842-rewardssocial-csv-updated-in-1-6-1",
            Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE
        );
    }
}

$installer->endSetup();

?>
