<?php

/**
 * Addonline
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline (http://www.addonline.fr)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Classe Service d'appel au WS
 *
 * @category Addonline
 * @package Addonline_SoColissimo
 * @copyright Copyright (c) 2014 Addonline
 * @author Addonline (http://www.addonline.fr)
 */

use WSColissimo\WSColiPosteLetterService\ClientBuilder;
use WSColissimo\WSColiPosteLetterService\Request\ValueObject\DestEnv;
use WSColissimo\WSColiPosteLetterService\Request\ValueObject\ExpEnv;
use WSColissimo\WSColiPosteLetterService\Request\ValueObject\Address;
use WSColissimo\WSColiPosteLetterService\Request\ValueObject\Parcel;
use WSColissimo\WSColiPosteLetterService\Request\ValueObject\ServiceCallContext;
use WSColissimo\WSColiPosteLetterService\Request\ValueObject\Letter;
use WSColissimo\WSColiPosteLetterService\Request\ValueObject\LetterSub;

class Addonline_SoColissimo_Model_Flexibilite_Etiquette
{

    /**
     * L'url du webservice
     *
     * @var string
     */
    protected $_urlWsdl;

    /**
     * Flag service disponible
     *
     * @var boolean
     */
    protected $_available;


    /**
     * Renvoie L'url du webservice
     *
     * @return string
     */
    public function getUrlWsdl ()
    {
        if (Mage::getStoreConfig(
                'carriers/socolissimo/testws_socolissimo_flexibilite')) {
            $this->_urlWsdl = "http://pfi.telintrans.fr/sls-ws/SlsServiceWS?wsdl"; //WS de test
        } else {
            $this->_urlWsdl = "https://ws.colissimo.fr/sls-ws/SlsServiceWS?wsdl"; //WS de production
        }

        //@begin : WSDL static. comment for production usage
        //$this->_urlWsdl = "https://ws.colissimo.fr/sls-ws/SlsServiceWS?wsdl"; //WS de production
        //$this->_urlWsdl = "http://pfi.telintrans.fr/sls-ws/SlsServiceWS?wsdl"; //WS de test
        //@end : WSDL static. comment for production usage

        return $this->_urlWsdl;
    }

    /**
     * Réponds si le WS est disponible
     *
     * @return boolean
     */
    public function isAvailable()
    {
        $timeout = 60; // 0.6;
        if (! $this->_available) {
            try {
                $supervisionUrl = "https://ws.colissimo.fr/supervision-wspudo/supervision.jsp";
                if (Mage::getStoreConfig(
                        'carriers/socolissimo/testws_socolissimo_flexibilite')) {
                    $supervisionUrl = "https://pfi.telintrans.fr/supervision-wspudo/supervision.jsp";
                    $timeout = 60;
                }
                $ctx = stream_context_create(
                        array(
                                'http' => array(
                                        'timeout' => $timeout
                                )
                        )); // Si on n'a pas de réponse en moins d'une demi
                            // seconde
                $this->_available = file_get_contents($supervisionUrl, false,
                        $ctx);
            } catch (Exception $e) {
                $this->_available =false;
            }
        }
        return true;
    }

    /**
     * @param Mage_Shipping_Model_Shipment_Request $request
     */
    public function run(Mage_Shipping_Model_Shipment_Request $request)
    {
        $parameters = array();

        //@begin : uncomment for production usage
        $parameters['account']  = Mage::getStoreConfig('carriers/socolissimo/id_socolissimo_flexibilite');
        $parameters['password'] = Mage::getStoreConfig('carriers/socolissimo/password_socolissimo_flexibilite');
        //@end : uncomment for production usage

        //@begin :Account paramter static for testing. comment for production usage
        //$parameters['account']  = '820375';//prod
        //$parameters['password'] = 'ZMYY296amr';//prod
        //$parameters['account']  = '818301';//test
        //$parameters['password'] = 'colis536859';//test
        //@begin : Account paramter static for testing. comment for production usage


        $order = $request->getOrderShipment()->getOrder();

        $generateLabelRequest = new Letter();
        $this->_prepareLetter($generateLabelRequest, $parameters);

        $letter = new LetterSub();
        //SERVICE
        $service = new ServiceCallContext();
        $this->_prepareService($service, $request, $order);
        $letter->setService($service);
        //PARCEL
        $parcel = new Parcel();
        $this->_prepareParcel($parcel, $request, $order);
        $letter->setParcel($parcel);
        //SENDER
        $sender = new  Address();
        $this->_prepareSender($sender, $request);
        $letter->setSender(new ExpEnv($sender));
        //ADDRESS
        $addressee = new Address();
        $this->_prepareAddressee($addressee, $request);
        $letter->setAddressee(new DestEnv($addressee));

        //add letter bloc
        $generateLabelRequest->setLetter($letter);
        //convert object parameter  to XML parameter.
        $xmlRequest = $this->_getXmlRequest($generateLabelRequest);

        if( $this->isAvailable() ) {
            $client = new SoapClient($this->getUrlWsdl(), array('trace' => 1, 'exceptions' => 0) );
            try {
                $args = array(new SoapVar($xmlRequest, XSD_ANYXML));
                //use generateLabel method for generating the label
                $client->__soapCall('generateLabel', $args );
                //get the last response from the client
                $response = $client->__getLastResponse();

                //Get Label PDF if exist
                $binaryPdf = null;
                if( preg_match_all('#%PDF-(.*?)%%EOF#s', $response, $matches, PREG_SET_ORDER, 0)) {
                    if( isset($matches[0][0]) ) {
                        $binaryPdf = $matches[0][0];
                    }
                }

                //extract the response data from the WS response
                if( preg_match_all('#<return>(.*?)</return>#s', $response, $matches, PREG_SET_ORDER, 0 )) {
                    $soapReturn = simplexml_load_string($matches[0][0]);
                    $labelResponse = (array)$soapReturn->labelResponse;
                    //if has error
                    if($soapReturn->messages->type == "ERROR") {
                        return array('error' => true, 'messages' => $soapReturn->messages->messageContent, 'type' => $soapReturn->messages->id);
                    } else {
                        $trackingNumber = $labelResponse['parcelNumber'];
                        if( is_null($binaryPdf) ) {
                            $pdfUrl = $labelResponse['pdfUrl'];
                        } else {
                            $pdfUrl = $binaryPdf;
                        }
                    }
                    //return the trackingNumber and pdfUrl from the response
                    return array( 'trackingNumber' => $trackingNumber, 'pdfUrl' => $pdfUrl);
                }
            } catch (SoapFault $f) {
                echo "<hr>Message :" . $f->getMessage();die;
            }
        }

        return false;
    }

    /**
     * @param stdClass $letter
     * @param array $parameters
     */
    private function _prepareLetter(Letter &$letter, $parameters)
    {
        $letter->setContractNumber($parameters['account']);
        $letter->setPassword($parameters['password']);
    }

    /**
     * @param ServiceCallContext $service
     * @param Mage_Shipping_Model_Shipment_Request $request
     * @param Mage_Sales_Model_Order $order
     */
    private function _prepareService(ServiceCallContext &$service, Mage_Shipping_Model_Shipment_Request $request, Mage_Sales_Model_Order $order)
    {
        $service->setProductCode($order->getSocoProductCode());
        $service->setDepositDate();
        $service->setCommercialName($request->getShipperContactCompanyName());
        $service->setReturnTypeChoice(2); //use 2 for Yes, 3 for No
        $service->setOrderNumber($order->getIncrementId());
    }

    /**
     * @param Parcel $parcel
     * @param Mage_Shipping_Model_Shipment_Request $request
     * @param Mage_Sales_Model_Order $order
     */
    private function _prepareParcel(Parcel &$parcel, Mage_Shipping_Model_Shipment_Request $request, Mage_Sales_Model_Order $order)
    {
        $parcel->weight = $request->getPackageWeight();
        $deliveryModesNeedRegateCode = Mage::helper('socolissimo')->getDeliveryModesNeedRegateCode();
        if(in_array($order->getSocoProductCode(), $deliveryModesNeedRegateCode)) {
            $parcel->pickupLocationId =$order->getSocoRelayPointCode();
        }
    }

    /**
     * @param Address $address
     * @param Mage_Shipping_Model_Shipment_Request $request
     */
    private function _prepareSender(Address &$address, Mage_Shipping_Model_Shipment_Request $request)
    {
        $address->setCompanyName($request->getShipperContactCompanyName());
        $address->setLastName($request->getShipperContactPersonLastName());
        $address->setFirstName($request->getShipperContactFirstName());
        $address->setLine2($request->getShipperAddressStreet());
        $address->setZipCode($request->getShipperAddressPostalCode());
        $address->setCity($request->getShipperAddressCity());
        $address->setCountryCode($request->getShipperAddressCountryCode());
        $address->setMobileNumber($request->getShipperContactPhoneNumber());
        $address->setEmail('noreply@noreply.com');

    }

    /**
     * @param Address $address
     * @param Mage_Shipping_Model_Shipment_Request $request
     */
    private function _prepareAddressee(Address &$address, Mage_Shipping_Model_Shipment_Request $request)
    {
        $address->setCompanyName($request->getRecipientContactCompanyName());
        $address->setLastName($request->getRecipientContactPersonLastName());
        $address->setFirstName($request->getRecipientContactPersonFirstName());

        /*l'API colissimo demande comme paramètre obligatoire pour l'adresse la ligne numero 2.
        Si 2 lignes d'adresse sont définies, on affecte la ligne magento 1 a la ligne colissimo 2 et la ligne magento 2 a la ligne colissimo 1.
        On inverse car la ligne 1 de colissimo est utilisée pour spécifier une residence, un immeuble etc... et la 2 pour l'adresse,
        or il est plus courant de rentrer d'abord son adresse puis sa résidence, immeuble ...
        Mais si seulement une seule est définie (ligne 1 pour magento) on la paramètre sur la ligne 2 de colissimo*/

        $recipientAddressStreet2 = $request->getRecipientAddressStreet2();
        if(isset($recipientAddressStreet2) && $recipientAddressStreet2 != ''){
            if(strlen($recipientAddressStreet2) > 35){
                $splittedStreet2 = str_split($recipientAddressStreet2, 35);
                $address->setLine1($splittedStreet2[0]);
                $address->setLine0($splittedStreet2[1]);
            }else{
                $address->setLine1($recipientAddressStreet2);
            }
        }
        $address->setLine2($request->getRecipientAddressStreet1());
        $address->setZipCode($request->getRecipientAddressPostalCode());
        $address->setCity($request->getRecipientAddressCity());
        $address->setCountryCode($request->getRecipientAddressCountryCode());
        $address->setMobileNumber($request->getRecipientContactPhoneNumber());
        $address->setEmail('noreply@noreply.com');
    }


    /**
     * @param array $input
     * @return string
     */
    private function _parseArrayToXml(array $input)
    {
        $xmlString = '';
        foreach($input as $key => $data) {
            if( is_array($data) ) {
                $data = $this->_parseArrayToXml($data);
            }
            $xmlString .= "<$key>$data</$key>";
        }
        return $xmlString;
    }

    /**
     * @param $data
     * @return array
     */
    private function _objectToArray($data)
    {   $return = array();
        foreach( $data as $key => $value ) {
            if( is_object($value)) {
                $return[$key] = $this->_objectToArray($value);
            } else {
                $return[$key] = $value;
            }
        }
        return $return;
    }

    /**
     * @param Letter $letter
     * @return string
     */
    private  function _getXmlRequest(Letter $letter)
    {
        $letter = $this->_objectToArray($letter);
        $xmlRequest = '<ns1:generateLabel>
                            <generateLabelRequest>
                        ';
        $xmlRequest .= $this->_parseArrayToXml($letter);
        $xmlRequest .= '    </generateLabelRequest>
                        </ns1:generateLabel>
                           ';

        return $xmlRequest;
    }
}
