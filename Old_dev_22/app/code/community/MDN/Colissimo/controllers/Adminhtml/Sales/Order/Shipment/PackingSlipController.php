<?php
/**
 * Adminhtml controller for UPS labels
 * @version 1.1.0
 * @package MDN\Colissimo\controllers\Adminhtml
 */

class MDN_Colissimo_Adminhtml_Sales_Order_Shipment_PackingSlipController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Prints and down load a PDF containing UPS label for a shipment
     * @version 1.1.0
     * @param void
     * @return PDF PDF document containing UPS label
     */
    public function printAction()
    {

        /**
         * TODO :
         * insérer l'étiquette PDF
         * avec FPDI
         * Générer le packing slip et le renvoyer
         */

        $shipmentId = $this->getRequest()->getParam('shipment_id');

        $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);

        if ( ($shipmentId = $shipment->getId()) > 0) {

            if (Mage::helper('colissimo/Order')->isEligibleForColissimoShipment($shipment->getOrder())) {

                foreach($shipment->getAllTracks() as $tracknum)
                {
                    $tracknums[]=$tracknum->getNumber();
                }
                try{
                    $pdf = Mage::getModel('colissimo/Pdf_PackingSlip')->prepare($shipment)->getPdf();
                }catch(Exception $e){
                    Mage::getSingleton('adminhtml/session')->AddError($e->getMessage());
                    Mage::helper('colissimo')->redirectReferrer();
                }
                return $this->_prepareDownloadResponse('PackingSlip_' . $shipmentId . '.pdf', $pdf, 'application/pdf');
            }
        }
    }
}