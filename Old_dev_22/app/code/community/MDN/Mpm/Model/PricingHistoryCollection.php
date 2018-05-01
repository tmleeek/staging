<?php

class MDN_Mpm_Model_PricingHistoryCollection extends MDN_Mpm_Model_CustomCollection
{

    private $size;
    private $productId;
    private $channel;

    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->_isCollectionLoaded) {

            $history = Mage::helper('Mpm/Carl')->getProductPricingHistory($this->productId, $this->channel);
            $this->size = count($history);
            foreach($history as $pricing) {
                $pricingData = new Varien_Object();

                foreach($pricing->rules_result as $rule) {
                    $ruleType = strtolower($rule->type);
                    if(isset($pricing->$ruleType)) {
//                        $pricing->$ruleType+= $rule->result;
                    } else {
                        $pricing->$ruleType = $rule->result;
                    }
                }

                $pricingData->status = $pricing->status;
                $pricingData->created_at = $pricing->created_at;
                $pricingData->buy_box_winner = $pricing->buy_box_winner;
                $pricingData->final_price = $pricing->final_price;
                $pricingData->margin = $pricing->final_margin;
                $pricingData->my_rank = $pricing->target_position;
                $pricingData->my_position = $pricing->my_position;
                $pricingData->target_position = $pricing->target_position;

                foreach($pricing->rules_result as $rule) {
                    if($rule->type === 'BEHAVIOR') {
                        $pricingData->behaviour = $rule->result;
                    }
                }

                $this->addItem($pricingData);
            }

            $this->_isCollectionLoaded = true;
        }

        return $this;
    }

    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    public function getSize()
    {
        return $this->size;
    }
}
