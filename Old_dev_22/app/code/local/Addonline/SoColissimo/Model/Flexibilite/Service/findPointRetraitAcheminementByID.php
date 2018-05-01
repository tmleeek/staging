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

if (! class_exists("findPointRetraitAcheminementByID", false)) {

/**
 * findPointRetraitAcheminementByID
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
    class findPointRetraitAcheminementByID
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
         * @var string $id
         * @access public
         */
        public $id;

        /**
         *
         * @var string $date
         * @access public
         */
        public $date;

        /**
         *
         * @var string $weight
         * @access public
         */
        public $weight;

        /**
         *
         * @var string $filterRelay
         * @access public
         */
        public $filterRelay;

        /**
         *
         * @var string $reseau
         * @access public
         */
        public $reseau;

        /**
         *
         * @var string $langue
         * @access public
         */
        public $langue;
    }
}
