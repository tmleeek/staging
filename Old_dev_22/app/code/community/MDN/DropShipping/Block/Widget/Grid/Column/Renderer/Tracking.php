<?php

class MDN_DropShipping_Block_Widget_Grid_Column_Renderer_Tracking extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $name = 'trackings['.$row->getId().']';
        return '<input type="text" name="'.$name.'" id="'.$name.'" value="" size="20">';
    }

}