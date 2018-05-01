<?php

class MDN_AdvancedStock_Block_MassStockEditor_Widget_Grid_Column_Renderer_Stock extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $stockId = $row->getId();
        $qty = (int) $row->getqty();

        $onChange = 'onchange="persistantGrid.logChange(this.name, \''.$qty.'\')"';
        $retour = '<input type="text" name="qty_' . $stockId . '" id="qty_' . $stockId . '" value="' . $qty . '" size="4" '.$onChange.'>';
        return $retour;
    }

}