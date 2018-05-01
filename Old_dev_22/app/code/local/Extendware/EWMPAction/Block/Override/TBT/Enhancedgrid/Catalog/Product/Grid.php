<?php
class Extendware_EWMPAction_Block_Override_TBT_Enhancedgrid_Catalog_Product_Grid extends Extendware_EWMPAction_Block_Override_TBT_Enhancedgrid_Catalog_Product_Grid_Bridge {
	protected $_massactionBlockName = 'ewmpaction/mage_adminhtml_widget_grid_massaction';
	
	protected function _prepareMassaction()
	{
		parent::_prepareMassaction();
		Mage::dispatchEvent('ewmpaction_product_grid_prepare_massaction', array(
			'block' => $this
		));
	}
}