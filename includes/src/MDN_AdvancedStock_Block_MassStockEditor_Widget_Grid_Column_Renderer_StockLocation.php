<?php

class MDN_AdvancedStock_Block_MassStockEditor_Widget_Grid_Column_Renderer_StockLocation
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
    	$stockId = $row->getId();        
    	
        $onChange = 'onchange="persistantGrid.logChange(this.name, \''.$row->getshelf_location().'\')"';
    	$retour = '<input type="text" name="shelf_location_'.$stockId.'" id="shelf_location_'.$stockId.'" value="'.$row->getshelf_location().'" size="4" '.$onChange.'>';
        return $retour;
    }
    
}