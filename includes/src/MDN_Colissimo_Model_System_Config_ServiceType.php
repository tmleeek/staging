<?php
class  MDN_Colissimo_Model_System_Config_ServiceType extends Mage_Core_Model_Abstract
{
    private $_config;
    private $_contract_number = null;
    private $_password = null;

    public function toOptionArray(){
        $types = array(
            '7R' => 'International',
            '8R' => 'France',
        );

        return $types;
    }
}