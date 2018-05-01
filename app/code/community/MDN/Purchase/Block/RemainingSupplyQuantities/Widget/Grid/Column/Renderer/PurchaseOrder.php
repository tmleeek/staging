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
* retourne les �l�ments � envoyer pour une commande s�lectionn�e pour la pr�paration de commandes
*/
class MDN_Purchase_Block_RemainingSupplyQuantities_Widget_Grid_Column_Renderer_PurchaseOrder
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
	public function render(Varien_Object $row)
    {
    	$html = '<table border="1" cellspacing="0">';
    	
    	$productId = $row->getpop_product_id();
    	$collection = mage::getResourceModel('Purchase/Order_collection')->getPendingOrdersForProduct($productId);
    	foreach ($collection as $item)
    	{
    		$supplierUrl = $this->getUrl('Purchase/Suppliers/Edit/', array('sup_id' => $item->getsup_id()));
    		$orderUrl = $this->getUrl('Purchase/Orders/Edit/', array('po_num' => $item->getpo_num()));
    		$color = mage::helper('purchase/RemainingSupplyQuantities')->getColorForDate($item->getpo_supply_date());
    		
    		
    		$html .= '<tr>';
    		$html .= '<td><a href="'.$supplierUrl.'">'.$item->getsup_name().'</a></td>';
    		$html .= '<td><a href="'.$orderUrl.'">'.$item->getpo_order_id().'</a></td>';
    		$html .= '<td>'.$item->getpop_supplier_ref().'</td>';
    		$html .= '<td><font color="'.$color.'">'.$item->getpo_supply_date().'</font></td>';

                $suppliedQty = mage::helper('purchase/Product_Packaging')->convertToSalesUnit($productId, $item->getpop_supplied_qty());
                $orderedQty = mage::helper('purchase/Product_Packaging')->convertToSalesUnit($productId, $item->getpop_qty());

    		$html .= '<td nowrap>'.$suppliedQty.' / '.$orderedQty.'</td>';
    		$html .= '</tr>';
    	}
    	
    	$html .= '</table>';

    	return $html;
    }
    
    public function renderExport(Varien_Object $row)
    {
    	$csv = '';
    	
    	$productId = $row->getpop_product_id();
    	$collection = mage::getResourceModel('Purchase/Order_collection')->getPendingOrdersForProduct($productId);
    	foreach ($collection as $item)
    	{
    		$csv .= '('.$item->getsup_name().' - '.$item->getpo_order_id().' - '.$item->getpo_supply_date().' - '.($item->getpop_supplied_qty().'/'.$item->getpop_qty()).') ';
    	}

    	return $csv;
    }
}