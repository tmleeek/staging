<?php

class Phpro_Translate_Model_System_Config_Source_Modules {

    public function toArray() {
        $options = array();
        $collection = Mage::getModel('translate/translate')->getCollection();
        $collection->getSelect()->group('module');

        foreach ($collection as $locale) {
            $row = $locale->getData();
            $options[$row['module']] = $row['module'];
        }

        return $options;
    }

    public function getModulesForForm() {
        $modules[] = array('label' => "All", 'value' => "all");

        $moduleKeys = array_keys((array) Mage::getConfig()->getNode('modules')->children());
        foreach ($moduleKeys as $key => $className) {
            $modules[] = array('label' => $className, 'value' => $className);
        }
        return $modules;
    }

}