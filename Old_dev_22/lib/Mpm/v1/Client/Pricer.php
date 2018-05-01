<?php namespace Mpm\GatewayClient\Client;

include_once dirname(__FILE__).DS.'..'.DS.'Client.php';

use Mpm\GatewayClient\Client as BaseClient;

class Pricer extends BaseClient
{
    public static function getInstance()
    {
        if (self::$_instance === null || false === (self::$_instance instanceof Pricer)) {
            self::$_instance = new Pricer();
        }
        return self::$_instance;
    }

    public function getConfigurationStatus()
    {
        return $this->get('/pricer/configuration/check');
    }

    public function getRuleFields()
    {
        return $this->get('/pricer/rule/create', array(), array(), array(), 30);
    }

    public function createRule(array $configuration)
    {
        return $this->post('/pricer/rule', array(), $configuration);
    }

    public function updateRule($ruleId, array $configuration)
    {
        return $this->put('/pricer/rule/'.$ruleId, array(), $configuration);
    }

    public function deleteRule($ruleId)
    {
        return $this->delete('/pricer/rule/'.$ruleId);
    }

    public function getRules()
    {
        return $this->get('/pricer/rule');
    }

    public function getRule($ruleId)
    {
        return $this->get('/pricer/rule/'.$ruleId);
    }

    public function indexRule($ruleId)
    {
        return $this->get('/pricer/rule/'.$ruleId.'/reindex');
    }

    public function createRuleProduct($productId, $channel = '', array $configuration)
    {
        $configuration['product_id'] = $productId;
        $configuration['channel']    = $channel;

        return $this->post('/pricer/rule-product', array(), $configuration);
    }

    public function updateRuleProduct($ruleId, $productId, $channel = '', array $configuration)
    {
        $configuration['product_id'] = $productId;
        $configuration['channel']    = $channel;

        return $this->put('/pricer/rule-product/'.$ruleId, array(), $configuration);
    }

    public function deleteRuleProduct($ruleId)
    {
        return $this->delete('/pricer/rule-product/'.$ruleId);
    }

    public function getRulesProduct($productId, $channel = '')
    {
        $parameters = array(
            'product_id' => $productId,
            'channel'    => $channel,
        );

        return $this->get('/pricer/rule-product', $parameters);
    }

    public function getProducts(array $filters = array(), array $sort = array(), $limit = 50, $page = 0)
    {
        $filtersRequest = '';
        foreach ($filters as $field => $value) {
            $filtersRequest.= $field.','.$value.';';
        }
        $filtersRequest = rtrim($filtersRequest, ';');

        $sortRequest = '';
        foreach ($sort as $field => $value) {
            $sortRequest.= $field.','.$value.';';
        }
        $sortRequest = rtrim($sortRequest, ';');

        $parameters = array(
            'filter' => $filtersRequest,
            'sort'   => $sortRequest,
            'limit'  => $limit,
            'page'   => $page,
        );

        return $this->get('/pricer/pricing/list', $parameters, array(), array(), 3);
    }

    public function getLastPricing()
    {
        return $this->get('/pricer/pricing/last-updated');
    }

    public function getProduct($productId, $channel = null)
    {
        $parameters = array(
            'product_id' => $productId,
            'channel'    => $channel,
        );

        return $this->get('/pricer/pricing/product', $parameters);
    }

    public function getRulesFromProduct($productId, $channel = null)
    {
        $parameters = array(
            'product_id' => $productId,
            'channel'    => $channel,
        );

        return $this->get('/pricer/product/rules', $parameters);
    }

    public function getProductsRule($ruleId, $offset = 0, $limit = 20, $filter = array(), $sort = array())
    {
        $parameters = array(
            'offset' => $offset,
            'limit'  => $limit,
        );
        if(!empty($filter)){
            $parameters["filter"] = $filter;
        }
        if(!empty($sort)){
            $parameters["sort"] = $sort;
        }
        return $this->get('/pricer/rule/'.$ruleId.'/product-rule', $parameters);
    }

    public function getProductHistory($productId, $channel = null, $limit = 10)
    {
        $parameters = array(
            'product_id' => $productId,
            'channel'    => $channel,
            'limit'      => $limit,
        );

        return $this->get('/pricer/pricing/product-history', $parameters);
    }

    public function simulatePricing($productId, $channel = null, $rules = array())
    {
        $parameters = array(
            'product_id' => $productId,
            'channel'    => $channel
        );

        // @todo fix this behavior in the Base Client
        foreach ($rules as $key => $value) {
            $parameters['rules['.$key.']'] = $value;
        }

        return $this->get('/pricer/pricing/simulate', $parameters);
    }

    public function repriceProduct($productId)
    {
        $parameters = array(
            'product_id' => $productId
        );

        return $this->get('/pricer/pricing/reprice-product', $parameters);
    }

    public function pricingInProgress()
    {
        return $this->get('/pricer/pricing/in-progress');
    }

    public function getShippingGrids()
    {
        return $this->get('/pricer/shipping/grid');
    }

    public function getShippingRows($gridName)
    {
        $parameters = array(
            'name' => $gridName
        );

        return $this->get('/pricer/shipping/grid-rows', $parameters);
    }

    public function createShippingRow($gridName, $weight, $price)
    {
        $parameters = array(
            'name' => $gridName,
            'weight' => $weight,
            'price' => $price,
        );

        return $this->post('/pricer/shipping/row', array(), $parameters);
    }

    public function updateShippingRow($rowId, $gridName, $weight, $price)
    {
        $parameters = array(
            'name' => $gridName,
            'weight' => $weight,
            'price' => $price,
        );

        return $this->put('/pricer/shipping/row/'.$rowId, array(), $parameters);
    }

    public function deleteShippingRow($rowId)
    {
        return $this->delete('/pricer/shipping/row/'.$rowId);
    }

    public function deleteShippingGrid($gridName)
    {
        $parameters = array(
            'name' => $gridName
        );

        return $this->delete('/pricer/shipping/grid/', $parameters);
    }

    public function getStatisticsBbw()
    {
        return $this->get('/pricer/statistics/bbw');
    }

    public function getStatisticsOffers()
    {
        return $this->get('/pricer/statistics/offers');
    }

    public function getFieldsUsed()
    {
        return $this->get('/pricer/fields-used');
    }

    public function getFieldsToMap()
    {

        return $this->get('/pricer/configuration/fields-to-map');

    }

    protected function get($route, $params = array(), $body = array(), $headers = array(), $ttl = null)
    {
        $response = $this->getBody(parent::get($route, $params, $body, $headers, $ttl));
        $this->checkErrors($response);

        return $response;
    }

    protected function post($route, $params = array(), $body = array(), $headers = array(), $attachments = array())
    {
        $response = $this->getBody(parent::post($route, $params, $body, $headers));
        $this->checkErrors($response);

        return $response;
    }

    protected function put($route, $params = array(), $body = array(), $headers = array())
    {
        $response = $this->getBody(parent::put($route, $params, $body, $headers));
        $this->checkErrors($response);

        return $response;
    }

    protected function delete($route, $params = array(), $body = array(), $headers = array())
    {
        $response = $this->getBody(parent::delete($route, $params, $body, $headers));
        $this->checkErrors($response);

        return $response;
    }

    private function checkErrors($response)
    {
        if (isset($response->status) && $response->status === 'error' && isset($response->errors)) {
            throw new \Exception(implode(' ', $response->errors));
        }
    }

    private function getBody($response)
    {
        $response = json_decode($response);

        return isset($response->body) ? $response->body : $response;
    }
}
