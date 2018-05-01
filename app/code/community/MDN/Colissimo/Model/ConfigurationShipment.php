<?php

class MDN_Colissimo_Model_ConfigurationShipment
{
    private $_contract_number = null;
    private $_password = null;
    private $_wsdl = null;
    private $_shipment = null;
    private $_package = null;

    public function __construct()
    {
        $this->_active = Mage::getStoreConfig('colissimo/account_shipment/is_active');
        $this->_contract_number = Mage::getStoreConfig('colissimo/account_shipment/username');
        $this->_password = Mage::getStoreConfig('colissimo/account_shipment/password');
        $this->_wsdl = Mage::getStoreConfig('colissimo/account_shipment/wsdl');
    }

    public function __get($name)
    {
        return $this->{'_' . $name};
    }

    public function setPackage($package){
        $this->_package = $package;
    }

    public function setShipment($shipment){
        $this->_shipment = $shipment;
    }

    public function isLinkActive()
    {
        return $this->_active == 1 ? true : false;
    }

    public function getShipper(){
        //DOC : AddressVO exp
        $ret = array(
            'alert' => 'none',
            'codeBarForreference' => false,
            'addressVO' => array(
                'companyName' => Mage::getStoreConfig('colissimo/shipper/companyname'),
                'Civility' => Mage::getStoreConfig('colissimo/shipper/civility'),
                'Name' => Mage::getStoreConfig('colissimo/shipper/name'),
                'Surname' => Mage::getStoreConfig('colissimo/shipper/surname'),
                'line0' => Mage::getStoreConfig('colissimo/shipper/line0'),    //Etage, couloir, escalier, n° appartement
                'line1' => Mage::getStoreConfig('colissimo/shipper/line1'),    //Entrée, batiment, immeubeuble, residence
                'line2' => Mage::getStoreConfig('colissimo/shipper/line2'),  //Numéro et libellé de voie
                'line3' => Mage::getStoreConfig('colissimo/shipper/line3'),    //Lieu dit ou autre mentien spéciale
                'phone' => Mage::getStoreConfig('colissimo/shipper/phone'),
                'MobileNumber' => Mage::getStoreConfig('colissimo/shipper/mobilephone'),
                //    'DoorCode1' => Mage::getStoreConfig('colissimo/shipper'),
                //    'DoorCode2' => Mage::getStoreConfig('colissimo/shipper'),
                //    'Interphone' => Mage::getStoreConfig('colissimo/shipper'),
                'countryCode' =>trim(Mage::getStoreConfig('colissimo/shipper/country')),
                'city' => Mage::getStoreConfig('colissimo/shipper/city'),
                'email' => trim(Mage::getStoreConfig('colissimo/shipper/email')),
                'postalCode' => trim(Mage::getStoreConfig('colissimo/shipper/postalcode'))
            )
        );

        return $ret;
    }

    public function buildShippingAddress()
    {
        $address = $this->_shipment->getShippingAddress();
        $order = $this->_shipment->getOrder();
        $ret = array(
            'alert' => 'none',
            'codeBarForreference' => false,
            'addressVO' =>  array(
                'companyName' => $address->getcompany(),
                'Name' => $address->getlastname(),
                'Surname' => $address->getfirstname(),
                'line0' => $address->getstreet(3),    //Etage, couloir, escalier, n° appartement
                'line1' => $address->getstreet(2),    //Entrée, batiment, immeubeuble, residence
                'line2' => $address->getstreet(1),  //Numéro et libellé de voie
                'line3' => $address->getstreet(4),    //Lieu dit ou autre mentien spéciale
                //    'DoorCode1' => Mage::getStoreConfig('colissimo/shipper'),
                //    'DoorCode2' => Mage::getStoreConfig('colissimo/shipper'),
                //    'Interphone' => Mage::getStoreConfig('colissimo/shipper'),
                'countryCode' => $address->getcountry_id(),
                'city' => $address->getcity(),
                'email' => Mage::helper('colissimo')->cleanSpaces($order->getcustomer_email()),
                'postalCode' => Mage::helper('colissimo')->cleanSpaces($address->getpostcode())
            )
        );

        //check if phone is mobile or not and add line to $ret
        $ret = Mage::helper('colissimo')->checkPhone($ret, $address->gettelephone());

        return $ret;
    }
    public function buildParcel(){
        //appel buildArticleArray
        //poid total

        if($this->_package['weight'] <= 30 ){
        $ret = array(
            'weight' => $this->_package['weight'],
            'horsGabarit' => $this->_checkGabarit(),//Mage::getStoreConfig('colissimo/config_shipment/mecanisable'),
            'DeliveryMode' => $this->_package['deliverymode'],
            'RecommendationAmount' => (isset($this->_package['recommendationamount']) ? $this->_package['recommendationamount'] : ''),
            'RegateCode' => (isset($this->_package['regatecode']) ? $this->_package['regatecode'] : ''),
            'contents' => array(
                'article' => $this->buildArticlesArray($this->_shipment),
                'categorie' => Mage::getStoreConfig('colissimo/config_shipment/categorie')
            )
        );
        }else{
            Mage::throwException("Package weight can't exceed 30 Kg");
        }
        return $ret;
    }

    protected function _checkGabarit(){

        //        - Les dimensions minimales du colis : 16 cm (Longueur) × 11cm (largeur) x 1 cm (hauteur)
        //        - Les dimensions maximales du colis : L+l+h < ou = 150 cm et avec L < ou = 100 cm
        $packageInfo = $this->_package;
        if($packageInfo['parceltype'] == 1){

            if(isset($packageInfo['length']) && isset($packageInfo['width']) && isset($packageInfo['height'])){
                $totaldim = $packageInfo['length'] + $packageInfo['width'] + $packageInfo['height'];
                if($packageInfo['length'] <= 100 && $packageInfo['width'] >= 11 && $packageInfo['height'] >= 1 && $packageInfo['length'] >= 16 && $totaldim  <= 150){
                    if($packageInfo['length'] > 100 && $packageInfo['width'] > 100 && $packageInfo['height'] > 100)
                        return 1;
                    return 0;

                }else{
                    if($totaldim > 200 || $packageInfo['width'] < 11 || $packageInfo['height'] < 1 || $packageInfo['length'] < 16){
                        Mage::throwException('Package #'.$packageInfo['packageID'].' does not meet Colissimo packages size requirments.');
                    }
                    return 1;
                }
            }else
                return 0;

        }else if($packageInfo['parceltype'] == 2){
            if(isset($packageInfo['length']) && isset($packageInfo['diam'])){
                $totaldim = $packageInfo['length'] + $packageInfo['diam'] * 2;
                if($packageInfo['length'] >= 16 && $packageInfo['diam'] >= 5 && $totaldim <= 150 && $totaldim >= 26){
                    return 1;
                }else{
                    Mage::throwException('Package #'.$packageInfo['packageID'].' does not meet Colissimo packages size requirments.');
                }
            }else
                return 1;
        }

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
        $order = $this->_shipment->getOrder();

        $ret = array(
            'dateDeposite' => $date->format('Y-m-d\TH:i:s\Z'),
            'dateValidation' => $datev->format('Y-m-d\TH:i:s\Z'),
            'returnType' => 'CreatePDFFile',
            'serviceType' => 'SO',
            'commercialName' => 'client_test',
            'crbt' => false,
            'totalAmount' => $order->getShippingAmount() + $order->getShippingTaxAmount()
        );

        return $ret;
    }

}