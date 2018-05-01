<?php 
/**
 * Shipment helper for UPS Shipment module
 * @package MDN\Colissimo\Helper
 */

class MDN_Colissimo_Helper_Shipment extends Mage_Core_Helper_Abstract
{
	/**
	 * Retrieves packages for a shipment
	 * @param Mage_Sales_Model_Order_Shipment $shipment Shipment to retrieve packages
	 * @return array Array containing packages for a shipment
	 */
	public function getPackages($shipment)
	{
		$packages = $shipment->getPackages();

		if (Mage::helper('colissimo')->getMagentoVersion() > '1.5')
		{
			return unserialize($packages);
		}

		return $packages;
	}

    public function authorizeRegate($method){
        // define authorized shipping method for regate code
        $authorized = array('A2P', 'MRL', 'CIT', 'BPR', 'ACP', 'CDI', 'CMT', 'BDP');

        if(in_array($method, $authorized))
            return true;
        else
            return false;

    }
}