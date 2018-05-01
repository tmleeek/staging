<?php

class MDN_Mpm_Model_LastPricingCollection extends MDN_Mpm_Model_CustomCollection
{

    private $size;

    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->_isCollectionLoaded) {
            $products = Mage::helper('Mpm/Carl')->getLastPricing();
            $this->size = $products->total;
            foreach($products->results as $product) {
                $productData = new Varien_Object();
                $this->addPricingInformation($productData, $product);
                $this->addItem($productData);
            }

            $this->_isCollectionLoaded = true;
        }

        return $this;
    }

    private function addPricingInformation(Varien_Object $productData, $product)
    {
        $productData->id = uniqid();
        $productData->channel = $product->channel;
        $productData->reference = $product->reference;
        $productData->product_id = $product->product_id;
        $productData->status = $product->status;
        if (($pos = strpos($product->error, ":")) !== FALSE) {
            $productData->error = substr($product->error, $pos+1);
        }else{
            $productData->error = $product->error;
        }
        $productData->my_position = $product->my_position == 0 ? 'NC' : $product->my_position;
        $productData->target_position = $product->target_position == 0 ? 'NC' : $product->target_position;
        $productData->best_offer = $product->best_offer;
        $productData->base_cost = $product->base_cost;
        $productData->tax_amount = $product->tax_amount;
        $productData->commission_amount = $product->commission_amount;
        $productData->final_margin = $product->final_margin;
        $productData->margin_for_bbw = $product->margin_for_bbw;
        $productData->final_price = $product->final_price;
        $productData->shipping_price = $product->shipping_price;
        $productData->created_at = $product->created_at;
        $productData->updated_at = $product->updated_at;
        $productData->behavior = $product->behavior;
        $productData->rules_result = $product->rules_result;

        return $productData;
    }

    public function getSize()
    {
        return $this->size;
    }
}
