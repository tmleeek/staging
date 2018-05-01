<?php

class MDN_Mpm_Block_Products_Tabs_Matching extends Mage_Adminhtml_Block_Widget  {

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('Mpm/Products/Tabs/Matching.phtml');
    }

    public function getProduct()
    {
        return Mage::registry('mpm_product');
    }

    public function getChannel()
    {
        return Mage::registry('mpm_channel');
    }

    public function getUrls()
    {
        $urls = Mage::helper('Mpm/Carl')->getMatchingByUrls($this->getProduct()->product_id);

        return is_array($urls) ? implode("\n", $urls): '';
    }
}