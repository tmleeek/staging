<?php 
/**
 * Adds a form to control package creation from new shipment view
 * @author Arnaud P <arnaud@boostmyshop.com>
 * @version 1.1.0
 * @package MDN\Colissimo\Block\Adminhtml\Sales\Order\Shipment\Create\Form
 */

class MDN_Colissimo_Block_Adminhtml_Sales_Order_Shipment_Create_Form_Colissimo
extends Mage_Adminhtml_Block_Sales_Order_Shipment_Create_Form
{
    /**
     * Retruns number of products of an order bound to current shipment
     * @author Arnaud P <arnaud@boostmyshop.com>
     * @version 1.1.0
     * @param void
     * @return int Number of products
     */

	public function getNbTotalProducts()
    {       
        $order = $this->getShipment()->getOrder();

        if ($order->getId() > 0) {
            $items = $order->getItemsCollection();

            return $items->getSize();
        }
    }
}
