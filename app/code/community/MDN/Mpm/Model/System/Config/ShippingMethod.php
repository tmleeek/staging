<?php

class MDN_Mpm_Model_System_Config_ShippingMethod extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    /**
     *
     * @return type
     */
    public function getAllOptions() {

        if (!$this->_options) {
            $this->_options = array();
            $this->_options[] = array('value' => '', 'label' => '');
            $config = Mage::getStoreConfig('carriers');

            foreach ($config as $code => $methodConfig) {
                try
                {
                    if (isset($methodConfig['model'])) {
                        //load allowed methods
                        $model = mage::getModel($methodConfig['model']);
                        if ($model) {
                            $methods = $model->getAllowedMethods();
                            if ($methods) {
                                foreach ($methods as $key => $value) {
                                    $this->_options[] = array('value' => $key, 'label' => $methodConfig['title'] . ' - ' . $value);
                                }
                            }
                        }
                    }
                }
                catch(Exception $ex)
                {
                    //nothing
                }
            }
        }
        return $this->_options;
    }

    /**
     *
     * @return type
     */
    public function toOptionArray() {
        return $this->getAllOptions();
    }

}
