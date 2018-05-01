<?php

class Phpro_Translate_Model_System_Config_Source_Groups {

    public function toOptionArray($isMultiSelect) {
        $groups = Mage::getModel('customer/group')->getCollection();

        $options = array();
        $options[] = array('value' => -1, 'label' => 'All');
        foreach ($groups as $group) {
            $options [] = array('value' => $group->getId(), 'label' => $group->getCode());
        }
        return $options;
    }

    public function toArray() {
        $groups = Mage::getModel('customer/group')->getCollection();

        $options = array();
        foreach ($groups as $group) {
            $options [] = array($group->getId() => $group->getCode());
        }
        return $options;
    }

}

