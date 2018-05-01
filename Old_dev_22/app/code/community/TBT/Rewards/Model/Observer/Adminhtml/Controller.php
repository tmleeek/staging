<?php

try {
    include_once (Mage::getBaseDir('lib') . DS. 'SweetTooth' . DS . 'etc' . DS . 'SdkException.php');
    include_once (Mage::getBaseDir('lib') . DS. 'SweetTooth' . DS . 'etc' . DS . 'ApiException.php');
} catch (Exception $e) {
    die(__FILE__ . ": Wasn't able to load lib/SweetTooth.php.  Download rewardsplatformsdk.git and run the installer to symlink it.");
}

class TBT_Rewards_Model_Observer_Adminhtml_Controller extends Varien_Object
{
    const TRANSFER_NOTIFIED = 'rewards/platform/milestones_transfer_notified';
    const TRANSFER_NOTIFICATIONS = 'rewards/platform/milestones_transfer_notifications';
    const DO_SHOW_NOTIFICATIONS = 'rewards/notifications/showUsageNotification';

    /**
     * Checks current account usage and, if the merchant has exceeded the next milestone
     * in the list of notifications, display a notification to the admin.
     *
     * @return TBT_Rewards_Model_Observer_Adminhtml_Controller
     */
    public function createAccountUsageNotification($observer)
    {
        $doShowNotifications = Mage::getStoreConfig(self::DO_SHOW_NOTIFICATIONS);
        if (!$doShowNotifications) {
            return $this;
        }

        $doSaveNotifications = false;

        $notifications_csv = Mage::getStoreConfig(self::TRANSFER_NOTIFICATIONS);
        $notifications = explode(",", $notifications_csv);

        $notified = Zend_Json::decode(Mage::getStoreConfig(self::TRANSFER_NOTIFIED));

        try {
            $client = Mage::getSingleton('rewards/platform_instance');
            $account = $client->account()->get();
        } catch (Exception $ex) {
            return $this;
        }

        if (isset($account['billing'])) {
            // remove invalid notifications based on current account usage percent
            $validNotified = $this->_cleanNotifications($account['billing']['percent'], $notified);
            if ($notified !== $validNotified) {
                // making sure we save new notifications array
                $notified = $validNotified;
                $doSaveNotifications = true;
            }

            foreach ($notifications as $notification) {
                // don't notify the admin of a milestone if they've already been notified
                if (in_array($notification, array_keys($notified))) {
                    continue;
                }

                // if the admin has reached the next milestone, let them know
                if ($account['billing']['percent'] >= $notification) {
                    $newNotificationId = $this->_notify($account, $notification);
                    $notified[$notification] = $newNotificationId;
                    $doSaveNotifications = true;
                }
            }
        }

        if ($doSaveNotifications) {
            Mage::getConfig()->saveConfig(self::TRANSFER_NOTIFIED, Zend_Json::encode($notified))
                ->cleanCache();
        }

        return $this;
    }

    /**
     * Creates an admin notification describing that an account usage milestone has been reached
     *
     * @param array $account The account resource returned from the API
     * @param int $percent The percent usage milestone that the merchant has reached
     * @return int Created notification's ID
     */
    protected function _notify($account, $percent)
    {
        $transfers_used = $account['billing']['transfers_used'];
        $transfers_total = $account['billing']['transfers_total'];
        $plan = $account['billing']['plan'];
        $url = urldecode($account['login_url']);

        $severity = Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE;
        $date = date("c", time());
        $title = Mage::helper('rewards')->__("You have used %s%% of your Sweet Tooth subscription", $percent);
        $description = Mage::helper('rewards')->__("You have used %s points transfers out of %s total available transfers on your %s subscription plan.  You can upgrade at any time; go to the [billing_url]Billing section[/billing_url] of your Sweet Tooth Account.", $transfers_used, $transfers_total, $plan);
        $description = Mage::helper('tbtcommon/strings')->getTextWithLinks($description, 'billing_url',
            $url, array('target' => 'window'));

        $notification = Mage::getModel('adminnotification/inbox')
            ->setDateAdded($date)
            ->setSeverity($severity)
            ->setTitle($title)
            ->setDescription($description)
            ->setUrl('')
            ->save();

        return $notification->getId();
    }

    /**
     * Goes through list of active notifications and removes the ones that no longer apply,
     * due to being a new month, or the user upgraded his plan
     *
     * @param int $accountUsagePercent  The account usage percent ( $account['billing']['percent'] )
     * @param array $notified           The current array(milestone => notificationId) that the user was made aware of
     * @return array                    All valid notifications array
     */
    protected function _cleanNotifications($accountUsagePercent, $notified) {
        // check if we have already notified the admin of any account usage milestones being reached
        if (count($notified) <= 0) {
            return array();
        }

        $notificationKeys = array_keys($notified);
        $latestNotification = array_pop($notificationKeys);
        // get the latest milestone and, if their current usage is less, it must be the next month
        if ($accountUsagePercent < $latestNotification) {
            // since the month has reset make sure to remove the admin notifications that are no longer valid
            foreach ($notified as $milestone => $notificationId) {
                // remove only notifications that are no longer valid
                if ($accountUsagePercent < $milestone) {
                    $model = Mage::getModel('adminnotification/inbox')->load($notificationId);
                    if (!$model->getId()) {
                        continue;
                    }

                    $model->setIsRemove(1)->save();
                    unset($notified[$milestone]);
                }
            }
        }

        return $notified;
    }
}
