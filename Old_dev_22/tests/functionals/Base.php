<?php

/**
 * Class Base
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class Base {

    public function __construct(){

        $this->_initBootstrap();

    }

    protected function _initBootstrap(){

        require_once dirname(__FILE__).'/../../app/Mage.php';
        session_start();
        Mage::reset();
        Mage::app('admin');
        ini_set('display_errors', 1);

    }

}