<?php

class MDN_Colissimo_Model_ConfigurationReturn
{
    private $_contract_number = null;
    private $_password = null;
    private $_wsdl = null;
    private $_shipment = null;
    private $_package = null;

    public function __construct()
    {
        $this->_active = Mage::getStoreConfig('colissimo/account_return/is_active');
        $this->_contract_number = Mage::getStoreConfig('colissimo/account_return/username');
        $this->_password = Mage::getStoreConfig('colissimo/account_return/password');
        $this->_wsdl = Mage::getStoreConfig('colissimo/account_return/wsdl');
    }


    public function __get($name)
    {
        return $this->{'_' . $name};
    }

    public function setShipment($shipment){
        $this->_shipment = $shipment;
    }

    public function setPackage($package){
        $this->_package = $package;
    }

    public function isLinkActive()
    {
        return $this->_active == 1 ? true : false;
    }

    public function buildShippingAddress(){
        //DOC : AddressVO exp
        $ret = array(
            'entity' => array(
                'companyName' => Mage::getStoreConfig('colissimo/shipper/companyname'),
                'Name' => Mage::getStoreConfig('colissimo/shipper/name'),
                'Surname' => Mage::getStoreConfig('colissimo/shipper/surname'),
            ),
            'address' => array(
                'Civility' => Mage::getStoreConfig('colissimo/shipper/civility'),
                'line2' => Mage::getStoreConfig('colissimo/shipper/line0'),    //Etage, couloir, escalier, n° appartement
                'line1' => Mage::getStoreConfig('colissimo/shipper/line1'),    //Entrée, batiment, immeubeuble, residence
                'line0' => Mage::getStoreConfig('colissimo/shipper/line2'),  //Numéro et libellé de voie
                'line3' => Mage::getStoreConfig('colissimo/shipper/line3'),    //Lieu dit ou autre mentien spéciale
                'phone' => Mage::getStoreConfig('colissimo/shipper/phone'),
                'phone' => Mage::getStoreConfig('colissimo/shipper/mobilephone'),
                //    'DoorCode1' => Mage::getStoreConfig('colissimo/shipper'),
                //    'DoorCode2' => Mage::getStoreConfig('colissimo/shipper'),
                //    'Interphone' => Mage::getStoreConfig('colissimo/shipper'),
                'countryCode' => trim(Mage::getStoreConfig('colissimo/shipper/country')),
                'city' => Mage::getStoreConfig('colissimo/shipper/city'),
                'email' => trim(Mage::getStoreConfig('colissimo/shipper/email')),
                'postalCode' => trim(Mage::getStoreConfig('colissimo/shipper/postalcode'))
            ),
            'codeBarForreference' => false
        );

        return $ret;
    }

    public function getShipper()
    {
        $address = $this->_shipment->getShippingAddress();
        $order = $this->_shipment->getOrder();
        $ret = array(
            'entity' => array(
                'companyName' => $address->getcompany(),
                'civility' => 'Mr',
                'Name' => $address->getlastname(),
                'Surname' => $address->getfirstname(),
            ),
            'address' => array(
                'line0' => $address->getstreet(1),    //Etage, couloir, escalier, n° appartement
                'line1' => $address->getstreet(2),    //Entrée, batiment, immeubeuble, residence
                'line2' => $address->getstreet(3),  //Numéro et libellé de voie
//            'line3' => Mage::getStoreConfig('colissimo/shipper/line3'),    //Lieu dit ou autre mentien spéciale
                //    'DoorCode1' => Mage::getStoreConfig('colissimo/shipper'),
                //    'DoorCode2' => Mage::getStoreConfig('colissimo/shipper'),
                //    'Interphone' => Mage::getStoreConfig('colissimo/shipper'),
                'countryCode' => $address->getcountry_id(),
                'city' => $address->getcity(),
                'email' => Mage::helper('colissimo')->cleanSpaces($order->getcustomer_email()),
                'postalCode' => Mage::helper('colissimo')->cleanSpaces($address->getpostcode()),
            )
        );

        //check if phone is mobile or not and add line to $ret
        $ret = Mage::helper('colissimo')->checkPhone($ret, $address->gettelephone());

        return $ret;
    }
    public function buildParcel(){

        $ret = array(
            'weight' => $this->_shipment->getOrder()->getweight(),
            'horsGabarit' => Mage::getStoreConfig('colissimo/config_shipment/mecanisable'),
            'insuranceRange' => Mage::getStoreConfig('colissimo/shipper/')

        );

        return $ret;
    }

    protected function buildArticlesArray()
    {
        $ret = array( );
        $weight = 0;

        //add each article from order to $ret
        foreach ($this->_shipment->getAllItems() as $item) {
            if ($item->getOrderItem()->getParentItemId() == null) {
                $ret[] = array(
                    'description' => $item->getName(),
                    'quantite' => $item->getQty(),
                    'poids' => $item->getWeight(),
                    'valeur' => $item->getPrice()*$item->getQty(),
                    'numTarifaire' => '',   // A definir
                    'paysOrigine' => Mage::getStoreConfig('colissimo/shipper/country')
                );
            }
        }

        return $ret;
    }

    public function buildServiceCallContext()
    {
        $date = new DateTime();
        $datev = new DateTime();
        $datev->modify('+7 day');
        $ret = array(
            'dateDeposite' => $date->format('Y-m-d\TH:i:s'),
            'returnType' => 'CreatePDFFile',
            'serviceType' => '8R',
            'commandNumber' => $this->_shipment->getOrder()->getIncrementID()
        );

        return $ret;
    }

}