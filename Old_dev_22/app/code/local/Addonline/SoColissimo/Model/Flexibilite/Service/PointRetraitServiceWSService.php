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

if (! class_exists("PointRetraitServiceWSService", false)) {
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_PointRetrait.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_PointRetrait.php';
    else
        require_once ('PointRetrait.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_Conges.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_Conges.php';
    else
        require_once ('Conges.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_pointRetraitAcheminementResult.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_pointRetraitAcheminementResult.php';
    else
        require_once ('pointRetraitAcheminementResult.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_pointRetraitAcheminement.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_pointRetraitAcheminement.php';
    else
        require_once ('pointRetraitAcheminement.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_rdvPointRetraitAcheminementResult.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_rdvPointRetraitAcheminementResult.php';
    else
        require_once ('rdvPointRetraitAcheminementResult.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_pointRetraitAcheminementByIDResult.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_pointRetraitAcheminementByIDResult.php';
    else
        require_once ('pointRetraitAcheminementByIDResult.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_rdvPointRetraitAcheminementByIDResult.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_rdvPointRetraitAcheminementByIDResult.php';
    else
        require_once ('rdvPointRetraitAcheminementByIDResult.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findRDVPointRetraitAcheminement.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findRDVPointRetraitAcheminement.php';
    else
        require_once ('findRDVPointRetraitAcheminement.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findRDVPointRetraitAcheminementResponse.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findRDVPointRetraitAcheminementResponse.php';
    else
        require_once ('findRDVPointRetraitAcheminementResponse.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findInternalPointRetraitAcheminementByID.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findInternalPointRetraitAcheminementByID.php';
    else
        require_once ('findInternalPointRetraitAcheminementByID.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findInternalPointRetraitAcheminementByIDResponse.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findInternalPointRetraitAcheminementByIDResponse.php';
    else
        require_once ('findInternalPointRetraitAcheminementByIDResponse.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findPointRetraitAcheminementByID.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findPointRetraitAcheminementByID.php';
    else
        require_once ('findPointRetraitAcheminementByID.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findPointRetraitAcheminementByIDResponse.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findPointRetraitAcheminementByIDResponse.php';
    else
        require_once ('findPointRetraitAcheminementByIDResponse.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findInternalRDVPointRetraitAcheminement.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findInternalRDVPointRetraitAcheminement.php';
    else
        require_once ('findInternalRDVPointRetraitAcheminement.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findInternalRDVPointRetraitAcheminementResponse.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findInternalRDVPointRetraitAcheminementResponse.php';
    else
        require_once ('findInternalRDVPointRetraitAcheminementResponse.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findInternalRDVPointRetraitAcheminementByID.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findInternalRDVPointRetraitAcheminementByID.php';
    else
        require_once ('findInternalRDVPointRetraitAcheminementByID.php');
    if (file_exists(dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findInternalRDVPointRetraitAcheminementByIDResponse.php'))
        require_once dirname(__FILE__) . '/Addonline_SoColissimo_Model_Flexibilite_Service_findInternalRDVPointRetraitAcheminementByIDResponse.php';
    else
        require_once ('findInternalRDVPointRetraitAcheminementByIDResponse.php');

    /**
     * SO Colissimo (mon Service mes Options) WEB Service Point Retrait [Version 2]
     *
     * @category    Addonline
     * @package     Addonline_SoColissimo
     * @copyright   Copyright (c) 2014 Addonline
     * @author 	    Addonline (http://www.addonline.fr)
     */
    class PointRetraitServiceWSService extends SoapClient
    {

        /**
         *
         * @var array $classmap The defined classes
         * @access private
         */
        private static $classmap = array(
            'PointRetrait' => 'PointRetrait',
            'PointRetrait' => 'PointRetrait',
            'Conges' => 'Conges',
            'pointRetraitAcheminementResult' => 'pointRetraitAcheminementResult',
            'pointRetraitAcheminement' => 'pointRetraitAcheminement',
            'rdvPointRetraitAcheminementResult' => 'rdvPointRetraitAcheminementResult',
            'pointRetraitAcheminementByIDResult' => 'pointRetraitAcheminementByIDResult',
            'rdvPointRetraitAcheminementByIDResult' => 'rdvPointRetraitAcheminementByIDResult',
            'findRDVPointRetraitAcheminement' => 'findRDVPointRetraitAcheminement',
            'findRDVPointRetraitAcheminementResponse' => 'findRDVPointRetraitAcheminementResponse',
            'findInternalPointRetraitAcheminementByID' => 'findInternalPointRetraitAcheminementByID',
            'findInternalPointRetraitAcheminementByIDResponse' => 'findInternalPointRetraitAcheminementByIDResponse',
            'findPointRetraitAcheminementByID' => 'findPointRetraitAcheminementByID',
            'findPointRetraitAcheminementByIDResponse' => 'findPointRetraitAcheminementByIDResponse',
            'findInternalRDVPointRetraitAcheminement' => 'findInternalRDVPointRetraitAcheminement',
            'findInternalRDVPointRetraitAcheminementResponse' => 'findInternalRDVPointRetraitAcheminementResponse',
            'findInternalRDVPointRetraitAcheminementByID' => 'findInternalRDVPointRetraitAcheminementByID',
            'findInternalRDVPointRetraitAcheminementByIDResponse' => 'findInternalRDVPointRetraitAcheminementByIDResponse'
        );

        /**
         * Contructor
         * 
         * @param array $config A array of config values
         * @param string $wsdl The wsdl file to use
         * @access public
         */
        public function __construct(array $options = array(), $wsdl = 'http://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl')
        {
            foreach (self::$classmap as $key => $value) {
                if (! isset($options['classmap'][$key])) {
                    $options['classmap'][$key] = $value;
                }
            }
            
            parent::__construct($wsdl, $options);
        }

        /**
         * findInternalPointRetraitAcheminementByID
         * 
         * @param findInternalPointRetraitAcheminementByID $parameters            
         * @access public
         */
        public function findInternalPointRetraitAcheminementByID(findInternalPointRetraitAcheminementByID $parameters)
        {
            return $this->__soapCall('findInternalPointRetraitAcheminementByID', array(
                $parameters
            ));
        }

        /**
         * findRDVPointRetraitAcheminement
         * 
         * @param findRDVPointRetraitAcheminement $parameters            
         * @access public
         */
        public function findRDVPointRetraitAcheminement(findRDVPointRetraitAcheminement $parameters)
        {
            return $this->__soapCall('findRDVPointRetraitAcheminement', array(
                $parameters
            ));
        }

        /**
         * findInternalRDVPointRetraitAcheminement
         * 
         * @param findInternalRDVPointRetraitAcheminement $parameters            
         * @access public
         */
        public function findInternalRDVPointRetraitAcheminement(findInternalRDVPointRetraitAcheminement $parameters)
        {
            return $this->__soapCall('findInternalRDVPointRetraitAcheminement', array(
                $parameters
            ));
        }

        /**
         * findPointRetraitAcheminementByID
         * 
         * @param findPointRetraitAcheminementByID $parameters            
         * @access public
         */
        public function findPointRetraitAcheminementByID(findPointRetraitAcheminementByID $parameters)
        {
            return $this->__soapCall('findPointRetraitAcheminementByID', array(
                $parameters
            ));
        }

        /**
         * findInternalRDVPointRetraitAcheminementByID
         * 
         * @param findInternalRDVPointRetraitAcheminementByID $parameters            
         * @access public
         */
        public function findInternalRDVPointRetraitAcheminementByID(findInternalRDVPointRetraitAcheminementByID $parameters)
        {
            return $this->__soapCall('findInternalRDVPointRetraitAcheminementByID', array(
                $parameters
            ));
        }
    }
}
