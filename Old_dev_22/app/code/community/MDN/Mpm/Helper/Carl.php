<?php

include_once('Mpm/v1/Client/Auth.php');
include_once('Mpm/v1/Client/Transaction.php');
include_once('Mpm/v1/Client/Monitoring.php');
include_once('Mpm/v1/Client/Catalog.php');
include_once('Mpm/v1/Client/ClientReport.php');
include_once('Mpm/v1/Client/Seller.php');
include_once('Mpm/v1/Client/User.php');
require_once('Mpm/v1/Client/Pricer.php');
require_once('Mpm/v1/Client/Product.php');
require_once('Mpm/v1/Client/Rule.php');
require_once('Mpm/v1/Client/Dashboard.php');

class MDN_Mpm_Helper_Carl extends Mage_Core_Helper_Abstract {

    private static $pricer;
    private static $_clients;

    protected $_carl = null;

    protected $_cache = array();

    protected $_channelSuscribed = null;

    private $_defaultRuleFields = array(
        'id',
        'type',
        'name',
        'priority',
        'enable',
        'last_indexation',
        'updated_at'
    );

    public function getUserStatus()
    {
        $instance = \Mpm\GatewayClient\Client\User::getInstance();
        $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
        $instance->setToken($this->getToken());
        return $instance->getStatus();
    }

    public function getTransactionHistory()
    {
        Mage::helper('Mpm')->log('Call service getTransactionHistory');

        $instance = \Mpm\GatewayClient\Client\Transaction::getInstance();
        $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
        $instance->setToken($this->getToken());
        $result = $instance->listTransactions();
        return $result;
    }

    public function getOfferInformation($sku, $channel)
    {
        $instance = \Mpm\GatewayClient\Client\Monitoring::getInstance();
        $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
        $instance->setToken($this->getToken());
        return $instance->getOfferInformation($sku, $channel);
    }

    public function getProductInformation($productId)
    {
        $product = \Mpm\GatewayClient\Client\Product::getInstance();
        $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
        $product->setToken($this->getToken());

        $productInformation = $product->getProduct($productId);
        $productInformation = current(json_decode($productInformation)->body->products);

        return $productInformation;
    }

    public function getCatalogFields()
    {
        return json_decode($this->getClient('catalog')->getFields())->body;
    }

    public function updateProduct($product, $sku)
    {
        return $this->getClient('catalog')->updateProduct($product, $sku);
    }

    public function getMatchingByUrls($sku)
    {
        return $this->getClient('monitoring')->getMatchingByUrls($sku);
    }

    public function postMatchingByUrls($sku, array $urls)
    {
        return $this->getClient('monitoring')->postMatchingByUrls($sku, $urls);
    }

    public function postRule($code, $source, $withoutUserId = false)
    {
        return $this->getClient('rule')->postSave($code, $source, $withoutUserId);
    }

    /**
     * Return the list of the offers for one product
     *
     * @param $sku
     * @return mixed
     */
    public function getProductOffers($sku)
    {
        Mage::helper('Mpm')->log('Call service getProductOffers for sku '.$sku);

        $instance = \Mpm\GatewayClient\Client\Monitoring::getInstance();
        $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
        $instance->setToken($this->getToken());
        $result = $instance->getAllCompetitors($sku);

        $collection = array();
        foreach($result as $channel => $offers)
        {
            $arrayResult = (array)$result;
            $timestamp = !empty($arrayResult[$channel]->_updated_at_timestamp) ? $arrayResult[$channel]->_updated_at_timestamp : null;
            foreach($offers as $offer)
            {
                $item = array_merge(array('channel' => $channel), (array)$offer);
                if (isset($item['price']))
                {
                    $item['total'] = $item['price'] + $item['shipping'];
                    $item['updated_at'] = date('Y-m-d H:i:s', $timestamp);
                    $collection[] = $item;
                }
            }
        }

        return $collection;
    }

    public function getOffersHistory($sku, $channelCode, $from = null, $to = null)
    {
        Mage::helper('Mpm')->log('Call service getOffersHistory for sku '.$sku.' and channel '.$channelCode);

        $instance = \Mpm\GatewayClient\Client\Monitoring::getInstance();
        $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
        $instance->setToken($this->getToken());
        $result = $instance->getCompetitorsHistory($sku, $channelCode, $from, $to);

        return $result;
    }

    public function getMatchingData($sku)
    {
        Mage::helper('Mpm')->log('Call service getMatchingData for sku '.$sku);

        $instance = \Mpm\GatewayClient\Client\Monitoring::getInstance();
        $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
        $instance->setToken($this->getToken());
        $result = $instance->getMatchingData($sku);

        return $result;
    }

    public function getAllChannels(){

        $instance = \Mpm\GatewayClient\Client\Monitoring::getInstance();
        $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
        $instance->setToken($this->getToken());
        return $instance->getAllChannels();

    }

    public function getChannelsSubscribed($toOptionList = false)
    {
        if ($this->_channelSuscribed == null)
        {
            Mage::helper('Mpm')->log('Call service getChannelsSubscribed ');

            $instance = \Mpm\GatewayClient\Client\Monitoring::getInstance();
            $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
            $instance->setToken($this->getToken());
            $this->_channelSuscribed = $instance->getChannelsSubscribed();

            foreach($this->_channelSuscribed as $item)
            {
                $item->channelCode = $item->organization.'_'.$item->locale.'_'.$item->subset;
                $item->channelLabel = $this->getChannelLabel($item->channelCode);
            }
        }

        if ($toOptionList)
        {
            $optionList = array();
            foreach($this->_channelSuscribed as $channel)
                $optionList[$channel->channelCode] = $channel->channelLabel;
            return $optionList;
        }
        else
            return $this->_channelSuscribed;
    }

    public function getChannelLabel($channelCode)
    {
        list($organization, $locale, $subset) = explode('_', $channelCode);
        return ucfirst($organization).'.'.strtolower($locale);
    }

    public function getToken()
    {
        if (!$this->isConfigured())
            throw new Exception('Carl configuration is missing');

        $session = Mage::getSingleton('core/session');
        $token = $session->getData('carl_token');
        $tokenTimestamp = $session->getData('carl_token_timestamp');

        if ($token)
        {
            if (time() - $tokenTimestamp > 1800)
                $token = false;
        }
        if (!$token)
        {
            $login = Mage::getStoreConfig('mpm/account/login');
            $password = Mage::getStoreConfig('mpm/account/password');

            $instance = \Mpm\GatewayClient\Client\Auth::getInstance();
            $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
            $result = $instance->authenticate($login, $password);
            $token = $result->access_token;
            $session->setData('carl_token', $token);
            $session->setData('carl_token_timestamp', time());
        }
        return $token;
    }

    public function unsetToken()
    {
        $session = Mage::getSingleton('core/session');
        $session->setData('carl_token', null);
    }

    /**
     * Check if the credentials are valid
     *
     * @return bool
     */
    public function checkCredentials()
    {
        try
        {
            $token = $this->getToken();
            return true;
        }
        catch(Exception $ex)
        {
            return false;
        }

    }

    /**
     * @param $filePath
     * @throws Exception
     */
    public function uploadCatalog($filePath)
    {
        Mage::helper('Mpm')->log('Start catalog upload');

        if (!file_exists($filePath))
            throw new Exception('File to upload does not exist');

        $instance = \Mpm\GatewayClient\Client\Catalog::getInstance();
        $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
        $instance->setToken($this->getToken());
        $result = $instance->upload($filePath);

        Mage::helper('Mpm')->log('Catalog upload complete : '.$result);
    }


    public function isConfigured()
    {
        return (Mage::getStoreConfig('mpm/account/login') && Mage::getStoreConfig('mpm/account/password'));
    }

    public function getChannelImageUrl($channel)
    {
        return 'http://bms-performance.com/img/channel/'.$channel.'.png';
    }

    /***************************************************************************************************************************************
     ***************************************************************************************************************************************
     * REPORTS
     ***************************************************************************************************************************************
     */


    /**
     * Launch a report request
     *
     * @param $source
     */
    public function requestClientReport($source, $params = array())
    {
        Mage::helper('Mpm')->log('Call service requestClientReport with source='.$source);

        $instance = \Mpm\GatewayClient\Client\ClientReport::getInstance();
        $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
        $instance->setToken($this->getToken());
        return $instance->request($source, $params);
    }

    public function getReportList()
    {
        Mage::helper('Mpm')->log('Call service getReportList');

        $instance = \Mpm\GatewayClient\Client\ClientReport::getInstance();
        $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
        $instance->setToken($this->getToken());
        return $instance->getReportList();
    }


    public function getReportContent($reportId)
    {
        Mage::helper('Mpm')->log('Call service getReportContent');

        $instance = \Mpm\GatewayClient\Client\ClientReport::getInstance();
        $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
        $instance->setToken($this->getToken());
        return $instance->getReportContent($reportId);
    }

    public function getReportStatus($reportId)
    {
        Mage::helper('Mpm')->log('Call service getReportStatus for report #'.$reportId);

        $reports = $this->getReportList();

        $result = 'not_found';
        foreach($reports as $report) {
            if ($report->id == $reportId)
                $result = $report->status;
        }

        return $result;
    }

    public function getWebserviceCredentials($channel)
    {
        Mage::helper('Mpm')->log('Call service getWebserviceCredentials');

        $instance = \Mpm\GatewayClient\Client\Seller::getInstance();
        $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
        $instance->setToken($this->getToken());
        return $instance->getWebserviceCredentials($channel);
    }

    public function setWebserviceCredentials($channel, $data)
    {
        Mage::helper('Mpm')->log('Call service setWebserviceCredentials');

        $instance = \Mpm\GatewayClient\Client\Seller::getInstance();
        $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
        $instance->setToken($this->getToken());
        return $instance->setWebserviceCredentials($channel, $data);
    }

    public function getChannelStats()
    {
        Mage::helper('Mpm')->log('Call service getChannelStats');

        $instance = \Mpm\GatewayClient\Client\Monitoring::getInstance();
        $instance->setLogPath(Mage::helper('Mpm')->getClientLogPath());
        $instance->setToken($this->getToken());
        $data = $instance->getChannelStats();

        /*foreach($data as $k => $item)
        {
            if ($k == 'total')
                continue;

            $nbrAssociated = (isset($item->details->associated)) ? $item->details->associated->nbr : 0;
            $nbrNotAssociated = (isset($item->details->not_associated)) ? $item->details->not_associated->nbr : 0;

            $item->details->pending->nbr = $data->total - ($nbrAssociated + $nbrNotAssociated);
        }*/

        return $data;
    }


    /***************************************************************************************************************************************
     ***************************************************************************************************************************************
     * RULES
     ***************************************************************************************************************************************
     */

    public function getRulesProduct($productId, $channel)
    {
        return $this->getPricer()->getRulesProduct($productId, $channel);
    }

    public function simulatePricing($productId, $channel = null, $rules = array())
    {
        return $this->getPricer()->simulatePricing($productId, $channel, $rules);
    }

    public function repriceProduct($productId)
    {
        return $this->getPricer()->repriceProduct($productId);
    }

    public function createRuleProduct($productId, $channel = '', array $configuration)
    {
        return $this->getPricer()->createRuleProduct($productId, $channel, $configuration);
    }

    public function updateRuleProduct($ruleId, $productId, $channel = '', array $configuration)
    {
        return $this->getPricer()->updateRuleProduct($ruleId, $productId, $channel, $configuration);
    }

    public function deleteRuleProduct($ruleId)
    {
        return $this->getPricer()->deleteRuleProduct($ruleId);
    }

    public function createRule(array $configuration)
    {
        return $this->getPricer()->createRule($configuration);
    }

    public function updateRule($ruleId, array $configuration)
    {
        return $this->getPricer()->updateRule($ruleId, $configuration);
    }

    public function deleteRule($ruleId)
    {
        return $this->getPricer()->deleteRule($ruleId);
    }

    public function getRuleById($ruleId)
    {
        return $this->getPricer()->getRule($ruleId);
    }

    public function indexRule($ruleId)
    {
        return $this->getPricer()->indexRule($ruleId);
    }

    public function getRule($ruleId)
    {
        $rule = $this->getPricer()->getRule($ruleId);

        $ruleObject = new Varien_Object();

        $perimeter = array();
        $condition = array();
        $variables = array();
        foreach($rule as $field => $value) {
            if($field === 'perimeter') {
                foreach($value as $key => $perimeterValue) {
                    $perimeter[$key] = $perimeterValue;
                }
            } elseif($field === 'condition') {
                foreach($value as $key => $conditionValue) {
                    $condition[$key] = $conditionValue;
                }
            } elseif(!in_array($field, $this->_defaultRuleFields)) {
                $variables[$field] = $value;
            } else {
                $ruleObject->$field = $value;
            }
        }

        $ruleObject->type = strtolower($ruleObject->type);
        $ruleObject->variables = $variables;
        $ruleObject->perimeter = $perimeter;
        $ruleObject->condition = $condition;

        $ruleObject->variableConditions  = $this->_getVariablesFromRuleType($rule->type);
        $ruleObject->ruleConditions      = $this->_getRuleFields()->condition_fields;
        $ruleObject->perimeterConditions = $this->_getRuleFields()->perimeter_fields;

        return $ruleObject;
    }

    private function _getVariablesFromRuleType($type)
    {
        $variables = array();
        foreach($this->_getRuleFields()->rules_fields as $rule) {
            if (strtolower($rule->type) === strtolower($type)) {
                $variables = array();
                foreach ($rule->translation->require_fields as $field) {
                    if (!in_array($field->name, $this->_defaultRuleFields, true)) {
                        $variables[] = $field;
                    }
                }

                foreach ($rule->translation->optional_fields as $field) {
                    if (!in_array($field->name, $this->_defaultRuleFields, true)) {
                        $variables[] = $field;
                    }
                }
            }
        }
        return $variables;
    }

    public function getRuleTypes()
    {
        $ruleTypes = array();
        foreach($this->_getRuleFields()->rules_fields as $rule) {
            $ruleTypes[] = $rule->type;
        }

        return $ruleTypes;
    }

    public function getRuleByType($type)
    {
        foreach($this->_getRuleFields()->rules_fields as $rule) {
            if(strtolower($rule->type) === strtolower($type)) {
                $ruleObject = new Varien_Object();

                foreach($rule->require_fields as $field) {
                    if(in_array($field->name, $this->_defaultRuleFields)) {
                        $ruleObject->{$field->name} = '';
                    }
                }

                $ruleObject->type = $type;
                $ruleObject->variables = array();
                $ruleObject->perimeter = array();
                $ruleObject->condition = array();

                $ruleObject->variableConditions  = $this->_getVariablesFromRuleType($rule->type);
                $ruleObject->ruleConditions      = $this->_getRuleFields()->condition_fields;
                $ruleObject->perimeterConditions = $this->_getRuleFields()->perimeter_fields;

                return $ruleObject;
            }
        }

        throw new \Exception('The type rule "'.$type.'" does not exist');
    }

    public function getClientRuleByType($type)
    {
        $rulesByType = array();
        $clientRules = $this->_getClientRules();
        foreach($clientRules as $rule) {
            if(isset($rule->type) && strtoupper($type) === $rule->type) {
                $rulesByType[] = $rule;
            }
        }

        return $rulesByType;
    }

    private function _getClientRules()
    {
        if(!isset($this->_cache['client_rules'])) {
            $this->_cache['client_rules'] = $this->getPricer()->getRules();
        }

        return $this->_cache['client_rules'];
    }

    private function _getRuleFields()
    {
        if(!isset($this->_cache['rule_fields'])) {
            $this->_cache['rule_fields'] = $this->getPricer()->getRuleFields();
        }

        return $this->_cache['rule_fields'];
    }

    public function getProducts(array $filters = array(), array $sort = array(), $limit = 50, $page = 0)
    {
        return $this->getPricer()->getProducts($filters, $sort, $limit, $page);
    }

    public function getLastPricing()
    {
        return $this->getPricer()->getLastPricing();
    }

    public function getProductPricingHistory($productId, $channel, $limit = 10)
    {
        return $this->getPricer()->getProductHistory($productId, $channel, $limit);
    }

    public function getConfigurationStatus()
    {
        return $this->getPricer()->getConfigurationStatus();
    }

    private function getPricer()
    {
        if(!self::$pricer instanceof \Mpm\GatewayClient\Client\Pricer) {
            self::$pricer = \Mpm\GatewayClient\Client\Pricer::getInstance();
            self::$pricer->setLogPath(Mage::helper('Mpm')->getClientLogPath());
            self::$pricer->setToken($this->getToken());
        }

        return self::$pricer;
    }

    public function getProductsRule($ruleId, $offset = 0, $limit = 20, $filters = array(), $sort = array())
    {
        return $this->getPricer()->getProductsRule($ruleId, $offset, $limit, $filters, $sort);
    }

    public function getRulesFromProduct($productId, $channel = null)
    {
        return $this->getPricer()->getRulesFromProduct($productId, $channel);
    }

    public function getShippingGrids()
    {
        return $this->getPricer()->getShippingGrids();
    }

    public function deleteShippingGrid($gridName)
    {
        return $this->getPricer()->deleteShippingGrid($gridName);
    }

    public function getShippingRows($gridName)
    {
        return $this->getPricer()->getShippingRows($gridName);
    }

    public function createShippingRow($gridName, $weight, $price)
    {
        return $this->getPricer()->createShippingRow($gridName, $weight, $price);
    }

    public function pricingInProgress()
    {
        return $this->getPricer()->pricingInProgress();
    }

    public function getStatisticsBbw()
    {
        return $this->getPricer()->getStatisticsBbw();
    }

    public function getStatisticsOffers()
    {
        return $this->getPricer()->getStatisticsOffers();
    }

    public function getFieldsUsed()
    {
        return $this->getPricer()->getFieldsUsed();
    }

    public function getBestCompetitors($channel, $limit)
    {
        return $this->getClient('dashboard')->getBestCompetitors($channel, $limit);
    }

    public function jsonListSellers()
    {
        return $this->getClient('seller')->jsonListSellers()->competitors;
    }

    public function listSellers($query)
    {
        return $this->getClient('seller')->listSellers($query);
    }

    private function getClient($clientType)
    {
        if(!isset(self::$_clients[$clientType])) {
            $class = '\Mpm\GatewayClient\Client\\'.ucfirst($clientType);

            self::$_clients[$clientType] = $class::getInstance();
            self::$_clients[$clientType]->setToken($this->getToken());
        }

        return self::$_clients[$clientType];
    }

    public function getClientCurrency()
    {
        $currency = $this->getClient('rule')->getPlay('CLIENT-DATA.CONFIGURATION.CURRENCY');

        $currencies = array(
            'EUR' => '€',
            'GBP' => '£',
            'USD' => '$',
        );

        $clientCurrency = '?';
        if(isset($currencies[$currency])) {
            $clientCurrency =  $currencies[$currency];
        }

        return $clientCurrency;
    }

    public function getFieldsToMap(){

        return $this->getPricer()->getFieldsToMap();

    }

}
