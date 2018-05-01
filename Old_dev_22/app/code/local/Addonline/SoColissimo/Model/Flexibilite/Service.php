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
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Model_Flexibilite_Service
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
     * Renvoie  L'url du webservice
     * @return string
     */
    public function getUrlWsdl()
    {
        if (! $this->_urlWsdl) {
            if (Mage::getStoreConfig('carriers/socolissimo/testws_socolissimo_flexibilite')) {
                $this->_urlWsdl = "https://pfi.telintrans.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl";
            } else {
                $this->_urlWsdl = "https://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl";
            }
        }
        return $this->_urlWsdl;
    }

    /**
     * Réponds si le WS est disponible
     * @return boolean
     */
    public function isAvailable()
    {
    		$timeout = 60; // 0.6;
        if (! $this->_available) {
            try {
                $supervisionUrl = "https://ws.colissimo.fr/supervision-wspudo/supervision.jsp";
                if (Mage::getStoreConfig('carriers/socolissimo/testws_socolissimo_flexibilite')) {
                    $supervisionUrl = "https://pfi.telintrans.fr/supervision-wspudo/supervision.jsp";
                    $timeout = 60; // si on est en test on augmente le timeout (utilisé en local souvent, et en local le pc est plus lent qu'un serveur..)
                }
                $ctx = stream_context_create(array(
                    'http' => array(
                        'timeout' => $timeout
                    )
                )); // Si on n'a pas de réponse en moins d'une demi seconde
                $this->_available = file_get_contents($supervisionUrl, false, $ctx);
            } catch (Exception $e) {
                $this->_available = "[KO]";
            }
        }
        return trim($this->_available) === "[OK]";
    }

    /**
     * appel au WS pour liste des relais
     * @param unknown $adresse
     * @param unknown $zipcode
     * @param unknown $ville
     * @param unknown $country
     * @param unknown $filterRelay
     * @return boolean
     */
    function findRDVPointRetraitAcheminement($adresse, $zipcode, $ville, $country, $filterRelay)
    {
        
        /*
         * On inclu la class Stub générée avec wsdl2phpgenrator : https://github.com/walle/wsdl2phpgenerator/wiki/ExampleUsage ./wsdl2php -et -i http://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl
         */
        // Pour gérer les cas où il y a eu compilation
        if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_PointRetraitServiceWSService.php'))
            include_once 'Addonline_SoColissimo_Model_Flexibilite_Service_PointRetraitServiceWSService.php';
        else
            require_once dirname(__FILE__) . '/Service/PointRetraitServiceWSService.php';
        if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findRDVPointRetraitAcheminement.php'))
            include_once 'Addonline_SoColissimo_Model_Flexibilite_Service_findRDVPointRetraitAcheminement.php';
        
        try {
            $pointRetraitServiceWSService = new PointRetraitServiceWSService(array(
                'trace' => TRUE
            ), $this->getUrlWsdl());
        
            $findRDVPointRetraitAcheminement = new findRDVPointRetraitAcheminement();
            $findRDVPointRetraitAcheminement->accountNumber = Mage::getStoreConfig('carriers/socolissimo/id_socolissimo_flexibilite');
            $findRDVPointRetraitAcheminement->password = Mage::getStoreConfig('carriers/socolissimo/password_socolissimo_flexibilite');
            $findRDVPointRetraitAcheminement->address = $adresse;
            $findRDVPointRetraitAcheminement->zipCode = $zipcode;
            $findRDVPointRetraitAcheminement->city = $ville;
            $findRDVPointRetraitAcheminement->countryCode = $country;
            $findRDVPointRetraitAcheminement->weight = Mage::helper('socolissimo')->getQuoteWeight();
            $findRDVPointRetraitAcheminement->shippingDate = Mage::helper('socolissimo')->getShippingDate();
            $findRDVPointRetraitAcheminement->filterRelay = $filterRelay;
            $date = new Zend_Date();
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $findRDVPointRetraitAcheminement->requestId = Mage::getStoreConfig('carriers/socolissimo/id_socolissimo_flexibilite') . $quote->getCustomerId() . $date->toString('yyyyMMddHHmmss');
            $findRDVPointRetraitAcheminement->lang = (Mage::app()->getStore()->getLanguageCode() == 'NL') ? 'NL' : 'FR';
            $findRDVPointRetraitAcheminement->optionInter = Mage::getStoreConfig('carriers/socolissimo/international');
            
            $result = $pointRetraitServiceWSService->findRDVPointRetraitAcheminement($findRDVPointRetraitAcheminement);
            
            if ($result->return->errorCode != 0) {
                Mage::log($result->return, null, 'socolissimo.log');
            }
            
            // If we have a single result the webservice return us an object and not an array (we convert it here to an array)
            if ($result->return->errorCode == 0 && is_object($result->return->listePointRetraitAcheminement)) {
              $result->return->listePointRetraitAcheminement = array($result->return->listePointRetraitAcheminement);
            }
            return $result->return;
        } catch (SoapFault $fault) {
            /* On va flusher le cache wsdl, car parfois une mise à jour du WS peut nécéssiter un flush de ce cache */
            foreach (glob(sys_get_temp_dir() . DS . 'wsdl*') as $filename) {
                if( strpos(file_get_contents($filename),$this->getUrlWsdl()) !== false) {
                    unlink($filename);
                }
            }            
            if (isset($pointRetraitServiceWSService)) {
                Mage::log('RequestHeaders ' . $pointRetraitServiceWSService->__getLastRequestHeaders(), null, 'socolissimo.log');
                Mage::log('Request ' . $pointRetraitServiceWSService->__getLastRequest(), null, 'socolissimo.log');
                Mage::log('ResponseHeaders ' . $pointRetraitServiceWSService->__getLastResponseHeaders(), null, 'socolissimo.log');
                Mage::log('Response ' . $pointRetraitServiceWSService->__getLastResponse(), null, 'socolissimo.log');
            }
            Mage::log($fault, null, 'socolissimo.log');
            $result = new Varien_Object();
            $result->errorCode= 500;
            $result->errorMessage='Erreur WebService : '.$fault->faultcode.' '.$fault->faultstring;
            return $result;
        }
    }

    /**
     * appel au WS pour un relais
     * @param unknown $id
     * @param unknown $reseau
     * @return unknown|boolean
     */
    function findPointRetraitAcheminementByID($id, $reseau)
    {
        require_once dirname(__FILE__) . '/Service/PointRetraitServiceWSService.php';
        
        try {
            $pointRetraitServiceWSService = new PointRetraitServiceWSService(array(
                'trace' => TRUE
            ), $this->getUrlWsdl());
        
        
            $findPointRetraitAcheminementByID = new findPointRetraitAcheminementByID();
            $findPointRetraitAcheminementByID->accountNumber = Mage::getStoreConfig('carriers/socolissimo/id_socolissimo_flexibilite');
            $findPointRetraitAcheminementByID->password = Mage::getStoreConfig('carriers/socolissimo/password_socolissimo_flexibilite');
            $findPointRetraitAcheminementByID->id = $id;
            $findPointRetraitAcheminementByID->weight = Mage::helper('socolissimo')->getQuoteWeight();
            $findPointRetraitAcheminementByID->date = Mage::helper('socolissimo')->getShippingDate();
            $findPointRetraitAcheminementByID->filterRelay = 1; // pout tous les avoir, même les commerçants
            $findPointRetraitAcheminementByID->reseau = $reseau;
            $findPointRetraitAcheminementByID->langue = (Mage::app()->getStore()->getLanguageCode() == 'NL') ? 'NL' : 'FR';
            
            $result = $pointRetraitServiceWSService->findPointRetraitAcheminementByID($findPointRetraitAcheminementByID);
            
            if ($result->return->errorCode == 0) {
                // Mage::log($result->return->pointRetraitAcheminement, null, 'socolissimo.log');
                $relais = Mage::getModel('socolissimo/flexibilite_relais');
                $relais->setPointRetraitAcheminement($result->return->pointRetraitAcheminement);
                return $relais;
            } else {
                return $result->return->errorMessage;
            }
        } catch (SoapFault $fault) {
            /* On va flusher le cache wsdl, car parfois une mise à jour du WS peut nécéssiter un flush de ce cache */
            foreach (glob(sys_get_temp_dir() . DS . 'wsdl*') as $filename) {
                if( strpos(file_get_contents($filename),$this->getUrlWsdl()) !== false) {
                    unlink($filename);
                }
            }            
            if (isset($pointRetraitServiceWSService)) {
                Mage::log('RequestHeaders ' . $pointRetraitServiceWSService->__getLastRequestHeaders(), null, 'socolissimo.log');
                Mage::log('Request ' . $pointRetraitServiceWSService->__getLastRequest(), null, 'socolissimo.log');
                Mage::log('ResponseHeaders ' . $pointRetraitServiceWSService->__getLastResponseHeaders(), null, 'socolissimo.log');
                Mage::log('Response ' . $pointRetraitServiceWSService->__getLastResponse(), null, 'socolissimo.log');
            }
            Mage::log($fault, null, 'socolissimo.log');
            $result = new Varien_Object();
            $result->errorCode= 500;
            $result->errorMessage='Erreur WebService : '.$fault->faultcode.' '.$fault->faultstring;
            return $result;
        }
    }

/**
 * Codes erreurs WS
 *
 * 0 Code retour OK
 * 101 Numéro de compte absent
 * 102 Mot de passe absent
 * 104 Code postal absent
 * 105 Ville absente
 * 106 Date estimée de l’envoi absente
 * 107 Identifiant point de retrait absent
 * 120 Poids n’est pas un entier
 * 121 Poids n’est pas compris entre 1 et 99999
 * 122 Date n’est pas au format JJ/MM/AAAA
 * 123 Filtre relais n’est pas 0 ou 1
 * 124 Identifiant point de retrait incorrect
 * 125 Code postal incorrect (non compris entre 01XXX et 95XXX ou 980XX)
 * 201 Identifiant / mot de passe invalide
 * 202 Service non autorisé pour cet identifiant
 * 300 Pas de point de retrait suite à l’application des règles métier
 * 1000 Erreur système (erreur technique)
 */
}
