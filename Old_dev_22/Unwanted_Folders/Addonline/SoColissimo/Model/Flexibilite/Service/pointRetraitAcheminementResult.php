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

if (! class_exists("pointRetraitAcheminementResult", false)) {

/**
 * pointRetraitAcheminementResult
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
    class pointRetraitAcheminementResult
    {

        /**
         *
         * @var int $errorCode
         * @access public
         */
        public $errorCode;

        /**
         *
         * @var string $errorMessage
         * @access public
         */
        public $errorMessage;

        /**
         *
         * @var pointRetraitAcheminement $listePointRetraitAcheminement
         * @access public
         */
        public $listePointRetraitAcheminement;

        /**
         *
         * @var int $qualiteReponse
         * @access public
         */
        public $qualiteReponse;

        /**
         *
         * @var string $wsRequestId
         * @access public
         */
        public $wsRequestId;
    }
}
