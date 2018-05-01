<?php

/**
 * This will remove any current configuration data for 'rewards/platform/milestones_transfer_notified' key, used
 * to save milestones for which store administrator was already notified, to make sure we avoid any incompatibility
 * errors. At first admin login this will be re-populated with valid data.
 */
Mage::getConfig()->saveConfig(TBT_Rewards_Model_Observer_Adminhtml_Controller::TRANSFER_NOTIFIED, '[]');

// ensure that the next call on the API will pick up the config changes
Mage::getConfig()->cleanCache();
