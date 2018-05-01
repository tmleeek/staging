<?php

class MDN_Mpm_Model_System_Config_RuleTypes extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    private $typeName = array(
        'adjustment' => 'Price adjustment',
        'behavior' => 'Behavior',
        'commission' => 'Commissions on sales',
        'cost' => 'Product cost',
        'cost_shipping' => 'Shipping cost',
        'additional_cost' => 'Additional cost',
        'enable' => 'Include / Exclude products',
        'export' => 'Products to export',
        'margin' => 'Minimum profit margin',
        'max_price' => 'Maximum price',
        'min_price' => 'Minimum price',
        'tax_rate' => 'Tax',
        'price_without_competitor' => 'Price offer without competitor',
        'shipping_price' => 'Shipping price'
    );

    public function getAllOptions() {

        if (!$this->_options) {
            $this->_options = array();

            foreach(Mage::helper('Mpm/Carl')->getRuleTypes() as $type) {
                $typeName = isset($this->typeName[$type]) ? $this->typeName[$type] : $type;
                $this->_options[] = array('value' => $type, 'label' => Mage::helper('Mpm')->__($typeName));
            }

        }
        return $this->_options;
    }

    public function translate($status)
    {
        $options = $this->getAllOptions();
        foreach($options as $opt)
            if ($opt['value'] == $status)
                return $opt['label'];
    }

    /**
     *
     * @return type
     */
    public function toOptionArray() {
        return $this->getAllOptions();
    }


    public function toArrayKey()
    {
        $array = array();
        foreach($this->getAllOptions() as $opt)
        {
            $array[$opt['value']] = $opt['label'];
        }
        return $array;
    }
    
}
