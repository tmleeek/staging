<?php
/**
 * Adminhtml controller for Colissimo labels
 * @version 1.1.0
 * @package MDN\Colissimo\controllers\Adminhtml
 */

class MDN_Colissimo_Adminhtml_LabelController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Prints and down load a PDF containing UPS label for a shipment
     * @version 1.1.0
     * @param void
     * @return PDF PDF document containing UPS label
     */
    public function printAction()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');

        $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);

        if ( ($shipmentId = $shipment->getId()) > 0) {

            if (Mage::helper('colissimo/Order')->isEligibleForColissimoShipment($shipment->getOrder())) {

                try{
                    foreach($shipment->getAllTracks() as $tracknum)
                    {
                        $tracknums[]=$tracknum->getNumber();
                    }

                    //$pdf = file_get_contents(Mage::helper('colissimo/label')->getLabelsDirectory().'/shipment/'.$tracknums[0].'.pdf');
                    $pdf = Mage::getModel('colissimo/Pdf_Label')->prepare($shipment)->getPdf();
                    return $this->_prepareDownloadResponse('colissimo_label_' . $shipmentId . '.pdf', $pdf, 'application/pdf');
                }catch(Exception $e){
                    Mage::getSingleton('adminhtml/session')->AddError('Error creating Label list : '.$e->getMessage());
                    Mage::helper('colissimo')->redirectReferrer();
                }
            }
        }
    }
}