<?php
/**
 * created : 21 aout 2009
 * Alsobought product controller
 * 
 * 
 * @category SQLI
 * @package Sqli_Alsobought
 * @author sgautier
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * 
 * @package Sqli_Alsobought
 */
class Tatva_Alsobought_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function decodeInput($encoded)
    {
        parse_str($encoded, $data);
        foreach($data as $key=>$value) {
            parse_str(base64_decode($value), $data[$key]);
        }

        return $data;
    } 
}