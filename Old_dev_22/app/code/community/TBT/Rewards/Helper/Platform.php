<?php

/**
 * WDCA - Sweet Tooth
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS
 * License, which extends the Open Software License (OSL 3.0).
 * The Sweet Tooth License is available at this URL:
 * https://www.sweettoothrewards.com/terms-of-service
 * The Open Software License is available at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * By adding to, editing, or in any way modifying this code, WDCA is
 * not held liable for any inconsistencies or abnormalities in the
 * behaviour of this code.
 * By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by WDCA, outlined in the
 * provided Sweet Tooth License.
 * Upon discovery of modified code in the process of support, the Licensee
 * is still held accountable for any and all billable time WDCA spent
 * during the support process.
 * WDCA does not guarantee compatibility with any other framework extension.
 * WDCA is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to
 * support@sweettoothrewards.com or call 1.855.699.9322, so we can send you a copy
 * immediately.
 *
 * @category   [TBT]
 * @package    [TBT_Rewards]
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper Data
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */

try {
    include_once(Mage::getBaseDir('lib') . DS . 'SweetTooth'. DS .'SweetTooth.php');
    include_once(Mage::getBaseDir('lib') . DS . 'SweetTooth'. DS . 'etc' . DS . 'ApiException.php');
} catch (Exception $e) {
    die("Wasn't able to load lib/SweetTooth.php.  Download rewardsplatformsdk.git and run the installer to symlink it.");
}

class TBT_Rewards_Helper_Platform extends Mage_Core_Helper_Abstract
{
    protected $_hasErrors = false;

    public function createPlatformAccount($username, $email, $password)
    {
        // using core magento phrases here, so they should already be translated, if relevant
        if (!$username) {
            $this->_getSession()->addError($this->__("User Name is required field."));
            $this->_hasErrors = true;
        }
        if (!$email) {
            $this->_getSession()->addError($this->__("The email address is empty."));
            $this->_hasErrors = true;
        }
        if (!$password) {
            $this->_getSession()->addError($this->__("Please enter valid password."));
            $this->_hasErrors = true;
        }
        $alnumFilter = new Zend_Filter_Alnum();
        if ($username != $alnumFilter->filter($username)) {
            $this->_getSession()->addError($this->__("Username must only include letters and numbers (a-z, A-Z, 0-9)."));
            $this->_hasErrors = true;
        }
        if ($this->_hasErrors) {
            return $this;
        }

        try {
            $client = Mage::getSingleton('rewards/platform_instance');
            $fields = array(
                'username' => $username,
                'email' => $email,
                'password' => $password,
            );

            try {
                $account = $client->account()->create($fields);
                $this->_createChannelForAccount($username, $password);
                $this->_getSession()->addSuccess($this->__("Sweet Tooth account successfully created and connected to your Magento store."));
            } catch (SweetToothApiException $ex) {
                // 409 means the account already exists
                Mage::helper('rewards')->log("Exception (Code " . $ex->getCode() . "): " . $ex->getMessage());
                if ($ex->getCode() != 409 && !strstr($ex->getMessage(), "already exist")) {
                    Mage::helper('rewards')->log("Access Denied for API URL ({$client->getApiBaseUrl()}) using Key ({$client->getApiKey()}) and Secret ({$client->getApiSecret()})");
                    throw $ex;
                }

                throw $ex;

            }
        } catch (Exception $ex) {
            Mage::helper('rewards')->log($ex->getMessage());
            Mage::helper('rewards')->log($ex->getTraceAsString());

            $this->_getSession()->addError($ex->getMessage());
        }

        return $this;
    }

    public function connectWithPlatformAccount($username, $password, $isDevMode = false)
    {
        // using core magento phrases here, so they should already be translated, if relevant
        if (!$username) {
            $this->_getSession()->addError($this->__("User Name is required field."));
            $this->_hasErrors = true;
        }
        if (!$password) {
            $this->_getSession()->addError($this->__("Please enter valid password."));
            $this->_hasErrors = true;
        }
        if ($this->_hasErrors) {
            return $this;
        }

        try {
            $client = Mage::getSingleton('rewards/platform_instance');
            $fields = array(
                'username' => $username,
                'password' => $password,
            );

            // since account exists, let's TRY to login and continue to create/connect the channel
            $this->_createChannelForAccount($username, $password, $isDevMode);
            $this->_getSession()->addSuccess($this->__("Sweet Tooth account successfully connected to your Magento store."));
        } catch (SweetToothApiException $ex) {
            if ($ex->getCode() == SweetToothApiException::FORBIDDEN || $ex->getCode() == SweetToothApiException::UNAUTHORIZED) {
                $this->_getSession()->addError($this->__("Username or password is invalid."));

                return $this;
            } elseif ($ex->getCode() == SweetToothApiException::NOT_FOUND) {
                $this->_getSession()->addError($this->__("We're having trouble reaching Sweet Tooth servers at the moment."
                    . "<br/>If you continue to see this message for longer than a few hours, please contact Sweet Tooth support."
                ));

                return $this;
            } elseif ($ex->getCode() == 0) {
                $this->_getSession()->addError($this->__("We're having trouble reaching Sweet Tooth servers at the moment."
                . " Please check your server's connection to the internet."
                . "<br/>If you still continue to see this message for longer than a few hours, please contact Sweet Tooth support."
                ));

                return $this;
            }

            Mage::helper('rewards')->log(get_class($ex) . " (Code [{$ex->getCode()}]): [{$ex->getMessage()}]");
            Mage::helper('rewards')->log($ex->getTraceAsString());

            $message = $this->__("Servers are currently unavailable, please try again later.");
            $this->_getSession()->addError($message);
        } catch (Exception $ex) {
            Mage::helper('rewards')->log(get_class($ex) . " (Code [{$ex->getCode()}]): [{$ex->getMessage()}]");
            Mage::helper('rewards')->log($ex->getTraceAsString());

            $message = $this->__("Servers are currently unavailable, please try again later.");
            $this->_getSession()->addError($message);
        }

        return $this;
    }

    protected function _createChannelForAccount($username, $password, $isDevMode = false)
    {
        $client = new SweetTooth($username, $password);
        $client->setBaseDomain(Mage::getStoreConfig(TBT_Rewards_Model_Platform_Instance::CONFIG_API_URL));

        //if you can't get the account then doesn't exist throw error
        $account = $client->account()->get();

        $channelData['channel_type']     = 'magento';
        $channelData['channel_version']  = (string) Mage::getConfig()->getNode('modules/TBT_Rewards/version');
        $channelData['platform_version'] = Mage::getVersion();
        $channelData['frontend_url']     = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $channelData['backend_url']      = Mage::getUrl('adminhtml');
        $channelData['dev_mode']         = $isDevMode;

        $channel = $client->channel()->create($channelData);

        Mage::getModel('core/config')->saveConfig('rewards/platform/apikey', $channel['api_key']);
        Mage::getModel('core/config')->saveConfig('rewards/platform/secretkey', Mage::helper('core')->encrypt($channel['api_secret']));
        Mage::getModel('core/config')->saveConfig('rewards/platform/is_connected', 1);

        Mage::getConfig()->saveConfig(TBT_Rewards_Helper_Platform_Account::XPATH_PLATFORM_USERNAME, $username);
        Mage::getConfig()->saveConfig(TBT_Rewards_Helper_Platform_Account::XPATH_PLATFORM_EMAIL, $account['email']);
        Mage::getConfig()->saveConfig(TBT_Rewards_Helper_Platform_Account::XPATH_PLATFORM_FIRSTNAME, $account['firstname']);
        Mage::getConfig()->saveConfig(TBT_Rewards_Helper_Platform_Account::XPATH_PLATFORM_LASTNAME, $account['lastname']);
        if (isset($account['options'])) {
            Mage::getConfig()->saveConfig(TBT_Rewards_Helper_Platform_Account::XPATH_PLATFORM_OPTIONS, json_encode($account['options']));
        }

        // Set values on this helper in case these config options are subsequently saved so they don't override
        // these values.
        Mage::helper('rewards/config')->setConfigValue('rewards/platform/apikey', $channel['api_key']);
        Mage::helper('rewards/config')->setConfigValue('rewards/platform/secretkey', $channel['api_secret']);

        Mage::getConfig()->cleanCache();

        return $this;
    }

    protected function _getSession()
    {
        return Mage::getSingleton('core/session');
    }
}
