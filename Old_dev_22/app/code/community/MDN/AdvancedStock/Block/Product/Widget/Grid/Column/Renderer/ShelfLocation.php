<?php

class MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Renderer_ShelfLocation
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
		$retour = '';		
		
		//init vars
		$value = $row->getshelf_location();				
		
		//textbox
		$textboxName = 'shelf_location_'.$row->getId().'';
		$retour = '<input size="4" type="text" value="'.$value.'" id="'.$textboxName.'" name="'.$textboxName.'"><br>';
		
		return $retour;
    }
    
}