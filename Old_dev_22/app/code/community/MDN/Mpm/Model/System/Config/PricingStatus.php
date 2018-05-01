<?php

class MDN_Mpm_Model_System_Config_PricingStatus extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    /**
     *
     * @return type
     */
    public function getAllOptions()
    {

        if (!$this->_options) {
            $this->_options = array();

            $this->_options[] = array('value' => 'buy_box_winner', 'label' => Mage::helper('Mpm')->__('Target reached'));
            $this->_options[] = array('value' => 'challenger', 'label' => Mage::helper('Mpm')->__('Challenger'));
            $this->_options[] = array('value' => 'out_of_competition', 'label' => Mage::helper('Mpm')->__('Runner'));
            $this->_options[] = array('value' => 'no_offers', 'label' => Mage::helper('Mpm')->__('No competitor'));
            $this->_options[] = array('value' => 'error', 'label' => Mage::helper('Mpm')->__('In Error'));
            $this->_options[] = array('value' => 'disable', 'label' => Mage::helper('Mpm')->__('Disable'));

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

    public function getSmileyUrl($status)
    {
        $img = null;
        switch($status)
        {
            case 'out_of_competition': $img =  'red'; break;
            case 'buy_box_winner': $img =  'green'; break;
            case 'not_associated': $img =  'green'; break;
            case 'no_offers': $img =  'green'; break;
            case 'challenger': $img =  'orange'; break;
            default: $img = 'grey'; break;
        }
        return Mage::getDesign()->getSkinUrl('Mpm/images/smileys/'.$img.'.png');
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    public function toArrayKey()
    {
        $array = array();
        foreach($this->getAllOptions() as $opt) {
            $array[$opt['value']] = $opt['label'];
        }
        return $array;
    }

}
