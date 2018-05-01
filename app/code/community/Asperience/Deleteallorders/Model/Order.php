<?php
/**
 * @category   ASPerience
 * @package    Asperience_Deleteallorders
 * @author     ASPerience - www.asperience.fr
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Asperience_Deleteallorders_Model_Order extends Mage_Sales_Model_Order
{
	const XML_PATH_SALES_IS_ACTIVE	= 'sales/delete_order/is_active';
	const XML_PATH_SALES_DELETE_ALL	= 'sales/delete_order/delete_all';
	const XML_PATH_SALES_STATUS  	= 'sales/delete_order/order_status';
     const STATE_CICPAYMENTSTATUS_CUSTOMERCANCEL = 'cicpaymentcustomercancelstatus';
    const STATE_CICPAYMENTSTATUS_PAYMENTREFUSED = 'cicpaymentstatus';
    const STATE_CICPAYMENTSTATUS_HOLD = 'cicpaymenthold';

	public function getDeleteStatusIds()
	{
		return explode(',', Mage::getStoreConfig(self::XML_PATH_SALES_STATUS));
	}
	
    public function hasAvalaibleStatus()
    {
		Mage::log($this->getStatus());
		Mage::log($this->getDeleteStatusIds());
    	return (in_array($this->getStatus(), $this->getDeleteStatusIds()));
    }
    
    
    public function hasNoOrdersRelated()
    {
    	return (!$this->hasInvoices() && !$this->hasShipments() && !$this->hasCreditmemos());
    }
    
    public function canDelete()
    {
    	return (Mage::getStoreConfig(self::XML_PATH_SALES_IS_ACTIVE) && $this->hasAvalaibleStatus() && (Mage::getStoreConfig(self::XML_PATH_SALES_DELETE_ALL) || $this->hasNoOrdersRelated()));
    }
}
