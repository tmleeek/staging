<?php

class MDN_Mpm_Block_Products_Tabs_PricingCarl extends MDN_Mpm_Block_Products_Tabs_Pricing  {

    protected $_productKey = null;
    protected $_product = null;

    public function getProduct()
    {
        $productId =  Mage::registry('mpm_product')->getSku();
        $productKey = $productId . "-" . $this->getChannel();
        if($productKey !== $this->_productKey || $productKey === null || $this->_product === null){
            $products = (new MDN_Mpm_Model_PricingCollection());
            $products->addFieldToFilter('product_id', $productId );
            $products->addFieldToFilter('channel', $this->getChannel());
            $this->_product = $products->load()->getFirstItem();
        }

        return $this->_product;
    }
}