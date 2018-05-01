<?php

class MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Renderer_FavoriteWarehouse extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $retour = '';

        //init vars
        $value = $row->getis_favorite_warehouse();

        //textbox
        $checkboxName = 'is_favorite_warehouse';
        $checked = ($value == 1 ? ' checked ' : '');
        $retour = '<input type="radio" value="' . $row->getId() . '" id="' . $checkboxName . '" name="' . $checkboxName . '" ' . $checked . '>';

        return $retour;
    }

}