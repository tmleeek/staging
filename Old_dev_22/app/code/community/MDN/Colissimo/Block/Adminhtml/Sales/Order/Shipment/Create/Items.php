<?php 


class MDN_Colissimo_Block_Adminhtml_Sales_Order_Shipment_Create_Items
extends Mage_Adminhtml_Block_Sales_Order_Shipment_Create_Items
{
	/**
	 * Always returns false to override default magento UPS checkbox
	 * @param void
	 * @return bool Always false to hide Colissimo default checkbox
	 */
    public function canCreateShippingLabel()
    {
        return false;
    }
}