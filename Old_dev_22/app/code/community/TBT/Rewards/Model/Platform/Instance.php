<?php

try {
    include_once(Mage::getBaseDir('lib'). DS. 'SweetTooth'. DS .'SweetTooth.php');
} catch (Exception $e) {
    die(__FILE__ . ": Wasn't able to load lib/SweetTooth.php.  Download rewardsplatformsdk.git and run the installer to symlink it.");
}


class TBT_Rewards_Model_Platform_Instance extends SweetTooth
{
    const CHANNEL_ID = 'magento';


    const CONFIG_API_KEY     = 'rewards/platform/apikey';
    const CONFIG_SECRET_KEY  = 'rewards/platform/secretkey';
    const CONFIG_API_URL     = 'rewards/developer/apiurl';
    const CONFIG_API_TIMEOUT = 'rewards/developer/api_timeout';
    const CONFIG_DEBUG_MODE  = 'rewards/developer/debug_mode';

    public function __construct()
    {
        $this->apiKey = Mage::app()->getStore()->getConfig(self::CONFIG_API_KEY);
    	$this->apiSecret = Mage::helper('core')->decrypt(Mage::app()->getStore()->getConfig(self::CONFIG_SECRET_KEY));

        $instance = parent::__construct($this->apiKey, $this->apiSecret);
        $instance->setBaseDomain(Mage::getStoreConfig(self::CONFIG_API_URL));
        $instance->setTransferApiTimeout(Mage::getStoreConfig(self::CONFIG_API_TIMEOUT));

        return $instance;
    }

    /**
     * Logging outgoing GET requests.  This is useful for performance testing as well as testing any unexpected
     * responses or connectivity issues with Platform.
     *
     * @see SweetToothClient::get()
     */
    public function get($resource, $data = array())
    {
        if (!Mage::getStoreConfig(self::CONFIG_DEBUG_MODE)) {
            return parent::get($resource, $data);
        }

        $url = $this->_subdomain . '.' . $this->_baseDomain . $this->_apiEndpoint . $resource;
        if (isset($data) && count($data) > 0) {
            $url .= '?' . http_build_query($data);
        }
        $restClient = $this->getRestClient("GET", $resource);

        Mage::helper('rewards')->log(sprintf("Debug: RESTClient Object: %s", print_r($restClient, true)));
        Mage::helper('rewards')->log(sprintf("Debug: Querying API: %s", $url));

        $startTime = microtime(true);
        $result = parent::get($resource, $data);
        $endTime = microtime(true);

        Mage::helper('rewards')->log(sprintf("Debug: Query complete (took %ss). Result: %s", round(($endTime - $startTime) / 1000, 3), print_r($result, true)));

        return $result;
    }

    /**
     * Logging outgoing POST requests.  This is useful for performance testing as well as testing any unexpected
     * responses or connectivity issues with platform.
     *
     * @see SweetToothClient::post()
     */
    public function post($resource, $data)
    {
        if (!Mage::getStoreConfig(self::CONFIG_DEBUG_MODE)) {
            return parent::post($resource, $data);
        }

        $url = $this->_subdomain . '.' . $this->_baseDomain . $this->_apiEndpoint . $resource;
        $json = json_encode($data, true);
        $restClient = $this->getRestClient("POST", $resource);

        Mage::helper('rewards')->log(sprintf("Debug: RESTClient Object: %s", print_r($restClient, true)));
        Mage::helper('rewards')->log(sprintf("Debug: Posting to API: %s: JSON: %s", $url, $json));

        $startTime = microtime(true);
        $result = parent::post($resource, $data);
        $endTime = microtime(true);

        Mage::helper('rewards')->log(sprintf("Debug: Posting complete (took %ss). Result: %s", round(($endTime - $startTime) / 1000, 3), print_r($result, true)));

        return $result;
    }
}
