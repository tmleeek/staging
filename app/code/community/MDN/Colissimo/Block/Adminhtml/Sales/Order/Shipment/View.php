<?php
/**
 * Adminhtml shipment view overload
 * @package MDN\colissimo\Block\Adminhtml\Sales\Order\Shipment
 */
class MDN_Colissimo_Block_Adminhtml_Sales_Order_Shipment_View extends Mage_Adminhtml_Block_Sales_Order_Shipment_View
{

    /**
     * Add a button to print Colissimo label
     * @param void
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $shipment = $this->getShipment();

        if ($shipmentId = $shipment->getId()) {

            if ($order = $shipment->getOrder()) {
                if (Mage::helper('colissimo/Order')->isEligibleForColissimoShipment($order)) {

                    $this->_addButton('print_colissimo_packingslip', array(
                        'label'     => Mage::helper('colissimo')->__('Print Packing Slip '),
                        'class'     => 'save',
                        'onclick'   => 'setLocation(\''.$this->getPrintColissimoPackingSlipUrl($shipmentId).'.pdf\')'
                    ));


                    $this->_addButton('print_ups_label', array(
                        'label'     => Mage::helper('colissimo')->__('Print Colissimo label'),
                        'class'     => 'save',
                        'onclick'   => 'setLocation(\''.$this->getPrintColissimoLabelUrl($shipmentId).'.pdf\')'
                    ));
                }
            }
        }
    }

    /**
     * Retrieves URL to print Colissimo label
     * @param int shipmentId ID of the shipment to print label from
     * @return string URL to print shipment label
     */
    protected function getPrintColissimoLabelUrl($shipmentId)
    {
        return Mage::helper('adminhtml')->getUrl('colissimo/adminhtml_label/print', array('shipment_id' => $shipmentId));
    }

    /**
     * Retrieves URL to print PackingSlip
     * @param int shipmentId ID of the shipment to print label from
     * @return string URL to print shipment label
     */
    protected function getPrintColissimoPackingSlipUrl($shipmentId)
    {
        return Mage::helper('adminhtml')->getUrl('colissimo/adminhtml_Sales_Order_Shipment_PackingSlip/print', array('shipment_id' => $shipmentId));
    }
}