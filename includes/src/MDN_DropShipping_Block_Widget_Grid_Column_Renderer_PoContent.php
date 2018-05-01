<?php

class MDN_DropShipping_Block_Widget_Grid_Column_Renderer_PoContent extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $po) {
        
        $html = '';
        $html .= '<table border="0" width="100%" id="table_edit_po_'.$po->getId().'">';
        $html .= '<tr><th><input type="hidden" name="po_num" value="'.$po->getId().'"></th><th></th></tr>';
        
        $name = 'po_supplier_order_ref';
        $html .= '<tr><td>Supplier PO #</td><td><input type="text" name="'.$name.'" id="'.$name.'" value=""></td></tr>';
        
        $name = 'shipping';
        $html .= '<tr><td>Shipping</td><td><input type="text" name="'.$name.'" id="'.$name.'" value="'.$po->getpo_shipping_cost().'"></td></tr>';
        
        foreach($po->GetProducts() as $poProduct)
        {
            $name = 'products['.$poProduct->getId().'][price]';
            $html .= '<tr><td>'.$poProduct->getpop_product_name().'</td><td><input type="text" id="'.$name.'" name="'.$name.'" value="'.$poProduct->getpop_price_ht().'"></td></tr>';
        }
        $html .= '</table>';

        
        return $html;
    }

}