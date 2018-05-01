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

if (! class_exists("findInternalRDVPointRetraitAcheminement", false)) {

/**
 * findInternalRDVPointRetraitAcheminement
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
    class findInternalRDVPointRetraitAcheminement
    {

        /**
         *
         * @var string $accountNumber
         * @access public
         */
        public $accountNumber;

        /**
         *
         * @var string $password
         * @access public
         */
        public $password;

        /**
         *
         * @var string $address
         * @access public
         */
        public $address;

        /**
         *
         * @var string $zipCode
         * @access public
         */
        public $zipCode;

        /**
         *
         * @var string $city
         * @access public
         */
        public $city;

        /**
         *
         * @var string $countryCode
         * @access public
         */
        public $countryCode;

        /**
         *
         * @var string $weight
         * @access public
         */
        public $weight;

        /**
         *
         * @var string $shippingDate
         * @access public
         */
        public $shippingDate;

        /**
         *
         * @var string $filterRelay
         * @access public
         */
        public $filterRelay;

        /**
         *
         * @var string $requestId
         * @access public
         */
        public $requestId;

        /**
         *
         * @var string $lang
         * @access public
         */
        public $lang;

        /**
         *
         * @var string $optionInter
         * @access public
         */
        public $optionInter;
    }
}
