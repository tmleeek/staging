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
class Aitoc_Aitmanufacturers_Block_Rewrite_CatalogProductView extends Tatva_Catalog_Block_Product_View
{
    protected function _toHtml()
    {


       /* $html = parent::_toHtml();
        $reg = '/<div class=\"brandlogodisplay\">(.*?)<\/div>/ism';
		if (preg_match($reg, $html))
        {    $marque='';  $collection='';
              $marque=$this->getProduct()->getManufacturer();
            $manufacturerLink = Mage::helper('aitmanufacturers')->getCustommanufacturerLink($marque,$collection);
            $html=$manufacturerLink;
            if ($manufacturerLink)
            {
                $reg = '/<div class=\"brandlogodisplay\">(.*?)<\/div>/ism';
                $replace = '<div class="brandlogodisplay">${1}</div><h4>'.$manufacturerLink.'</h4>';
                $html = preg_replace($reg, $replace, $html);
            }
        }*/

		$html = parent::_toHtml();
        


        return $html;


    }
}