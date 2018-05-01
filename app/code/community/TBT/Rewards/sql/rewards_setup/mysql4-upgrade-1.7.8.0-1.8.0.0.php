<?php
/**
 * This only adds an Admin notification about the new Reporting stuff.
 */
$installer = $this;

$installer->startSetup();

$title    = "New Feature - Sweet Tooth Reports!";
$desc     = "We are pleased to let you know that this new version of Sweet Tooth includes a great new feature - "
    . "<strong>Sweet Tooth Reports</strong>. <br/>Get a better insight into your <i>loyalty program</i> effectiveness"
    . " at a glance. Click <i>Read Details</i> for more.";
$url      = "https://support.sweettoothrewards.com/entries/46697836-Sweet-Tooth-Reports";
$severity = Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE;
$installer->createInstallNotice($title, $desc, $url, $severity);

$installer->endSetup();
