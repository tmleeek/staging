<?php
/**
 * LaPoste_ExpeditorINet
 * 
 * @category    LaPoste
 * @package     LaPoste_ExpeditorINet
 * @copyright   Copyright (c) 2010 La Poste
 * @author 	    Smile (http://www.smile.fr) & Jibé
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LaPoste_ExpeditorINet_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Export configuration
     */
    public function getConfigurationFileExtension() {
        return Mage::getStoreConfig('expeditorinet/export/file_extension');
    }

    public function getConfigurationFileCharset() {
        return Mage::getStoreConfig('expeditorinet/export/file_charset');
    }

    public function getConfigurationEndOfLineCharacter() {
        return Mage::getStoreConfig('expeditorinet/export/endofline_character');
    }

    public function getConfigurationFieldDelimiter() {
        return Mage::getStoreConfig('expeditorinet/export/field_delimiter');
    }

    public function getConfigurationFieldSeparator() {
        return Mage::getStoreConfig('expeditorinet/export/field_separator');
    }

    public function getCompanyCommercialName() {
        return Mage::getStoreConfig('expeditorinet/export/company_commercial_name');
    }

    /**
     * Import configuration
     */
    public function getConfigurationSendEmail() {
        return Mage::getStoreConfig('expeditorinet/import/send_email');
    }

    public function getConfigurationIncludeComment() {
        return Mage::getStoreConfig('expeditorinet/import/include_comment');
    }

    public function getConfigurationDefaultTrackingTitle() {
        return Mage::getStoreConfig('expeditorinet/import/default_tracking_title');
    }

    public function getConfigurationShippingComment() {
        return Mage::getStoreConfig('expeditorinet/import/shipping_comment');
    }
	
    public function getConfigurationCarrierCode() {
        return Mage::getStoreConfig('expeditorinet/import/carrier_code');
    }

}