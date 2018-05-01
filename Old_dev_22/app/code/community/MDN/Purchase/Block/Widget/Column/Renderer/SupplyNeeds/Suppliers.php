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
class MDN_Purchase_Block_Widget_Column_Renderer_SupplyNeeds_Suppliers extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {

        $productId = $row->getsn_product_id();


        $suppliers = Mage::getModel('Purchase/ProductSupplier')->getSuppliersForProduct($row);
        $html = '';
        foreach($suppliers as $supplier)
        {
            $price = Mage::helper('core')->currency($supplier->getpps_last_unit_price());
            $html .= $supplier->getsup_name().' : '.$price.' <i>('.(int)$supplier->getpps_quantity_product().')</i><br>';
        }

        return $html;
    }

}