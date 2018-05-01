<?php
/**
 * Shop By Brands
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitmanufacturers
 * @version      3.3.1
 * @license:     zAuKpf4IoBvEYeo5ue8Cll0eto0di8JUzOnOWiuiAF
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Helper_Image extends Mage_Core_Helper_Abstract
{
    public function getUrl($image)
    {
        return Mage::getBaseUrl('media').'aitmanufacturers' . DS . $image;
    }
    
	public function getIconUrl($image)
    {
        return Mage::getBaseUrl('media').'aitmanufacturers' . DS . 'list' . DS . $image;
    }
    
    public function init($image)
    {
        $this->image = $image;
        $this->path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS;
        $this->_processor = new Varien_Image($this->path.$this->image);
        $this->_processor->keepAspectRatio(true);
        return $this;
    }
    
    public function resize($width, $height = null)
    {
         $this->_processor->resize($width, $height);
         return $this->_processor;
    }
}