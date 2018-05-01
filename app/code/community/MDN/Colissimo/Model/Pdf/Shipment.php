<?php
/**
 * Overloads default shipment printing to return Colissimo packing slip pdf
 * @author Arnaud P <arnaud@boostmyshop.com>
 * @version 1.0.0
 * @package MDN\Colissimo\Model\Pdf
 * @todo Refactoring
 */

class MDN_Colissimo_Model_Pdf_Shipment extends Mage_Sales_Model_Order_Pdf_Shipment
{
	/**
	 * Returns PDF object depending on module configuration
	 * @author Arnaud P <arnaud@boostmyshop.com>
	 * @version 1.0.0
	 * @param array Array of shipments objects
	 * @return Zend_Pdf Pdf object
	 */
    public function getPdf($shipments = array())
    {
        if (Mage::getStoreConfig('colissimo/account_shipment/pdf_overloading') == '1') {
            return Mage::getModel('colissimo/Pdf_PackingSlip')
                ->getPdfOverload($shipments);
        }

        return parent::getPdf($shipments);
    }
}