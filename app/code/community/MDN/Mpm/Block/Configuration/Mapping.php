<?php

class MDN_Mpm_Block_Configuration_Mapping extends Mage_Adminhtml_Block_Widget_Container {

    public function getFieldsToMap(){

        try {
            return Mage::Helper('Mpm/Carl')->getFieldsToMap();
        }catch(Exception $e){
            return $e->getMessage();
        }

    }

}