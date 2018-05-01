<?php

class MDN_Colissimo_Model_Observer
{
    protected $_configshipment = null;


    public function __construct()
    {

       $this->_configshipment = Mage::getSingleton('colissimo/ConfigurationShipment');

    }


    public function createColissimoShipment(Varien_Event_Observer $observer)
    {

        if($this->_configshipment->isLinkActive() == true){
            $shipment = $observer->getShipment();
  //          called only if shipment is a new one
            if ($shipment->isObjectNew()) {
                if (null === ($order = $shipment->getOrder())) {
                    throw new Exception('Unable to load order bound to shipment');
                }
                if (Mage::helper('colissimo/order')->isEligibleForColissimoShipment($order)) {
                    //$shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
                    try {

                        $response = Mage::getModel('colissimo/Shipment')->process($shipment)->getResponse();

                        if(empty($response))
                            Mage::throwError('Unknow fail happened.');

                        //$reponseReturn = $obj->getResponseReturn();
                        //Mage::throwException(Mage::helper('adminhtml')->__(var_dump($response)));
                        //$shipment = $observer->getShipment();

                        foreach($response as $key => $value){
                            $track = new Mage_Sales_Model_Order_Shipment_Track();
                            $track->setNumber($value->getLetterColissimoReturn->parcelNumber)
                                //->setCarrierCode('Colissimo')
                                ->setCarrierCode($order->getShippingCarrier()->getCarrierCode())
                                ->setTitle('Colissimo');
                            $shipment->addTrack($track);
                        }

                    }catch(Exception $e){
                        Mage::throwException($e->getMessage());
                        Mage::log('OrderID : '.$shipment->getOrderId().' '.$e->getMessage().' '.$e->getTraceAsString());
                    }
                }

            }else{
                /*Mage::throwException(Mage::helper('adminhtml')->__($shipment->isObjectNew()));*/
            }
        }
    }
}