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

    public function restoreTags($input)
    {
        $opened = array();
        // loop through opened and closed tags in order
        if(preg_match_all("/<(\/?[a-z]+)>?/i", $input, $matches))
        {
            foreach($matches[1] as $tag)
            {
                if(preg_match("/^[a-z]+$/i", $tag, $regs))
                {
                    // a tag has been opened
                    if(strtolower($regs[0]) != 'br') $opened[] = $regs[0];
                }
                elseif(preg_match("/^\/([a-z]+)$/i", $tag, $regs))
                {
                    // a tag has been closed
                    unset($opened[array_pop(array_keys($opened, $regs[1]))]);
                }
            }
        }

        // close tags that are still open
        if($opened)
        {
            $tagstoclose = array_reverse($opened);
            foreach($tagstoclose as $tag) $input .= "</$tag>";
        }

        return $input;
    }

    public function strip_single($tags_to_strip,$string)
    {
        foreach ($tags_to_strip as $tag)
        {
            $string = preg_replace("/<\\/?" . $tag . "(.|\\s)*?>/",' ',$string);

        }
        //$string=preg_replace('/<'.$tag.'[^>]*>/i', '', $string);
        //$string=preg_replace('/<\/'.$tag.'>/i', '', $string);
        return $string;
    }
}