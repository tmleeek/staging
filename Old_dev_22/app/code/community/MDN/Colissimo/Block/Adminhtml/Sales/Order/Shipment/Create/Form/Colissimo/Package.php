<?php 
/**
 * Block used to retrieve HTML for a package form
 * @author Arnaud P <arnaud@boostmyshop.com>
 * @version 1.2.0
 * @package MDN\Colissimo\Block\Adminhtml\Sales\Order\Shipment\Create\Form\Colissimo
 */

class MDN_Colissimo_Block_Adminhtml_Sales_Order_Shipment_Create_Form_Colissimo_Package
extends Mage_Core_Block_Template
{
    /**
     * Returns requested package index
     * @author Arnaud P <arnaud@boostmyshop.com>
     * @version 1.2.0
     * @param void
     * @return string Package index 
     */
    public function getPackageIndex()
    {
        return Mage::app()->getFrontController()->getRequest()->getParam('package_number');
    }
}