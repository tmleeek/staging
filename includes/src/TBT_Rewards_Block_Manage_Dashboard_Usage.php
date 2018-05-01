<?php

class TBT_Rewards_Block_Manage_Dashboard_Usage extends Mage_Adminhtml_Block_Template
{
    const CONFIG_DEV_MODE = 'rewards/platform/dev_mode';
    const FORMAT_DATE_DISPLAY = 'M j, Y';

    protected $_account = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('rewards/dashboard/usage.phtml');

        return $this;
    }

    public function getAccountData()
    {
        if ($this->_account !== null) {
            return $this->_account;
        }

        try {
            $platform = Mage::getSingleton('rewards/platform_instance');
            $this->_account = $platform->account()->get();
        } catch (Exception $ex) {
            $this->_account = false;
        }

        return $this->_account;
    }

    /**
     * Retrieve percent of transactions used for current billing period
     *
     * @return int Percent of transactions made in current billing period
     */
    public function getPercentComplete()
    {
        $account = $this->getAccountData();
        if (isset($account['billing']['percent'])) {
            return $account['billing']['percent'];
        }

        return 0;
    }

    /**
     * Retrieve # of transactions used for current billing period
     *
     * @return int Number of transactions made in current billing period
     */
    public function getTransactionsUsed()
    {
        $account = $this->getAccountData();
        if (isset($account['billing']['transfers_used'])) {
            return $account['billing']['transfers_used'];
        }

        return -1;
    }

    /**
     * Retrieve current account billing period start date
     *
     * @return text Date on which current billing period starts
     */
    public function getBillingPeriodStart()
    {
        $account = $this->getAccountData();

        if (isset($account['billing']['start_date'])) {
            $timestamp = strtotime($account['billing']['start_date']);
            return date(self::FORMAT_DATE_DISPLAY, $timestamp);
        }

        return date(self::FORMAT_DATE_DISPLAY);
    }

    /**
     * Retrieve current account billing period end date
     *
     * @return text Date on which current billing period ends
     */
    public function getBillingPeriodEnd()
    {
        $account = $this->getAccountData();

        if (isset($account['billing']['end_date'])) {
            $timestamp = strtotime($account['billing']['end_date']);
            return date(self::FORMAT_DATE_DISPLAY, $timestamp);
        }

        return date(self::FORMAT_DATE_DISPLAY);
    }

    public function displayBillingPeriod()
    {
        $account = $this->getAccountData();

        // billing period will soon be returned by Platform, so we make sure it's currently returned
        if ($account === false || !isset($account['billing']['start_date']) || !isset($account['billing']['end_date'])) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the usage bar, transactions and billing period should be displayed
     *
     * @return boolean Returns true if account is connected and not in Developer Mode
     */
    public function displayUsage()
    {
        $account = $this->getAccountData();

        if ($account === false || $this->isDevMode()) {
            return false;
        }

        // just to be safe we check for billing data
        if (!isset($account['billing'])) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether we add a notification on Sweet Tooth Usage Dashboard abot CRON not working
     * @return boolean returns true if CRON is not enabled
     */
    public function displayCronNotification()
    {
        if ( !$this->_isCronRequired() ) {
            return false;
        }

        return ! Mage::helper('rewards/cron')->isWorking();
    }

    /**
     * Check if account is connected and is in developer mode
     * @return boolean return true if account connected in Developer Mode
     */
    public function isDevMode()
    {
        $account = $this->getAccountData();
        return ($account !== false && Mage::getStoreConfig(self::CONFIG_DEV_MODE));
    }

    /**
     * Retrive notifications block html
     *
     * @return Notifications blocks html to be rendered
     */
    public function getNotificationsHtml()
    {
        $notificationsBlock = $this->getLayout()->createBlock('rewards/manage_dashboard_notifications');

        if ($this->displayConnectNotification()) {
            $notificationsBlock->setNotification(
                $this->__("Connect your Sweet Tooth Account to start rewarding!"),
                "https://support.sweettoothrewards.com/entries/21376743-connecting-a-magento-store-to-your-sweet-tooth-account",
                $this->__("Learn More")
            );
        }

        if ($this->displayConnectNotification() && $this->_displayDisableNotification()) {
            $notificationsBlock->setNotification(
                $this->__("Sweet Tooth will automatically stop rewarding your customers, if your account is disconnected for longer than 24 hours."),
                "https://support.sweettoothrewards.com/entries/21376743-connecting-a-magento-store-to-your-sweet-tooth-account",
                $this->__("Learn More")
            );
        }

        if ($this->isDevMode()) {
            $notificationsBlock->setNotification(
                $this->__("Your account is in <strong>Developer Mode</strong>."),
                "https://support.sweettoothrewards.com/entries/21526272-developer-mode-in-magento",
                $this->__("Learn More")
            );
        }

        if ($this->displayCronNotification()) {
            $notificationsBlock->setNotification(
                $this->__("Your CRON tasks are not enabled and may be limiting functionality."),
                "https://support.sweettoothrewards.com/entries/21196536-setting-up-cron-jobs-in-magento",
                $this->__("Learn More")
            );
        }

        return $notificationsBlock->toHtml();
    }

    public function displayConnectNotification()
    {
        $account = $this->getAccountData();

        if ($account === false) {
            return true;
        }

        return false;
    }

    /**
     * Checks whether or not there are any earning rules. These will be disabled if account is not connected
     * for more than 24 hours.
     *
     * @return bool     True, if there are earning rules, false otherwise.
     */
    protected function _displayDisableNotification()
    {
        $behaviourRulesCount = Mage::getResourceModel('rewards/special_collection')
            ->getSize();
        if ($behaviourRulesCount > 0) {
            return true;
        }

        $catalogRulesCount = Mage::getResourceModel('catalogrule/rule_collection')
            ->addFieldToFilter('points_action', array('notnull' => true))
            ->addFieldToFilter('points_catalogrule_simple_action', array('null' => true))
            ->getSize();
        if ($catalogRulesCount > 0) {
            return true;
        }

        $salesRulesCount = Mage::getResourceModel('salesrule/rule_collection')
            ->addFieldToFilter('points_action', array('notnull' => true))
            ->addFieldToFilter('points_discount_action', array('null' => true))
            ->getSize();
        if ($salesRulesCount > 0) {
            return true;
        }

        return false;
    }

    /*
     *  Check for all the cron dependent sweet tooth services
     *  return boolean
     */
    protected function _isCronRequired()
    {
        if (Mage::getStoreConfigFlag('rewards/expire/is_enabled')) {
            return true;
        }
        if (Mage::getStoreConfigFlag('rewards/display/allow_points_summary_email')) {
            return true;
        }
        if (Mage::helper('rewards/cron')->hasBirthdayPointRules()) {
            return true;
        }
        if (Mage::helper('rewards/cron')->hasCatalogRules()) {
            return true;
        }
        if (Mage::helper('rewards/cron')->hasOnholdRules()) {
            return true;
        }

        return false;
    }
}
