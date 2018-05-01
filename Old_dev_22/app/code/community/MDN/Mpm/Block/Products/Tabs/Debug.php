<?php

class MDN_Mpm_Block_Products_Tabs_Debug extends Mage_Adminhtml_Block_Widget  {

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('Mpm/Products/Tabs/Debug.phtml');
    }

    public function getProduct()
    {
        return Mage::registry('mpm_product');
    }

    public function getChannel()
    {
        return Mage::registry('mpm_channel');
    }

    public function getDebug()
    {
        $debug = json_decode($this->getProduct()->getDebug(), true);
        $debug = json_encode($this->convertFloatToString($debug));

        $debug = str_replace("'", "\\'", str_replace('\\', '\\\\', $debug));

        return $debug;
    }

    private function convertFloatToString($response)
    {
        foreach ($response as &$value) {
            if (is_array($value)) {
                $value = $this->convertFloatToString($value);
            } elseif (is_float($value)) {
                $value = (string)round($value, 2);
            }
        }

        return $response;
    }

    public function getHumanExplanations()
    {
        $data = $this->getProduct()->getData();

        return $this->__('Calculate price for product %s and channel %s', $data['product_id'], $data['channel']);
    }
}