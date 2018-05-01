<?php
/**
 * Gls_Unibox extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Gls
 * @package    Gls_Unibox
 * @copyright  Copyright (c) 2013 webvisum GmbH
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Webvisum
 * @package    Gls_Unibox
 */
class Gls_Unibox_Block_Glsshipping extends Mage_Adminhtml_Block_Template
{

     public function _prepareLayout()
      {
		parent::_prepareLayout();
	  
          $onclick = "submitAndReloadArea($('shipment_gls_info'), '".$this->getSubmitUrl()."');";
          $this->setChild('gls_save_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                  ->setData(array(
                      'label'   => 'GLS Sendung verarbeiten',
                      'class'   => 'save',
                      'onclick' => $onclick
                  ))
          );		  
      }	

    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }	  

	public function getGlsButtonSaveHtml()
	{
		return $this->getChildHtml('gls_save_button');
	}	  
	
    public function getSubmitUrl() {
        return $this->getUrl('*/glsunibox/submitshipment', array() );
    }

    public function getTrackingReloadUrl() {
        return $this->getUrl('*/glsunibox/trackingreload', array() );
    }	
	
    public function getLabelUrl($id,$inc_id) {
        return $this->getUrl('*/glsunibox/printlabel', array(
            'glslabel_id' => $id,
			'paketnummer' => $inc_id
        ));
    }

    public function getLabelDeleteUrl($id) {
        return $this->getUrl('*/glsunibox/deletelabel', array(
            'glslabel_id' => $id
        ));
    }

	public function getFrankaturNeeded($shipmentId){
		$shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
		$countryId = $shipment->getOrder()->getShippingAddress()->getCountryId();
		$zip = $shipment->getOrder()->getShippingAddress()->getPostcode();
		if (
			$countryId == 'CH' ||
			$countryId == 'AD' ||
			$countryId == 'BA' ||
			$countryId == 'GI' ||
			$countryId == 'IS' ||
			$countryId == 'HR' ||
			$countryId == 'LI' ||
			$countryId == 'MK' ||
			$countryId == 'ME' ||
			$countryId == 'NO' ||
			$countryId == 'SM' ||
			$countryId == 'RS' ||
			$countryId == 'TR' ||
			$countryId == 'VA' ||
			$countryId == 'AU' ||
			$countryId == 'BR' ||
			$countryId == 'CN' ||
			$countryId == 'CH' ||
			$countryId == 'HK' ||
			$countryId == 'IN' ||
			$countryId == 'IL' ||
			$countryId == 'JP' ||
			$countryId == 'CA' ||
			$countryId == 'MY' ||
			$countryId == 'RU' ||
			$countryId == 'SG' ||
			$countryId == 'ZA' ||
			$countryId == 'TW' ||
			$countryId == 'US' ||
			$countryId == 'AE' ||
			($countryId == 'FI' && (int)$zip >= 22000 && (int)$zip <= 22999 ) ||
			($countryId == 'GB'	&& ( (strpos($zip,'GY') === 0) || (strpos($zip,'JY') === 0) )	) ||
			($countryId == 'ES'	&& ( ( (int)$zip >= 35000 && (int)$zip <= 35999 ) || ( (int)$zip >= 38000 && (int)$zip <= 38999 ) || ( (int)$zip >= 51000 && (int)$zip <= 51999 ) || ( (int)$zip >= 52000 && (int)$zip <= 52999 ) )	) ||
			($countryId == 'IT' && (int)$zip == 23030	) 
		) { return true;} else { return false; }
	}	
	
	public function getExpressPossible($shipmentId){
		$shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
		$countryId = $shipment->getOrder()->getShippingAddress()->getCountryId();
		if (
			$countryId == 'AT' ||
			$countryId == 'BE' ||
			$countryId == 'NL' ||
			$countryId == 'LU' ||
			$countryId == 'DK' ||
			$countryId == 'HU' ||
			$countryId == 'CZ' ||
			$countryId == 'GB' ||
			$countryId == 'DE'
		) { return true;} else { return false; }
	}
}