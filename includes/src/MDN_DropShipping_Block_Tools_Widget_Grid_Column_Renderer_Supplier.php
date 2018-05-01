<?php

class MDN_DropShipping_Block_Tools_Widget_Grid_Column_Renderer_Supplier
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    /**
     * get the name of the supplier and give a link to wiew it
     * 
     * @param Varien_Object $row
     * @return type 
     */
    public function render(Varien_Object $row)
    {
    	
        $html = '';
        
        $supplier = Mage::getModel("Purchase/Supplier")->load( $row->getdsposl_supplier_id() );

        $url = Mage::Helper('adminhtml')->getUrl('Purchase/Suppliers/Edit', array('sup_id' => $row->getdsposl_supplier_id() ));
        
        $html = '<b><a href="'.$url.'" title="supplier" name="supplier_'.$row->getdsposl_supplier_id().'" id="supplier_'.$row->getdsposl_supplier_id().'">'.$supplier->getsup_name().'</a></b>';
        
        return $html;
        
    }
    
}