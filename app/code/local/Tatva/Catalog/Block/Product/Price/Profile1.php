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
 * @category   Sqli
 * @package    Sqli_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product price block
 *
 * @category   Sqli
 * @package    Sqli_Catalog
 */
class Tatva_Catalog_Block_Product_Price_Profile1 extends Mage_Catalog_Block_Product_Price
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
			//$this->setData('begin_whole_price_html_tags', '<div class="contenu-prix-produit-item-liste selected-price"><strong class="texte-blanc">'.$this->__('Selected item').":</strong> ");
			//$this->setData('end_whole_price_html_tags', '</div>');
			
			$this->setData('begin_price_html_tags', '<div class="contenu-prix-produit-item-liste"><strong id="product-price-'.$this->getProduct()->getId().'">');
			$this->setData('end_price_html_tags', '</strong>');
			
			$this->setData('display_tax_mod', true);
			$this->setData('begin_tax_mod_html_tags', '<span>');
			$this->setData('end_tax_mod_html_tags', '</span></div>');
			
			$this->setData('begin_special_price_html_tags', '<div class="prix-au-lieu-de"><span>'.$this->__('Instead of').' </span><span class="prix-barre" id="old-price-'.$this->getProduct()->getId().'">');
			$this->setData('end_special_price_html_tags', '</span></div>');
			
			//Displayed item part
			$this->setData('begin_displayed_whole_price_html_tags', '<div class="contenu-prix-produit-item-liste displayed-price">'.$this->__('Displayed item').": ");
			$this->setData('end_displayed_whole_price_html_tags', '</div>');
			
			$this->setData('begin_displayed_price_html_tags', '<span class="no-size-reduction">');
			$this->setData('end_displayed_price_html_tags', '</span>');
			
			$this->setData('display_displayed_tax_mod', true);
			$this->setData('begin_displayed_tax_mod_html_tags', '<span>');
			$this->setData('end_displayed_tax_mod_html_tags', '</span>');
			
			$this->setData('begin_displayed_special_price_html_tags', '<div class="prix-au-lieu-de"><span>'.$this->__('Instead of').' </span><span class="prix-barre">');
			$this->setData('end_displayed_special_price_html_tags', '</span></div>');
		} else {
			$this->setData('begin_whole_price_html_tags', '<div class="contenu-prix-produit-item-liste">');
			$this->setData('end_whole_price_html_tags', '</div>');
			
			$this->setData('begin_price_html_tags', '<strong>');
			$this->setData('end_price_html_tags', '</strong>');
			
			$this->setData('display_tax_mod', true);
			$this->setData('begin_tax_mod_html_tags', ' <span>');
			$this->setData('end_tax_mod_html_tags', '</span>');
			
			$this->setData('begin_special_price_html_tags', '<div class="prix-au-lieu-de"><span>'.$this->__('Instead of').' </span><span class="prix-barre">');
			$this->setData('end_special_price_html_tags', '</span></div>');
		}
	}
}
