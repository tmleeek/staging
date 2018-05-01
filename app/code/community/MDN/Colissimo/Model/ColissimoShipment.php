<?php

class MDN_Colissimo_Model_ColissimoShipment
{
    private $_client;
    private $_letter = array();
    private $_response;
    private $_config;
    private $_configretour;


    public function __construct()
    {
        $this->_configshipment = Mage::getSingleton('colissimo/ConfigurationShipment');
        $this->_configreturn = Mage::getSingleton('colissimo/ConfigurationReturn');
    }
    public function getResponse()
    {
        return $this->_response;
    }

    protected function prepareClient($type = 'shipment')
    {
        $config = '_config'.$type;
        if($type == 'shipment')
            $wsdl = $this->$config->wsdl;
        elseif($type == 'return')
            $wsdl = $this->$config->wsdl;

        if(!empty($wsdl)){
            try{
                $this->_client = new SoapClient($wsdl);
            }catch(Exception $e){
                Mage::throwException($e->getMessage());
                Mage::log($e->getMessage().' '.$e->getTraceAsString());
            }
        }else{
            Mage::throwException('WSDL for type "'.$type.'" not found.');
        }

    }

    protected function prepareLetter($shipment, $objectType = 'shipment', $package){

        if(!empty($shipment)){
            $config = '_config'.$objectType;
            $this->$config->setShipment($shipment);
            $this->$config->setPackage($package);

            $this->_letter['password'] = $this->$config->password;
            $this->_letter['contractNumber'] = $this->$config->contract_number;
            $this->_letter['service'] = $this->$config->buildServiceCallContext();
            $this->_letter['parcel'] = $this->$config->buildParcel();
            $this->_letter['dest'] = $this->$config->buildShippingAddress(); //envoi tout shipment
            $this->_letter['exp'] = $this->$config->getShipper();

        }
    }

    public function processEnvoi($shipment,$indexPackage)
    {

        $package = Mage::helper('colissimo/Shipment')->getPackages($shipment);

       $this->prepareClient('shipment');

       $this->prepareLetter($shipment, 'shipment', $package[$indexPackage]);

        $resp = null;
        try{
            $resp = $this->_client->getLetterColissimo(array('letter' => $this->_letter));
          
            if (!is_null($resp)) {
                $this->_response = $resp;

                return $this;
            } else {
                Mage::throwException('Error with Colissimo API return');
            }

        Mage::throwException('Error while calling Colissimo API');

        }catch(Exception $e){
            Mage::throwException($e->getMessage());
            Mage::throwException($this->_client->__getLastResponse().' '.$this->_client->_getLastRequest());
            Mage::log($e->getMessage().' '.$e->getTraceAsString());
        }
    }

    public function processRetour($shipment, $indexPackage)
    {
        try{
            $package = Mage::helper('colissimo/Shipment')->getPackages($shipment);

            $this->prepareClient('return');

            $this->prepareLetter($shipment, 'return', $package[$indexPackage]);

            $resp = $this->_client->getLetter(array('letter' => $this->_letter));
            $this->_response = $resp;

            if (!is_null($resp)) {
                $this->_response = $resp;

                return $this;
            } else {
                Mage::throwException('Error with Colissimo API return');
            }

        }catch(Exception $e){
            Mage::throwException($e->getMessage());
            Mage::throwException($this->_client->__getLastResponse().' '.$this->_client->_getLastRequest());
            Mage::log($e->getMessage().' '.$e->getTraceAsString());
        }
    }
}
