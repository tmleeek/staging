<?php
class Extendware_EWMPAction_Block_Override_Mage_Catalog_Product_Grid extends Extendware_EWMPAction_Block_Override_Mage_Catalog_Product_Grid_Bridge {
	public function __construct()
    {
    	if ($this->_isEnabled()) {
    		$this->_massactionBlockName = 'ewmpaction/mage_adminhtml_widget_grid_massaction';
    	}
        parent::__construct();
    }
    
	protected function _prepareMassaction()
	{
		parent::_prepareMassaction();
		if ($this->_isEnabled()) {
			Mage::dispatchEvent('ewmpaction_product_grid_prepare_massaction', array(
				'block' => $this
			));
		}
	}
	
	private function _isEnabled() {
		if ($this instanceof AW_Pquestion2_Block_Adminhtml_Question_Edit_Tab_Sharing_Product_Grid) {
			return false;
		}
		return true;
	}
}