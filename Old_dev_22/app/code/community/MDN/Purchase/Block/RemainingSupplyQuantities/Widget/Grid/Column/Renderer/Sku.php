<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/*
* retourne les éléments à envoyer pour une commande sélectionnée pour la préparation de commandes
*/
class MDN_Purchase_Block_RemainingSupplyQuantities_Widget_Grid_Column_Renderer_Sku
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
	public function render(Varien_Object $row)
    {
    	$html = '';
    	
    	$productId = $row->getpop_product_id();
    	$url = $this->getUrl('AdvancedStock/Products/Edit', array('product_id' => $productId));
    	
    	$html = '<a href="'.$url.'">'.$row->getsku().'</a>';
    	
    	return $html;
    }
    
    public function renderExport(Varien_Object $row)
    {
    	return $row->getsku();
    }
}