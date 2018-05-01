<?php

// Revalidate each give_by_profit rule if one exists.
$profitRules = Mage::getModel('rewards/catalogrule_rule')->getCollection();
$profitRules->addFieldToFilter('points_action', 'give_by_profit');
if ($profitRules->getSize() > 0) {
    $rule = $profitRules->getFirstItem();
    if (Mage::helper('rewards/rule')->validateCatalogruleAdminSettings($rule, false)) {
        $msg_title = "Sweet Tooth updated your product 'cost' attribute for performance improvement reasons!";
        $msg_desc = "We enabled the 'Used in Product Listing' option for the 'cost' attribute configuration of your
        store in order to optimize speed for your rewards rules that rely on product profits. <br />To disable this again,
        you can manage your product 'cost' attribute in <i>Catalog > Attributes > Manage atrributes</i> section.";
        $msg_severity = Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE;

        Mage::helper('rewards/mysql4_install')->createInstallNotice($msg_title, $msg_desc, null, $msg_severity);
    }
}

// clean config cache
Mage::getConfig()->cleanCache();