<?php

class MDN_DropShipping_Block_Widget_Grid_Column_Renderer_PoActions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $po) {
        
        $popIds = array();
        foreach($po->GetProducts() as $pop)
        {
            $popIds[] = $pop->getId();
        }
        
        $html = '<div id="table_action_po_'.$po->getId().'">';
        $html .= '<button onclick="cancelDropShip('.$po->getId().');" class="scalable delete" type="button"><span>'.$this->__('Cancel').'</span></button><br>';
        
        switch($po->getpo_status())
        {
            case MDN_Purchase_Model_Order::STATUS_WAITING_FOR_SUPPLIER:
                $html .= '<button style="margin-top: 3px;" onclick="confirmDropShipRequest('.$po->getId().', \''.  implode(',', $popIds).'\');" class="scalable save" type="button"><span>'.$this->__('Confirm').'</span></button>';
                break;
            case MDN_Purchase_Model_Order::STATUS_WAITING_FOR_DELIVERY:
                $html .= '<button style="margin-top: 3px;" onclick="confirmDropShipShipping('.$po->getId().', \''.  implode(',', $popIds).'\');" class="scalable save" type="button"><span>'.$this->__('Confirm shipping').'</span></button>';                
                break;
        }
        $html .= '</div>';
        
        return $html;
    }

}