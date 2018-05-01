<?php

class MDN_Mpm_Model_System_Config_Backend_Mapping extends Mage_Core_Model_Config_Data
{

    /**
     * {@inherit}
     * todo Check if the value has changed
     */
    protected function _afterSave()
    {
        $value = $this->getData('groups/mapping/fields/'.$this->attribute.'_attribute/value');

        try {
            Mage::helper('Mpm/Carl')->postRule(
                'CLIENT-DATA.MAPPING.PRODUCT.'.strtoupper($this->attribute),
                sprintf('<?php return "%s";', $value)
            );
        } catch(\Exception $e) {

        }
    }
}