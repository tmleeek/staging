<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
//$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__FILE__));

if (version_compare(phpversion(), '5.2.0', '<')===true) {
    echo  '<div style="font:12px/1.35em arial, helvetica, sans-serif;">
<div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
<h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">
Whoops, it looks like you have an invalid PHP version.</h3></div><p>Magento supports PHP 5.2.0 or newer.
<a href="http://www.magentocommerce.com/install" target="">Find out</a> how to install</a>
 Magento using PHP-CGI as a work-around.</p></div>';
    exit;
}
//echo 'hell<pre>';print_r($_POST);exit;
/**
 * Error reporting
 */

error_reporting( E_RECOVERABLE_ERROR | E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR );
//error_reporting( E_ALL );

/**
 * Compilation includes configuration file
 */
define('MAGENTO_ROOT', getcwd());




$compilerConfig = MAGENTO_ROOT . '/includes/config.php';
if (file_exists($compilerConfig)) {
    include $compilerConfig;
}

$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
$maintenanceFile = 'maintenance.flag';

if (!file_exists($mageFilename)) {
    if (is_dir('downloader')) {
        header("Location: downloader");
    } else {
        echo $mageFilename." was not found";
    }
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'];
$allowed = array('178.32.251.171','5.39.30.41','195.154.235.91','103.224.241.215');



if (file_exists($maintenanceFile) && !in_array($ip, $allowed)) {
//if (file_exists($maintenanceFile)) {
    include_once dirname(__FILE__) . '/errors/503.php';
    exit;
}

require_once $mageFilename;
Mage::app();
#Varien_Profiler::enable();


/*if (in_array($ip, $allowed))
{
    $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
    echo $currentDate = date('Y-m-d H:i:s', $currentTimestamp);
}*/

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
}

#ini_set('display_errors', 1);

umask(0);

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

//Mage::run($mageRunCode, $mageRunType);

$host = $_SERVER['HTTP_HOST'];
$request_data= $_SERVER["REQUEST_URI"];


$product1 = strpos($request_data,'/fr/catalog/product/view/');

$product3 = strpos($request_data,'/en/catalog/product/view/');


$product4 = strpos($request_data,'.html');

$other_fr = strpos($request_data,'/fr/');

$other_profr = strpos($request_data,'/profr/');

$other_en = strpos($request_data,'/en/');

$other_proen = strpos($request_data,'/proen/');

$ip = $_SERVER['REMOTE_ADDR'];
$allowed = array('178.32.251.171');

if(($product1!== false)|| ($product3!==false))
{
  
	$result= Mage::getModel('advice/advice')->getCatlogIfoldUrlExist($request_data);

	if($result!='')
	{
   		if($product1!==false)
   		{
     		$main_url = 'http://www.az-boutique.fr';
   		}
   		if($product3!==false)
   		{
    		$main_url = 'http://www.az-boutique.com';
   		}
		/*if(in_array($ip, $allowed))
		{
			echo "1";exit;
		}*/


		$final_url  = $main_url.'/'.$result;

  		header( "HTTP/1.1 301 Moved Permanently" );
	  	header("Location: ".$final_url);
		exit;
	}
	else
	{
		/*if(in_array($ip, $allowed))
		{
			echo "2";exit;
		}*/
 		$main_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

  		header( "HTTP/1.1 301 Moved Permanently" );
  		header("Location: ".$main_url);
		exit;
	}

}
elseif(($product1 === false || $product3 === false || $product4 !== false) && ($other_fr !== false || $other_profr !== false || $other_en !== false || $other_proen !== false) && strpos($request_data,"/admin/")===false)
{
	/*if(in_array($ip, $allowed))
		{
			echo "3";exit;
	}*/
	$toReplace = array("/fr/", "/en/", "/profr/", "/proen/");
	$newweburl = str_replace($toReplace, "/", $request_data); //echos 'def'
  	header( "HTTP/1.1 301 Moved Permanently" );
  	header("Location: ".$newweburl);
	exit;
}
else
{
	/*if(in_array($ip, $allowed))
	{
			echo "4";exit;
	}*/
  	switch($host)
	{
                case 'www.az-boutique.com':
			Mage::run('website_az_us', 'website');
		break;
		case 'www.az-boutique.de':
			Mage::run('website_az_de', 'website');
		break;
		case 'www.az-boutique.be':
			Mage::run('website_az_be', 'website');
		break;
		case 'www.az-boutique.co.uk':
			Mage::run('website_az_uk', 'website');
		break;
		case 'www.az-boutique.es':
			Mage::run('website_az_es', 'website');
		break;
		case 'www.az-boutique.it':
			Mage::run('website_az_it', 'website');
		break;
		case 'www.az-boutique.lt':
			Mage::run('website_az_lt', 'website');
		break;
		case 'www.az-boutique.nl':
			Mage::run('website_az_nl', 'website');
		break;
		case 'www.az-boutique.ru':
			Mage::run('website_az_ru', 'website');
		break;
		case 'www.az-boutique.ch':
			Mage::run('website_az_ch', 'website');
		break;
		case 'az-boutique.com':
			Mage::run('website_az_us', 'website');
		break;
		case 'az-boutique.de':
			Mage::run('website_az_de', 'website');
		break;
		case 'az-boutique.be':
			Mage::run('website_az_be', 'website');
		break;
		case 'az-boutique.co.uk':
			Mage::run('website_az_uk', 'website');
		break;
		case 'az-boutique.es':
			Mage::run('website_az_es', 'website');
		break;
		case 'az-boutique.it':
			Mage::run('website_az_it', 'website');
		break;
		case 'az-boutique.lt':
			Mage::run('website_az_lt', 'website');
		break;
		case 'az-boutique.nl':
			Mage::run('website_az_nl', 'website');
		break;
		case 'az-boutique.ru':
			Mage::run('website_az_ru', 'website');
		break;
		case 'az-boutique.ch':
			Mage::run('website_az_ch', 'website');
		break;	

		// Mainstore.com (default store)
		default:
			Mage::run();
		break;
	}
}
