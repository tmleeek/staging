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

if (! class_exists("PointRetrait", false)) {

/**
 * PointRetrait
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
    class PointRetrait
    {

        /**
         *
         * @var boolean $accesPersonneMobiliteReduite
         * @access public
         */
        public $accesPersonneMobiliteReduite;

        /**
         *
         * @var string $adresse1
         * @access public
         */
        public $adresse1;

        /**
         *
         * @var string $adresse2
         * @access public
         */
        public $adresse2;

        /**
         *
         * @var string $adresse3
         * @access public
         */
        public $adresse3;

        /**
         *
         * @var string $codePostal
         * @access public
         */
        public $codePostal;

        /**
         *
         * @var boolean $congesPartiel
         * @access public
         */
        public $congesPartiel;

        /**
         *
         * @var boolean $congesTotal
         * @access public
         */
        public $congesTotal;

        /**
         *
         * @var string $coordGeolocalisationLatitude
         * @access public
         */
        public $coordGeolocalisationLatitude;

        /**
         *
         * @var string $coordGeolocalisationLongitude
         * @access public
         */
        public $coordGeolocalisationLongitude;

        /**
         *
         * @var int $distanceEnMetre
         * @access public
         */
        public $distanceEnMetre;

        /**
         *
         * @var string $horairesOuvertureDimanche
         * @access public
         */
        public $horairesOuvertureDimanche;

        /**
         *
         * @var string $horairesOuvertureJeudi
         * @access public
         */
        public $horairesOuvertureJeudi;

        /**
         *
         * @var string $horairesOuvertureLundi
         * @access public
         */
        public $horairesOuvertureLundi;

        /**
         *
         * @var string $horairesOuvertureMardi
         * @access public
         */
        public $horairesOuvertureMardi;

        /**
         *
         * @var string $horairesOuvertureMercredi
         * @access public
         */
        public $horairesOuvertureMercredi;

        /**
         *
         * @var string $horairesOuvertureSamedi
         * @access public
         */
        public $horairesOuvertureSamedi;

        /**
         *
         * @var string $horairesOuvertureVendredi
         * @access public
         */
        public $horairesOuvertureVendredi;

        /**
         *
         * @var string $identifiant
         * @access public
         */
        public $identifiant;

        /**
         *
         * @var string $indiceDeLocalisation
         * @access public
         */
        public $indiceDeLocalisation;

        /**
         *
         * @var Conges $listeConges
         * @access public
         */
        public $listeConges;

        /**
         *
         * @var string $localite
         * @access public
         */
        public $localite;

        /**
         *
         * @var string $nom
         * @access public
         */
        public $nom;

        /**
         *
         * @var string $periodeActiviteHoraireDeb
         * @access public
         */
        public $periodeActiviteHoraireDeb;

        /**
         *
         * @var string $periodeActiviteHoraireFin
         * @access public
         */
        public $periodeActiviteHoraireFin;

        /**
         *
         * @var int $poidsMaxi
         * @access public
         */
        public $poidsMaxi;

        /**
         *
         * @var string $typeDePoint
         * @access public
         */
        public $typeDePoint;

        /**
         *
         * @var string $codePays
         * @access public
         */
        public $codePays;

        /**
         *
         * @var string $codePays
         * @access public
         */
        public $langue;

        /**
         *
         * @var string $codePays
         * @access public
         */
        public $libellePays;

        /**
         *
         * @var string $codePays
         * @access public
         */
        public $loanOfHandlingTool;

        /**
         *
         * @var string $codePays
         * @access public
         */
        public $parking;

        /**
         *
         * @var string $codePays
         * @access public
         */
        public $reseau;
    }
}
