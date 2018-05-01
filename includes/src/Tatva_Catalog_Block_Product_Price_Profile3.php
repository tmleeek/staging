<?php
/**
 * created : 30 mars 2010
 * Product price block
 * 
 * updated by <user> : <date>
 * Description of the update
 * 
 * @category SQLI
 * @package Sqli_Catalog
 * @author alay
 * @copyright SQLI - 2010 - http://www.sqli.com
 */


/**
 * Product price block
 *
 * @category   Sqli
 * @package    Sqli_Catalog
 */
class Tatva_Catalog_Block_Product_Price_Profile3 extends Mage_Catalog_Block_Product_Price
{
	/**
	 * Fonctionnement des tags :
	 * 
	 * <begin_whole_price_html_tags>
	 * 		<begin_price_html_tags>PRIX (ex : 123.45 €)</end_price_html_tags>
	 *   	IF (display_tax_mod) THEN
	 *   		<begin_tax_mod_html_tags>MODE (ex : TTC)</end_tax_mod_html_tags>
	 *   	ENDIF
	 * </end_whole_price_html_tags>
	 * IF (produit en promotion) THEN
	 * 		<begin_special_price_html_tags>PRIX HORS PROMOTION</end_special_price_html_tags>
	 * ENDIF
	 * 
	 */
	public function __construct() {
		if($this->getProduct() instanceof Mage_Catalog_Model_Product && $this->getProduct()->getTypeId() == 'configurable') {
			/*$this->setData('begin_whole_price_html_tags', '<div class="contenu-prix-produit-item-liste-66 selected-price"><strong class="texte-blanc">'.$this->__('Selected item').":</strong> ");
			$this->setData('end_whole_price_html_tags', '</div>');*/
			
			$this->setData('begin_price_html_tags', '<div class="contenu-prix-produit-item-liste"><strong id="product-price-'.$this->getProduct()->getId().'">');
			$this->setData('end_price_html_tags', '</strong>');
			
			$this->setData('display_tax_mod', true);
			$this->setData('begin_tax_mod_html_tags', '<span>');
			$this->setData('end_tax_mod_html_tags', '</span></div>');
			
			$this->setData('begin_special_price_html_tags', '<div class="prix-au-lieu-de-80"><span>'.$this->__('instead of').' </span><span class="prix-barre" id="old-price-'.$this->getProduct()->getId().'">');
			$this->setData('end_special_price_html_tags', '</span></div>');
			
			//Displayed item part
			$this->setData('begin_displayed_whole_price_html_tags', '<div class="contenu-prix-produit-item-liste displayed-price">'.$this->__('Displayed item').": ");
			$this->setData('end_displayed_whole_price_html_tags', '</div>');
			
			$this->setData('begin_displayed_price_html_tags', '<span class="no-size-reduction">');
			$this->setData('end_displayed_price_html_tags', '</span>');
			
			$this->setData('display_displayed_tax_mod', true);
			$this->setData('begin_displayed_tax_mod_html_tags', '<span>');
			$this->setData('end_displayed_tax_mod_html_tags', '</span>');
			
			$this->setData('begin_displayed_special_price_html_tags', '<div class="prix-au-lieu-de-80"><span>'.$this->__('instead of').' </span><span class="prix-barre">');
			$this->setData('end_displayed_special_price_html_tags', '</span></div>');
		} else {
			$this->setData('begin_whole_price_html_tags', '<div class="contenu-prix-produit-item-liste">');
			$this->setData('end_whole_price_html_tags', '</div>');
			
			$this->setData('begin_price_html_tags', '<strong>');
			$this->setData('end_price_html_tags', '</strong>');
			
			$this->setData('display_tax_mod', true);
			$this->setData('begin_tax_mod_html_tags', ' <span>');
			$this->setData('end_tax_mod_html_tags', '</span>');
			
			$this->setData('begin_special_price_html_tags', '<div class="prix-au-lieu-de-80"><span>'.$this->__('instead of').' </span><span class="prix-barre">');
			$this->setData('end_special_price_html_tags', '</span></div>');
		}
	}
}
