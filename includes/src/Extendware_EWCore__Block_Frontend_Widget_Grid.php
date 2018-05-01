<?php
abstract class Extendware_EWCore_Block_Frontend_Widget_Grid extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Grid
{
	protected $_massactionBlockName = 'ewcore/frontend_widget_grid_massaction';
	
	public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setTemplate('extendware/ewcore/frontend/widget/grid.phtml');
    }
    
    public function getSkinUrl($file=null, array $params=array())
    {
    	if (strpos($file, 'images/') !== null) {
    		$file = 'extendware/ewcore/' . $file;
    	}
    	
    	return parent::getSkinUrl($file, $params);
    }
    
	protected function _toHtml()
    {
    	// do this so adminhtml specific callback is not called in the frontend
        return Mage_Core_Block_Template::_toHtml();
    }
}