<?php
/**
 * created : 6 oct. 2009
 * 
 * 
 * updated by <user> : <date>
 * Description of the update
 * 
 * @category SQLI
 * @package Sqli_Catalog
 * @author zimzourh
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 *
 * 
 * @package Sqli_Catalog
 */
class Tatva_Catalog_Block_Product_View extends Mage_Catalog_Block_Product_View
{

	public function __construct() {
        $this->addPriceBlockType('simple', 'catalog/product_price_profile1', 'catalog/product/view/price_complete.phtml');
		$this->addPriceBlockType('configurable', 'catalog/product_price_profile1', 'catalog/product/view/price_complete.phtml');
		$this->addPriceBlockType('virtual', 'catalog/product_price_profile1', 'catalog/product/view/price_complete.phtml');
		$this->addPriceBlockType('bundle', 'catalog/product_price_profile1', 'catalog/product/view/price_complete.phtml');
        parent::__construct();
	}
    protected function _toHtml()
    {


		$html = parent::_toHtml();
        $reg = '/<div class=\"brandlogodisplay\">(.*?)<\/div>/ism';

        if (preg_match($reg, $html))
        {

		      $marque = $this->getProduct()->getManufacturer();
			$collection = $this->getProduct()->getGammeCollectionNew();
                       
            $manufacturerLink = Mage::helper('aitmanufacturers')->getNewanufacturerLink($marque,$collection);

            if ($manufacturerLink)
            {
                $reg = '/<div class=\"brandlogodisplay\">(.*?)<\/div>/ism';
                $replace = '<div class="brandlogodisplay">${1}</div><h4>'.$manufacturerLink.'</h4>';
                $html = preg_replace($reg, $replace, $html);
            }
        }

        return $html;


    }

}
