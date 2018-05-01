<?php

class MDN_Mpm_Helper_Rule extends Mage_Core_Helper_Abstract {

    public function areSetup()
    {
        //if there are custom rules, ok
        $hasCustomRule = Mage::getModel('Mpm/Rule')->getCollection()->addFieldToFilter('is_system', 0)->getSize();
        if ($hasCustomRule)
            return true;

        //if all system rules are configure
        $collection = Mage::getModel('Mpm/Rule')->getCollection()->addFieldToFilter('is_system', 1);
        foreach($collection as $item)
        {
            $data = unserialize($item->getcontent());
            if (!is_array($data) || count($data) == 0)
                return false;
        }

        return true;
    }

}