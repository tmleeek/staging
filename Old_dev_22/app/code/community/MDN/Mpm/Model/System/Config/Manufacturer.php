<?php

/**
 * Class MDN_Mpm_Model_System_Config_Manufacturer
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Model_System_Config_Manufacturer extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if(!$this->_options){

            $this->_options = array();
            $this->_options[] = array('value' => '', 'label' => 'All');

            $attribute = Mage::getModel('eav/entity_attribute')
                ->loadByCode('catalog_product', 'manufacturer');

            $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attribute->getData('attribute_id'))
                ->setStoreFilter(0, false);

            foreach($valuesCollection as $value) {
                $this->_options[] = array('value' => $value->getOptionId(), 'label' => $value->getValue());
            }

        }

        return $this->_options;
    }

    /**
     * @return array
     */
    public function toOptionArray(){
        return $this->getAllOptions();
    }

}