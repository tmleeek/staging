<?php
/**
 * Overloading 
 * @author Arnaud P <arnaud@boostmyshop.com>
 * @version 1.1.0
 * @package MDN\Colissimo\Block\Adminhtml\Sales\Order\Shipment\View
 */

class MDN_Colissimo_Block_Adminhtml_Sales_Order_Shipment_View_Form
extends Mage_Adminhtml_Block_Sales_Order_Shipment_View_Form
{
	public function getCreateLabelButton()
    {
        return false;
    }
    public function canCreateShippingLabel()
    {
        return true;
    }
}