<?php
/**
 * @category   ASPerience
 * @package    Asperience_Deleteallorders
 * @author     ASPerience - www.asperience.fr
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Asperience_Deleteallorders_Model_Observer
{
	const XML_PATH_SALES_IS_ACTIVE	= 'sales/delete_order/is_active';
	const XML_PATH_SALES_DELETE_ALL	= 'sales/delete_order/delete_all';
	const XML_PATH_SALES_STATUS  	= 'sales/delete_order/order_status';
	
	public function addOptionToSelect($observer)
    {
	    if ($observer->getEvent()->getBlock()->getId() == 'sales_order_grid') {
    		$massBlock = $observer->getEvent()->getBlock()->getMassactionBlock();
	        if ($massBlock) {
	        	if(Mage::getStoreConfig(self::XML_PATH_SALES_IS_ACTIVE) && Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/delete')) {
	        		if(Mage::getStoreConfig(self::XML_PATH_SALES_DELETE_ALL)) {
			            $massBlock->addItem('delete_order', array(
				            'label'=> Mage::helper('deleteallorders')->__('Delete All !'),
						    //'url'  => Mage::helper('adminhtml')->getUrl('deleteallorders'),
							 'url'   => Mage::helper('adminhtml')->getUrl('deleteallorders/index/index', array('_current'=>true)),       
				        	'confirm' => Mage::helper('deleteallorders')->__('Are you sure to delete the selected sales orders? Warning: invoices/shipments/credit memos associated will be also deleted!'),
			            ));
		        	} else {
			            $massBlock->addItem('delete_order', array(
				            'label'=> Mage::helper('deleteallorders')->__('Delete All !'),
						    'url'  => Mage::helper('adminhtml')->getUrl('deleteallorders'),
				        	'confirm' => Mage::helper('deleteallorders')->__('Are you sure to delete the selected sales orders?'),
			            ));
		        	}
	        	}	        	
	        }
		}
    }
}
