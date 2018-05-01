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

class MDN_SalesOrderPlanning_Block_LateOrders_Widget_Grid_Column_Renderer_Late extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
    	$initialDate = strtotime($row->getanounced_date());
    	$currentDate = strtotime($row->getpsop_delivery_date());
    	
    	$diff = ($currentDate - $initialDate) / (3600 * 24);
    	$color = 'black';
    	if ($diff < 0)
    		$color = 'green';
    	elseif ($diff < 3)
    		$color = 'orange';
    	else 
    		$color = 'red';
    	$html = '<font color="'.$color.'">'.$diff.'</font>';
    	
		return $html;
    }
    
}