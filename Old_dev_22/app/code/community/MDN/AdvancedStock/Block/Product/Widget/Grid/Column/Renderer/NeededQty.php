<?php

class MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Renderer_NeededQty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {

    	$retour = '<span style="white-space: nowrap;">';
    	$retour .= '<b>'.$this->__('Min : %s', (int)$row->getNeededQtyForValidOrders()).'</b>';
    	$retour .= '<br>'.$this->__('Max : %s', (int)$row->getNeededQty());
    	$retour .= '</span>';

        return $retour;
    }

}