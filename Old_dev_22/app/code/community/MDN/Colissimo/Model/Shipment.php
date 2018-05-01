<?php

class MDN_Colissimo_Model_Shipment
{
    private $_shipment = null;

    private $_response = array();
    private $_responseReturn = array();
    private $_configReturn = null;

    public function  __construct(){
        $this->_configReturn = Mage::getSingleton('colissimo/ConfigurationReturn');
    }

    public function getResponse()
    {
        return $this->_response;
    }

    public function getResponseReturn()
    {
        return $this->_responseReturn;
    }

    public function process($shipment)
    {
        if(Mage::helper('colissimo')->supervision()){
        $this->_shipment = $shipment;
        $packages = Mage::helper('colissimo/Shipment')->getPackages($this->_shipment);

            /**
             * Verify packages gabarit before calling api
             */
            if($packages != false){
                foreach($packages as $indexPackage => $value){
                    $response = Mage::getModel('colissimo/ColissimoShipment')->processEnvoi($shipment, $indexPackage)->getResponse();

                    if($response->getLetterColissimoReturn->errorID == 0)
                    {
                        //save pdf etiquette
                        $labelJob = Mage::helper('colissimo/Label')->saveLabel($response->getLetterColissimoReturn,'shipment');

                        if($labelJob === false)
                            Mage::throwException("Error saving Shipment label.");
                        else
                            $this->_response[] = $response;

                        //Return letter
                        if($this->_configReturn->isLinkActive() == true){
                            $responseReturn = Mage::getModel('colissimo/ColissimoShipment')->processRetour($shipment, $indexPackage)->getResponse();

                            if($responseReturn->getLetterReturn->errorID == 0)
                            {
                                //save pdf etiquette
                                $labelJob = Mage::helper('colissimo/Label')->saveLabel($responseReturn->getLetterReturn,'return');

                                if($labelJob === false)
                                    Mage::throwException("Error saving Return label.");
                                else
                                    $this->_responseReturn[] = $responseReturn;

                            }else{
                                Mage::throwException($responseReturn->getLetterReturn->errorID.' - '.$responseReturn->getLetterReturn->error);
                            }
                        }

                    }else{
                        Mage::throwException($response->getLetterColissimoReturn->errorID.' - '.$response->getLetterColissimoReturn->error);
                    }

                }

                return $this;
            }else{
                Mage::throwException('No packages to ship.');
            }

        }else{
            Mage::throwException('Colissimo API status is offline');
        }

    }
}