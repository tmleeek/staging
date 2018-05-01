<?php 
/**
 * Ajax controller used to build html output for packages form
 * @author Arnaud P <arnaud@boostmyshop.com>
 * @version 1.2.0
 * @package MDN\Colissimo\controllers\Adminhtml\Sales\Order\Shipment
 */

class MDN_Colissimo_Adminhtml_Sales_Order_Shipment_PackageController
extends Mage_Adminhtml_Controller_Action
{
    /**
     * Echoes HTML output to print a package's form
     * @author Arnaud P <arnaud@boostmyshop.com>
     * @version 1.2.0
     * @param void
     * @return void
     */
    public function buildAction()
    {
        if ($packageNumber = $this->getRequest()->getParam('package_number')) {
            header('Content-Type: text/html;');

            $block = $this->getLayout()
                ->createBlock('colissimo/Adminhtml_Sales_Order_Shipment_Create_Form_Colissimo_Package')
                ->setTemplate('colissimo/sales/order/shipment/create/form/colissimo/package.phtml');

            Mage::getSingleton('adminhtml/session')->setnb_package($packageNumber);

            $this->getResponse()->setBody($block->toHtml());
        }
    }

}