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

/**
* retourne les elements a envoyer pour une commande selectionnee pour la preparation de commandes
*/
class MDN_Purchase_Block_RemainingSupplyQuantities_Widget_Grid_Column_Renderer_PendingCustomerOrdersQty
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
	public function render(Varien_Object $row)
    {   	
    	$productId = $row->getpop_product_id();
    	$stocks = mage::helper('AdvancedStock/Product_Base')->getStocks($productId);
    	$value = 0;
    	foreach ($stocks as $stock)
    	{
    		$value += $stock->getstock_ordered_qty_for_valid_orders() - $stock->getQty();
    	}
    	if ($value < 0)
    		$value = 0;
    	
    	$value = (int)$value;
    	if ($value == 0)
    		$value = "0";
    		
    	return $value;
    }
}