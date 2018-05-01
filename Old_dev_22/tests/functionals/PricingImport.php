<?php

require_once dirname(__FILE__).'/../../app/Mage.php';

$_SERVER['SCRIPT_FILENAME'] = '';
session_start();
Mage::reset();
Mage::app('admin');
ini_set('display_errors', 1);

//require_once dirname(__FILE__).'/Base.php';

/**
 * Class PricingImport
 *
 * @package   functionals
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PricingImport extends Base {

    public function run(){

        echo Mage::Helper('Mpm/Pricing')->getCurrency('amazon_fr_default');die();

        $products = array(
            array(
                'sku' => 'hde013',
                'channel' => 'amazon_fr_default',
                'price' => '22',
                'shipping' => '38'
            )
        );

        foreach($products as $product){

            Mage::helper('Mpm/PricingImport')->setPricing($product['sku'], $product['channel'], $product['price'], $product['shipping']);

        }

    }

}

$pricingImport = new PricingImport();
$pricingImport->run();