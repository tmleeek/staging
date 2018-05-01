<?php

/**
 *
 *
 */
class MDN_Mpm_Model_Pricer extends Mage_Core_Model_Abstract {

    public $_debug = array();

    const kPricingStatusError = 'error';
    const kPricingStatusNoOffers = 'no_offers';
    const kPricingStatusCompeteForFirstPosition = 'compete_for_first_position';
    const kPricingStatusCompeteNotFarFromFirstPosition = 'not_far_from_first_position';
    const kPricingStatusOutOfCompetition = 'out_of_competition';

    const kCompeteWithBestPrice = 'best_price';
    const kCompeteWithBestRank = 'bbw';

    const kShippingCalculationMethodPercentage = 'percentage';
    const kShippingCalculationMethodFixed = 'fixed';

    const kPricingMethodStandard = 'standard';
    const kPricingMethodAggressive = 'aggressive';
    const kPricingMethodHarakiri = 'harakiri';

    const kAdjustmentMethodPercent = 'percent';
    const kAdjustmentMethodValue = 'value';

    const kNoCompetitorModeMargin = 'margin';
    const kNoCompetitorModePrice = 'price';

    const kNotFarPricePercent = 5;

    /**
     * Calculate & store prices for every channel for product
     */
    public function processProduct($product, $channels = null)
    {
        $start = time();
        if (is_numeric($product))
            $product = Mage::getModel('catalog/product')->load($product);

        if ($channels == null)
            $channels = Mage::helper('Mpm/Carl')->getChannelsSubscribed();
        else
        {
            if (!is_array($channels))
            {
                $channel = new stdClass();
                $channel->channelCode = $channels;
                $channels = array($channel);
            }
        }

        foreach($channels as $channel)
        {
            $error = 0;
            $debug = '';
            $price = 0;
            try
            {
                $this->_debug = array();

                $price = $this->calculatePrice($product, $channel->channelCode);
                $this->storePrice($product, $channel->channelCode, $price);
            }
            catch(Exception $ex)
            {
                $error = 1;
                $debug = $ex->getMessage();
                Mage::helper('Mpm')->log('Error pricing product #'.$product->getId().' for channel '.$channel->channelCode.' : '.$ex->getMessage());
            }

            Mage::getSingleton('Mpm/PricingLog')->addLog($product->getId(),
                                                         $channel->channelCode,
                                                         null,
                                                         $this->getKeyIfExist($this->_debug, 'formula'),
                                                         $price,
                                                        $this->getKeyIfExist($this->_debug, 'status'),
                                                         $this->getKeyIfExist($this->_debug, 'behaviour'),
                                                         $error,
                                                         $debug,
                                                         $this->getKeyIfExist($this->_debug, 'best_offer_seller_name'),
                                                         $this->getKeyIfExist($this->_debug, 'best_offer'),
                                                         $this->getKeyIfExist($this->_debug, 'my_rank'),
                                                         $this->getKeyIfExist($this->_debug, 'final_margin'),
                                                         $this->getKeyIfExist($this->_debug, 'margin_for_bbw'),
                                                         $this->getKeyIfExist($this->_debug, 'cost'),
                                                         $this->getKeyIfExist($this->_debug, 'commission')
                                                            );
        }

        Mage::helper('Mpm')->log('processProduct product #'.$product->getId().' : '.(time() - $start).'s');
    }

    protected function getKeyIfExist($debug, $key)
    {
        if (isset($debug[$key]))
            return $debug[$key];
        else
            return false;
    }

    /**
     * Store calculated price in attribute(s)
     *
     * @param $product
     * @param $channel
     * @param $totalPrice
     */
    public function storePrice($product, $channel, $totalPrice)
    {
        if (Mage::getStoreConfig('mpm/repricing/test_mode')) {
            Mage::helper('Mpm')->log('Price store is disbable (test mode enabled');
            return false;
        }

        if (is_object($channel))
            $channel = $channel->channelCode;

        $priceAttribute = Mage::getStoreConfig('mpm/repricing/price_attributes_'.$channel);
        $shippingAttribute = Mage::getStoreConfig('mpm/repricing/shipping_attributes_'.$channel);

        if (!$priceAttribute)
            throw new Exception('Price attribute is not set for '.$channel);

        $shippingPrice = 0;
        $price = $totalPrice;
        if ($shippingAttribute)
        {
            $shippingPriceCalculationRule = $this->getRulesForProduct($product->getId(), $channel, MDN_Mpm_Model_Rule::kTypeShippingPrice, true);
            if ($shippingPriceCalculationRule)
            {
                $value = $shippingPriceCalculationRule->getFormula(MDN_Mpm_Model_Rule::kFormulaShippingPriceValue);
                switch($shippingPriceCalculationRule->getFormula(MDN_Mpm_Model_Rule::kFormulaShippingPriceMode))
                {
                    case self::kShippingCalculationMethodPercentage:
                        $shippingPrice = $totalPrice / 100 * $value;
                        $price = $totalPrice - $shippingPrice;
                        break;
                    case self::kShippingCalculationMethodFixed:
                        $shippingPrice = $value;
                        $price = $totalPrice - $shippingPrice;
                        break;
                }
            }
        }

        if ($price <= 0)
            throw new Exception('Can not store negative price');
        if ($shippingPrice < 0)
            throw new Exception('Can not store negative shipping price');

        $action = Mage::getModel('catalog/resource_product_action');
        $attributes = array($priceAttribute => $price);
        if ($shippingAttribute)
            $attributes[$shippingAttribute] = $shippingPrice;
        $action->updateAttributes(array($product->getId()), $attributes);
        foreach($attributes as $k => $v)
        {
            Mage::helper('Mpm')->log('Store for product #'.$product->getId().' on '.$channel.' : '.$k.' = '.$v);
        }

        if (Mage::getStoreConfig('mpm/repricing/change_product_updated_at'))
        {
            Mage::helper('Mpm/Product')->touchUpdatedAt($product->getId());
        }

    }

    /**
     * Calculate price for product / channel and try to get the best position
     *
     * @param $product
     * @param $channel
     * @return bool
     */
    public function calculatePrice($product, $channel, $productSettings = null)
    {
        $result = false;

        $this->_debug = array();

        if ($this->pricingIsDisabled($product, $channel))
            throw new Exception('Pricing disabled');

        if ($productSettings == null)
            $productSettings = Mage::getSingleton('Mpm/Product_Setting')->getForProductChannel($product->getId(), $channel);

        //cost rules
        $costRule = $this->getRulesForProduct($product->getId(), $channel, MDN_Mpm_Model_Rule::kTypeCost, true);
        if (!$costRule)
            throw new Exception('No cost rule available for product #'.$product->getId().' and channel '.$channel);
        $this->_debug['rules']['cost'] = $costRule->getId();

        //margin rule
        $marginRule = $this->getRulesForProduct($product->getId(), $channel, MDN_Mpm_Model_Rule::kTypeMargin, true);
        if (!$marginRule)
            throw new Exception('No margin rule available for product #'.$product->getId().' and channel '.$channel);
        $this->_debug['rules']['margin'] = $marginRule->getId();

        //shipping rule
        $shippingRule = $this->getRulesForProduct($product->getId(), $channel, MDN_Mpm_Model_Rule::kTypeShipping, true);
        if (!$shippingRule)
            throw new Exception('No shipping rule available for product #'.$product->getId().' and channel '.$channel);
        $this->_debug['rules']['shipping'] = $shippingRule->getId();

        //adjustment rule
        $adjustmentRule = $this->getRulesForProduct($product->getId(), $channel, MDN_Mpm_Model_Rule::kTypeAdjustment, true);
        if (!$adjustmentRule)
            throw new Exception('No adjustment rule available for product #'.$product->getId().' and channel '.$channel);
        $this->_debug['rules']['adjustment'] = $adjustmentRule->getId();

        //no competitor rule
        $noCompetitorRule = $this->getRulesForProduct($product->getId(), $channel, MDN_Mpm_Model_Rule::kTypeNoCompetitor, true);
        if (!$noCompetitorRule)
            throw new Exception('No no competitor rule available for product #'.$product->getId().' and channel '.$channel);
        $this->_debug['rules']['no_competitor'] = $noCompetitorRule->getId();

        //calculate baseCost
        $baseCost = $this->calculateCost($product, $channel, $costRule, $shippingRule);
        $this->_debug['cost'] = $baseCost;


        //get commission
        $commission = Mage::getModel('Mpm/Commission')->getPercent($product->getId(), $channel);
        $this->_debug['commission'] = $commission;

        //tax rate
        $taxRate = $this->getTaxRate($product, $channel);

        //offer to compete with
        $offerToBeat = $this->getOfferToBeat($product, $channel, $adjustmentRule);

        //get prices
        $this->_debug['behaviour'] = $productSettings->getBehaviour();
        $minMargin = $marginRule->getFormula($productSettings->getBehaviour());
        $this->_debug['min_margin'] = number_format($minMargin, 1, '.', '');
        $minimumPrice = $this->getMinimumPrice($product, $channel, $productSettings, $baseCost, $marginRule, $commission, $taxRate);
        $this->_debug['minimum_price'] = $minimumPrice;
        $regularPrice = $this->getRegularPrice($product, $channel, $productSettings, $baseCost, $noCompetitorRule, $commission, $taxRate);
        $this->_debug['regular_price_data'] = $regularPrice;
        $this->_debug['regular_price'] = $regularPrice;

        //set status
        $canCompeteBestOffer = ($minimumPrice < $offerToBeat);
        $status = 'undefined';
        if (!$offerToBeat)
            $status = self::kPricingStatusNoOffers;
        else
        {
            if ($canCompeteBestOffer)
                $status = self::kPricingStatusCompeteForFirstPosition;
            else
            {
                $status = self::kPricingStatusOutOfCompetition;
            }
        }

        //adjust price
        $result = $this->adjustPrice($status, $offerToBeat, $minimumPrice, $regularPrice, $adjustmentRule);

        //check min / max prices
        $result = $this->checkMinMaxPrice($channel, $product, $result);

        $this->_debug['result'] = $result;

        //calculate margin
        $taxAmount = $result - $result / (100 + $taxRate) * 100;
        $this->_debug['tax_amount'] = $taxAmount;

        //calculate commission amount
        $priceHt = $result - $taxAmount;
        $commissionAmount = $priceHt - $priceHt / (100 + $commission) * 100;
        $this->_debug['commission_amount'] = $commissionAmount;

        $this->_debug['net_margin_amount'] = $result - $taxAmount - $commissionAmount - $this->_debug['cost'];
        $margin = $this->_debug['net_margin_amount'] / $this->_debug['base_cost'] * 100;
        $this->_debug['final_margin'] = round($margin, 1);

        if ($status == self::kPricingStatusNoOffers)
        {
            $this->_debug['margin_for_bbw'] = 99.9;
        }
        else
        {
            $offerToBeatWithAdjustment = $this->adjustPrice(self::kPricingStatusCompeteForFirstPosition, $offerToBeat, 0, 0, $adjustmentRule);
            $finalPriceWithoutTax = $offerToBeatWithAdjustment / (1 + $commission / 100) / (1 + $taxRate / 100);
            $marginForBbw = ($finalPriceWithoutTax - $this->_debug['cost']) / $this->_debug['cost'] * 100;
            $this->_debug['margin_for_bbw'] = round($marginForBbw, 1);
        }

        //change status to kPricingStatusCompeteNotFarFromFirstPosition if aggressive behaviour can beat the BBW
        if ($status == self::kPricingStatusOutOfCompetition) {
            if ($this->_debug['margin_for_bbw'] >= $this->_debug['min_margins']['aggressive']) {
                $status = self::kPricingStatusCompeteNotFarFromFirstPosition;
            }
        }
        $this->_debug['status'] = $status;

        //calculate rank
        $this->_debug['my_rank'] = Mage::helper('Mpm/Product')->simulateRank($product->getId(), $channel, $result);

        if ($result <= 0)
            throw new Exception('Price calculation returns 0 !');

        Mage::helper('Mpm')->log('Calculate price for product #'.$product->getId().' for channel '.$channel.' : '.$result);

        return $result;
    }

    protected function checkMinMaxPrice($channel, $product, $price)
    {
        $minPriceRule = $this->getRulesForProduct($product->getId(), $channel, MDN_Mpm_Model_Rule::kTypeMinPrice, true);
        if ($minPriceRule)
        {
            $minPrice = $minPriceRule->getFormula(MDN_Mpm_Model_Rule::kFormulaMinPrice);
            if ($price < $minPrice)
                $price = $minPrice;
        }

        $maxPriceRule = $this->getRulesForProduct($product->getId(), $channel, MDN_Mpm_Model_Rule::kTypeMaxPrice, true);
        if ($maxPriceRule)
        {
            $maxPrice = $maxPriceRule->getFormula(MDN_Mpm_Model_Rule::kFormulaMaxPrice);
            if ($price > $maxPrice)
                $price = $maxPrice;
        }

        return $price;
    }

    protected function adjustPrice($status, $offerToBeat, $minimumPrice, $regularPrice, $adjustmentRule)
    {
        $this->_debug['formula'] = 'undefined';
        switch($status)
        {
            case 'undefined':
                throw new Exception('Unable to define status');
                break;
            case self::kPricingStatusNoOffers:
                $result = $regularPrice;
                $this->_debug['formula'] = $this->_debug['regular_price_data']['final_formula'];
                break;
            case self::kPricingStatusCompeteForFirstPosition:
                $adjustmentMethod = $adjustmentRule->getFormula(MDN_Mpm_Model_Rule::kFormulaAdjustmentMethod);
                $adjustmentValue = $adjustmentRule->getFormula(MDN_Mpm_Model_Rule::kFormulaAdjustmentValue);
                switch($adjustmentMethod)
                {
                    case self::kAdjustmentMethodValue:
                        $result = $offerToBeat - $adjustmentValue;
                        $this->_debug['formula'] = $offerToBeat.' - '.$adjustmentValue;
                        break;
                    case self::kAdjustmentMethodPercent:
                        $result = $offerToBeat - ($offerToBeat / 100 * $adjustmentValue);
                        $this->_debug['formula'] = $offerToBeat.' - ('.$offerToBeat.' / 100 * '.$adjustmentValue.')';
                        break;
                }

                break;
            case self::kPricingStatusCompeteNotFarFromFirstPosition:
            case self::kPricingStatusOutOfCompetition:
                $result = $minimumPrice;
                $this->_debug['formula'] = $this->_debug['minimum_price_data']['final_formula'];
                break;
        }

        //make sure that result is between min & max price
        if ($result < $minimumPrice)
            $result = $minimumPrice;

        return $result;
    }

    protected function getOfferToBeat($product, $channel, $adjustmentRule)
    {
        $allOffers = Mage::helper('Mpm/Product')->getOffers($product);
        $offerToBeat = Mage::helper('Mpm/Product')->getBestOffer($allOffers, $channel, true, $adjustmentRule->getFormula(MDN_Mpm_Model_Rule::kFormulaAdjustmentCompeteWith));
        $bestOffer = Mage::helper('Mpm/Product')->getBestOffer($allOffers, $channel, false, $adjustmentRule->getFormula(MDN_Mpm_Model_Rule::kFormulaAdjustmentCompeteWith));
        if ($offerToBeat)   //todo : understand this code !!!!
        {
            $this->_debug['best_offer'] = $bestOffer->getTotal();
            $this->_debug['best_offer_seller_name'] = $bestOffer->getseller_name();
        }
        else
        {
            $this->_debug['best_offer'] = 0;
            $this->_debug['best_offer_seller_name'] = '-';
        }
        $offerToBeat = ($offerToBeat ? $offerToBeat->getTotal() : 0);
        return $offerToBeat;
    }

    protected function calculateCost($product, $channel, $costRule, $shippingRule)
    {
        //base cost
        $baseCost = $costRule->calculate($product, $channel, MDN_Mpm_Model_Rule::kFormulaCost);
        $baseCost = $baseCost["result"];
        $this->_debug['costs']['base'] = $baseCost;

        //shipping cost
        $shippingCost = $this->getShippingCost($product, $shippingRule, $channel);

        //additional cost
        $additionalCosts = 0;
        $additionalCostRules = $this->getRulesForProduct($product->getId(), $channel, MDN_Mpm_Model_Rule::kTypeAdditionalCost);
        foreach($additionalCostRules as $rule)
        {
            $value = $rule->calculate($product, $channel, MDN_Mpm_Model_Rule::kFormulaAdditionalCostParam, array('base_cost' => $baseCost, 'shipping' => $shippingCost));
            $value = $value['result'];
            $additionalCosts += $value;
            $this->_debug['costs'][$rule->getName()] = $value;
        }

        $this->_debug['base_cost'] = $baseCost + $shippingCost + $additionalCosts;
        return $this->_debug['base_cost'];
    }


    protected function getShippingCost($product, $shippingRule, $channel)
    {
        $shippingMethod = $shippingRule->getFormula(MDN_Mpm_Model_Rule::kFormulaShippingMethod);
        $shippingCoef = $shippingRule->getFormula(MDN_Mpm_Model_Rule::kFormulaShippingCoefficient);
        $shippingAllowZero = $shippingRule->getFormula(MDN_Mpm_Model_Rule::kFormulaShippingAllowZero);
        $countryCode = Mage::getStoreConfig('mpm/repricing/country_'.$channel);
        $region = Mage::getStoreConfig('mpm/repricing/region_'.$channel);
        $postCode = Mage::getStoreConfig('mpm/repricing/postcode_'.$channel);
        $currency = 'EUR';
        $storeId  = Mage::getStoreConfig('mpm/repricing/store_'.$channel);

        $this->_debug['shipping_settings']['method'] = $shippingMethod;
        $this->_debug['shipping_settings']['country'] = $countryCode;
        $this->_debug['shipping_settings']['region'] = $region;
        $this->_debug['shipping_settings']['postcode'] = $postCode;

        $shippingCost = Mage::helper('Mpm/Shipping')->getRate($product, $currency, $shippingMethod, $countryCode, $storeId, $region, $postCode);
        $shippingCost = $shippingCost * $shippingCoef;

        $this->_debug['costs']['shipping'] = $shippingCost;
        $this->_debug['shipping_settings']['rate'] = $shippingCost;

        if (!$shippingAllowZero) {
            if ($shippingCost == '')
                throw new Exception('No shipping cost available');
        }

        return $shippingCost;
    }

    protected function getTaxRate($product, $channel)
    {
        $countryCode = Mage::getStoreConfig('mpm/repricing/country_'.$channel);
        $region = Mage::getStoreConfig('mpm/repricing/region_'.$channel);
        $postCode = Mage::getStoreConfig('mpm/repricing/postcode_'.$channel);
        $storeId = Mage::getStoreConfig('mpm/repricing/store_'.$channel);

        $this->_debug['tax_settings']['country'] = $countryCode;
        $this->_debug['tax_settings']['region'] = $region;
        $this->_debug['tax_settings']['postcode'] = $postCode;
        $this->_debug['tax_settings']['storeId'] = $storeId;

        $taxRate = Mage::helper('Mpm/Tax')->getProductTaxRate($product, $storeId, $countryCode, $region, $postCode);
        $this->_debug['tax_settings']['rate'] = $taxRate;
        $this->_debug['tax_rate'] = $taxRate;

        return $taxRate;
    }


    public function getMinimumPrice($product, $channel, $productSettings, $baseCost, $marginRule, $commission, $taxRate)
    {
        if ($productSettings->getBehaviour() == self::kPricingMethodHarakiri)
            return 0;

        $margin = 0;
        switch($productSettings->getBehaviour())
        {
            case self::kPricingMethodStandard:
                $margin = $marginRule->getFormula(MDN_Mpm_Model_Rule::kFormulaMarginStandard);
                break;
            case self::kPricingMethodAggressive:
                $margin = $marginRule->getFormula(MDN_Mpm_Model_Rule::kFormulaMarginAggressive);
                break;
        }

        $this->_debug['min_margin'] = $margin;
        $this->_debug['min_margins']['standard'] = $marginRule->getFormula(MDN_Mpm_Model_Rule::kFormulaMarginStandard);
        $this->_debug['min_margins']['aggressive'] = $marginRule->getFormula(MDN_Mpm_Model_Rule::kFormulaMarginAggressive);

        $result = $baseCost * (1 + $margin / 100) *  (1 + $commission / 100) * (1 + $taxRate / 100);

        return $result;
    }

    public function getRegularPrice($product, $channel, $productSettings, $baseCost, $noCompetitorRule, $commission, $taxRate)
    {

        switch($noCompetitorRule->getFormula(MDN_Mpm_Model_Rule::kFormulaNoCompetitorMode))
        {
            case MDN_Mpm_Model_Pricer::kNoCompetitorModeMargin:
                $margin = $noCompetitorRule->getFormula(MDN_Mpm_Model_Rule::kFormulaNoCompetitorValue);
                $result = $baseCost * (1 + $commission / 100) * (1 + $margin / 100) * (1 + $taxRate / 100);
                break;
            case MDN_Mpm_Model_Pricer::kNoCompetitorModePrice:
                $result = $noCompetitorRule->calculate($product, $channel, MDN_Mpm_Model_Rule::kFormulaNoCompetitorValue, array('commission' => $commission, 'tax' => $taxRate));
                $result = $result['result'];
                break;
        }

        return $result;
    }

    public function pricingIsDisabled($product, $channel)
    {
        $rule = $this->getRulesForProduct($product->getId(), $channel, MDN_Mpm_Model_Rule::kTypeEnable, true);

        if ($rule != false)
        {
            $this->_debug['enabled'] = $rule->getId();
            return ($rule->getFormula(MDN_Mpm_Model_Rule::kFormulaEnable) == 0);
        }
        else
            return false;
    }

    public function getRulesForProduct($productId, $channel, $ruleType, $onlyFirst = false)
    {
        $collection = Mage::getModel('Mpm/Rule')
            ->getCollection()
            ->join('Mpm/Rule_Product',
                'rule_id=`main_table`.id')
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('channel', $channel)
            ->addFieldToFilter('type', $ruleType)
            ->setOrder('priority', 'DESC');
        if ($onlyFirst)
        {
            if (!$collection->getFirstItem()->getId())
                return false;
            $rule = Mage::getModel('Mpm/Rule')->load($collection->getFirstItem()->getrule_id());
            if (is_array($this->_debug))
            {
                $this->_debug['rules'][$ruleType] = $rule->getId();
            }
            return $rule;
        }
        return $collection;
    }
}
