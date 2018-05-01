<?php
class  MDN_Colissimo_Model_System_Config_ParcelType extends Mage_Core_Model_Abstract
{
    private $_config;
    private $_contract_number = null;
    private $_password = null;
    private $_types = array(
            '1' => 'Classic',
            '2' => 'Roll',
        );

    public function toOptionArray(){
        return $this->_types;
    }

    public function getNameById($id){
        return $this->_types[$id];
    }
}