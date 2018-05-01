<?php

class Phpro_Translate_Block_Adminhtml_Renderer_String extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        $explode = explode('::', $value);
        $string = (isset($explode[1])) ? $explode[1] : $value;
        
        return $string;
    }

}
