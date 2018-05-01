<?php

/**
 * Customer edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class MDN_AdvancedStock_Block_Warehouse_New extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function getSubmitUrl()
	{
		return $this->getUrl('AdvancedStock/Warehouse/Create');
	}
}