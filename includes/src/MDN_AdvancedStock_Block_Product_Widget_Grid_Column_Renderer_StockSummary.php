<?php

class MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Renderer_StockSummary extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $retour = '<div style="white-space: nowrap;">';

        //Display stock quantity for a product : Available/Total
        $collection = mage::helper('AdvancedStock/Product_Base')->getStocks($row->getId());
        foreach ($collection as $item) {
            if ($item->ManageStock()) {
                $qty = ((int) $item->getqty());
                $available = ((int) $item->getAvailableQty());
                $color = ($available > 0 ? 'green' : 'red');
                $htmlLine = '<font color="'.$color.'">'.$item->getstock_name() . ' : ' . $available . ' / ' . $qty . '</font><br>';
                $retour .= $htmlLine;                
            }
        }

        //Display qty pending to be delivered by any supplier
        $waiting_for_delivery_qty = $row->getData('waiting_for_delivery_qty');
        if($waiting_for_delivery_qty>0){
            $retour .= Mage::helper('AdvancedStock')->__('Waiting for delivery'). ' : ' .$waiting_for_delivery_qty;
        }

        $retour .= '</div>';

        return $retour;
    }

}