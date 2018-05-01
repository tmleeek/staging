<?php

class MDN_Mpm_Block_Products_Tabs_Pricing extends Mage_Adminhtml_Block_Widget  {

    protected $_offerInformation = null;
    protected $_productSetting = null;
    protected $_debugData = array();
    protected $_currentLog = null;
    protected $showRules = false;
    protected $margins;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('Mpm/Products/Tabs/Pricing.phtml');
    }

    public function getProduct()
    {
        return Mage::registry('mpm_product');
    }

    public function getChannel()
    {
        return Mage::registry('mpm_channel');
    }

    public function getDebugData($behaviour)
    {
        try {
            if (!isset($this->_debugData[$behaviour])) {
                $rules = array('behavior' => $behaviour);
                $pricing = Mage::helper('Mpm/Carl')->simulatePricing($this->getProduct()->getProductId(), $this->getProduct()->getChannel(), $rules);

                foreach(json_decode($pricing->rules_result) as $rule) {
                    $ruleType = strtolower($rule->type);
                    if($ruleType != 'final_price' && isset($pricing->$ruleType)) {
                        $pricing->$ruleType+= $rule->result;
                    } else {
                        $pricing->$ruleType = $rule->result;
                    }
                }

                $pricing->margin = $this->getMargin($this->getProduct()->getProductId(), $this->getProduct()->getChannel(), $behaviour);
                $this->_debugData[$behaviour] = $pricing;
            }

            if($pricing->status === 'error') {
                throw new Exception('Pricing error : '.$pricing->error);
            }

            $debug = 1;

            return $this->_debugData[$behaviour];
        } catch(Exception $ex) {
            return $ex->getMessage();
        }
    }

    private function getMargin($productId, $channel, $behavior)
    {
        if($this->margins === null) {
            $productRules = Mage::helper('Mpm/Carl')->getRulesFromProduct($productId, $channel);
            foreach($productRules as $rule) {
                if($rule->type === 'MARGIN') {
                    $this->margins['normal'] = $rule->normal;
                    $this->margins['aggressive'] = $rule->aggressive;
                }
            }
        }

        return isset($this->margins[$behavior]) ? $this->margins[$behavior] : '';
    }

    public function getProductSetting($behaviour = null)
    {
        if (!isset($this->_productSetting))
        {
            $this->_productSetting = Mage::getSingleton('Mpm/Product_Setting')->getForProductChannel($this->getProduct()->getId(), $this->getChannel());
        }

        if ($behaviour != null)
        {
            $this->_productSetting->setuse_config_behaviour(0);
            $this->_productSetting->setbehaviour($behaviour);
        }

        return $this->_productSetting;
    }

    public function getBehaviours()
    {
        return
            array(
                array( "value" => "normal" ,"label" =>"Conservative" ),
                array( "value" => "aggressive" ,"label" =>"Moderate" ),
                array( "value" => "harakiri" ,"label" =>"Aggressive" ),
            );
    }

    public function getBestOffer()
    {
        $productOffers = Mage::helper('Mpm/Product')->getOffers($this->getProduct());
        return Mage::helper('Mpm/Product')->getBestOffer($productOffers, $this->getChannel());
    }

    public function getColorForRepricingStatus($status)
    {
        return Mage::helper('Mpm/Pricing')->getColorForRepricingStatus($status);
    }


    public function getCurrentLog()
    {
        if ($this->_currentLog === null) {
            $this->_currentLog = $this->getProduct();
        }

        return $this->_currentLog;
    }

    public function getCostDetails($debugData)
    {
        $baseCost = $costShipping = 0;
        foreach(json_decode($debugData->rules_result) as $ruleResult) {
            if($ruleResult->type === 'COST') {
                $baseCost = $ruleResult->result;
            }

            if($ruleResult->type === 'COST_SHIPPING') {
                $costShipping = round($ruleResult->result, 2);
            }
        }

        $html = '<ul>';
        $html .= '<li style="float: none;">'.$this->__('Base cost').' '.$this->currency($debugData->channel).' '.$baseCost.'</li>';
        $html .= '<li style="float: none;">'.$this->__('Cost shipping').' '.$this->currency($debugData->channel).' '.$costShipping.'</li>';
        if (isset($debugData->additional_cost))
            $html .= '<li style="float: none;">'.$this->__('Additional cost').' '.$this->currency($debugData->channel).' '.$debugData->additional_cost.'</li>';
        if (isset($debugData->commission))
            $html .= '<li style="float: none;">'.$this->__('Commission').' ('.$debugData->commission.'%): '.$this->currency($debugData->channel).' '.number_format((float)$debugData->commission_amount, 2, '.', '').'</li>';
        $html .= '<li style="float: none;">'.$this->__('Tax').' ('.$debugData->tax_rate.'%): '.$this->currency($debugData->channel).' '.number_format((float)$debugData->tax_amount, 2, '.', '').'</li>';
        $html .= '</ul>';

        return $html;
    }

    public function getSmileyUrl($status)
    {
        $imageUrl = Mage::getSingleton('Mpm/System_Config_PricingStatus')->getSmileyUrl($status);
        return $imageUrl;
    }

    public function currency($channel)
    {
        return Mage::helper('Mpm/Pricing')->getCurrency($channel);
    }

    public function getRules($pricing)
    {
        $rules = array();

        foreach(json_decode($pricing->rules_result) as $rule) {
            if($rule->id === 0) {
                continue;
            }

            $rule = Mage::helper('Mpm/Carl')->getRuleById($rule->id);
            $item = array();
            $item['type'] = $rule->type;
            $item['url'] = '';
            $item['label'] = $rule->name;

            $rules[] = $item;
        }

        return $rules;
    }

    public function showRules()
    {
        return $this->showRules;
    }

    /**
     * @param boolean $showRules
     */
    public function setShowRules($showRules)
    {
        $this->showRules = $showRules;
    }

    /**
     * @return string
     */
    public function getChannelCurrency()
    {
        $currency = Mage::registry('mpm_currency');
        if($currency === null){
            return $this->currency($this->getChannel());
        }
        return Mage::app()->getLocale()->currency($currency)->getSymbol();
    }

    /**
     * @param string $currency
     * @param stdClass $data
     * @return string
     */
    public function getFinalPrice($currency, $data){

        $debug = json_decode($data->debug, true);

        return Mage::Helper('Mpm/FinalPrice')->getValue($debug, $currency);

    }


}