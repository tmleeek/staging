<?php
/**
 * Gls_Unibox extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Gls
 * @package    Gls_Unibox
 * @copyright  Copyright (c) 2013 webvisum GmbH
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Webvisum
 * @package    Gls_Unibox
 */
class Gls_Unibox_Helper_Data extends Mage_Core_Helper_Abstract
{  
    public function html2txt($htmlText) {
    	// Created this function as strip_tags does not work properly as it removes some wanted text
		$search = array('@<script[^>]*?>.*?</script>@si',
               '@<[\/\!]*?[^<>]*?>@si',
               '@<style[^>]*?>.*?</style>@siU', 
               '@<![\s\S]*?--[ \t\n\r]*>@'       
		);
		$text = preg_replace($search, '', htmlspecialchars_decode($htmlText));
		$text = str_replace('&szlig;', 'ß', $text);
		$text = str_replace('&ouml;', 'ö', $text);
		$text = str_replace('&auml;', 'ä', $text);
		$text = str_replace('&uuml;', 'ü', $text);
		$text = str_replace('&Ouml;', 'Ö', $text);
		$text = str_replace('&Auml;', 'Ä', $text);
		$text = str_replace('&Uuml;', 'Ü', $text);
		$text = str_replace('&nbsp;', ' ', $text);
		return $text;
    }

	public function getSaveToDiskEnabled() {
		if ( Mage::getStoreConfig('glsbox/labels/savetodisk', Mage::app()->getStore()->getId() ) ) 
			{ return Mage::getStoreConfig('glsbox/labels/savetodisk', Mage::app()->getStore()->getId() ); }
		else
			{ return false; }	
	}

	public function getAutoInsertTracking() {
		if ( Mage::getStoreConfig('glsbox/labels/autoinserttracking', Mage::app()->getStore()->getId()) != "" ) 
			{ return Mage::getStoreConfig('glsbox/labels/autoinserttracking', Mage::app()->getStore()->getId()); }
		else
			{ return false; }
	}
	
	/**
	 * @param String $source
	 *
	 * @return mixed|string
	 */
	public function getFileDestination() {
		if ( Mage::getStoreConfig('glsbox/labels/storage_folder', Mage::app()->getStore()->getId()) != "" ) 
			{ $folder = Mage::getStoreConfig('glsbox/labels/storage_folder', Mage::app()->getStore()->getId()); }
		else
			{ $folder = 'gls'; }	
		return str_replace(' ','',Mage::getBaseDir('media').'/'.$folder.'/');
	}

	public function getTagValue($returnedtag,$tag) {
		if( stripos($returnedtag ,'\\\\\\\\\\GLS\\\\\\\\\\' ) !== false && stripos($returnedtag ,'/////GLS/////' ) !== false )
			{$returnedtag = str_ireplace ( array('\\\\\\\\\\GLS\\\\\\\\\\','/////GLS/////') ,'', $returnedtag); } else {return false;}
		$returnedtag = explode('|',$returnedtag);
		$glsTags = array();
		foreach ($returnedtag as $item) {
			if (stripos($item,'T') === 0) {$tmp = explode(':',$item,2); $tmp[0] = str_ireplace('T','',$tmp[0]); if($tmp[1] != ''){ $glsTags[$tmp[0]] = $tmp[1] ; }} 
				$tmp = null;
		}
		return $glsTags[$tag];
	}
}