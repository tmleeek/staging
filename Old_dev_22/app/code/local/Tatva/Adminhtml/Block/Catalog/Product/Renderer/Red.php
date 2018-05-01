<?php
class Tatva_Adminhtml_Block_Catalog_Product_Renderer_Red extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	/*public function render(Varien_Object $row)
	{
		static $i=0;
		$i++;
	$value =  $row->getData($this->getColumn()->getIndex());
	
	
	return '<div style="position:relative;"><a href="#" onMouseOver="ShowPicture(\'Style'.$i.'\',1)" onMouseOut="ShowPicture(\'Style'.$i.'\',0)"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/catalog/product'.$value.'" width="75"></a><div id="Style'.$i.'" style="position:absolute;top:0px;left:80px;visibility:hidden;border:solid 1px #CCC;"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/catalog/product'.$value.'" width="200"></div></div>';
	 
	}*/
	
	public function render(Varien_Object $row)
	{
		static $i=0;
		$i++;
	$value =  $row->getData($this->getColumn()->getIndex());
	return '<a href="#" onMouseOver="ShowPicture(\'Style'.$i.'\',1)" onMouseOut="ShowPicture(\'Style'.$i.'\',0)"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/catalog/product'.$value.'" height="75"></a><div id="Style'.$i.'" style="position:absolute;visibility:hidden;border:solid 1px #CCC;"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/catalog/product'.$value.'" height="150"></div>';

	}
}

	
	/*return '<a href="#" onMouseOver="ShowPicture(\'Style'.$i.'\',1)" onMouseOut="ShowPicture(\'Style'.$i.'\',0)"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/catalog/product'.$value.'" width="75"></a><div id="Style'.$i.'" style="position:absolute;visibility:hidden;border:solid 1px #CCC;"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/catalog/product'.$value.'" width="150"></div>';*/
?>
