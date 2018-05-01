<?php

class MDN_DropShipping_Block_Widget_Grid_Column_Renderer_OrderContent extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        
        $divPrefix = $this->getColumn()->getdiv_prefix();
        $retour = '<div id="'.$divPrefix.$row->getId().'">';
        $retour .= '<input type="hidden" name="order_id" value="'.$row->getId().'">';
        $supplierTitle = $this->__("Supplier");
        $dropshipAction = $this->__("Action");
        $websiteId = $row->getStore()->getwebsite_id();

        $dropShipStatusRestriction = $this->getColumn()->getdropship_status_restriction();

        foreach ($row->getItemsCollection() as $item) {

            //manage drop ship status restriction
            if ($dropShipStatusRestriction) {
                if (!in_array($item->getdropship_status(), $dropShipStatusRestriction))
                    continue;
            }

            $remaining_qty = $item->getRemainToShipQty();
            $productId = $item->getproduct_id();
            $name = $item->getName();
            $name .= mage::helper('AdvancedStock/Product_ConfigurableAttributes')->getDescription($productId);

            if ($remaining_qty > 0) {

                $productStockManagement = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
                if ($productStockManagement->getManageStock()) {
                    if (($item->getreserved_qty() >= $remaining_qty) && (!Mage::getStoreConfig('dropshipping/drop_shippable_order/display_orders_with_stock'))) {
                        $retour .= "<font color=\"green\">" . ((int) $remaining_qty) . 'x ' . $name . "</font>";
                    } else {
                        if (($item->getreserved_qty() < $remaining_qty) && ($item->getreserved_qty() > 0) && (!Mage::getStoreConfig('dropshipping/drop_shippable_order/display_orders_with_stock'))) {
                            $retour .= "<font color=\"orange\">" . ((int) $remaining_qty) . 'x ' . $name . " (" . $item->getreserved_qty() . '/' . $remaining_qty . ")</font>";
                        } else {
                            $suppliers = mage::helper('DropShipping')->getDropshipSuppliers($productId, $remaining_qty);
                            if ($suppliers->getSize() > 0) {
                                $retour .= '<div style="border : 1px solid red; text-align: center;">';

                                $productUrl = $this->getUrl('AdvancedStock/Products/Edit', array('product_id' => $productId));
                                $retour .= '<b><a href="' . $productUrl . '">' . ( (int) $remaining_qty) . 'x ' . $name . '</a></b><br>';

                                switch ($item->getdropship_status()) {
                                
                                    case MDN_DropShipping_Helper_Data::STATUS_DROPSHIP_PRICE_REQUEST_SENT:
                                        //add action menu
                                        $name = 'item[' . $item->getId() . ']';
                                        $retour .= '&nbsp;<select name="' . $name . '[mode]" id="' . $name . '[mode]" onchange="toggleSupplierPriceDiv(this, \''.$row->getId().'_'.$item->getId().'\')">';
                                        $retour .= '<option></option>';
                                        $retour .= '<option value="cancel">' . $this->__('Cancel') . '</option>';
                                        $retour .= '<option value="confirm">' . $this->__('Confirm') . '</option>';
                                        $retour .= '</select>';
                                        $retour .= '<div id="div_pricerequest_'.$row->getId().'_'.$item->getId().'" style="display: none">';
                                        $retour .= 'Supplier : <select name="'.$name.'[supplier]">';
                                        foreach ($suppliers as $supplier) {
                                            $caption = $supplier->getsup_name() . ' (' . $supplier->getpps_last_price() . ')';
                                            $retour .= '<option value="' . $supplier->getId() . '">' . $caption . '</option>';
                                        }
                                        $retour .= '</select>';
                                        $retour .= '&nbsp; Price : <input type="text" name="'.$name.'[price]" size="4">';
                                        $retour .= '&nbsp; Shipping : <input type="text" name="'.$name.'[shipping]" size="4">';
                                        $retour .= '</div>';
                                        break;
                                    default:
                                        //add drop ship combo box
                                        $name = 'item[' . $item->getId() . '][supplier]';
                                        $retour .= '' . $supplierTitle . '&nbsp;:&nbsp;<select name="' . $name . '" id="' . $name . '">';
                                        $retour .= '<option></option>';
                                        foreach ($suppliers as $supplier) {
                                            $caption = $supplier->getsup_name() . ' (' . $supplier->getpps_last_price() . ')';
                                            $retour .= '<option value="' . $supplier->getId() . '">' . $caption . '</option>';
                                        }
                                        $retour .= '</select>';

                                        //add drop ship checkbox with default mode selected
                                        $name = 'item[' . $item->getId() . '][mode]';
                                        $defaultMode = Mage::getStoreConfig('dropshipping/drop_shippable_order/default_dropship_mode');
                                        $retour .= '&nbsp;' . $dropshipAction . '&nbsp;:&nbsp;<select name="' . $name . '" id="' . $name . '">';
                                        foreach (Mage::helper('DropShipping')->getDropShipMode() as $value => $label) {
                                            if ($value == Mage::getStoreConfig('dropshipping/drop_shippable_order/default_dropship_mode'))
                                                $retour .= '<option value=' . $value . ' selected="selected">' . $label . '</option>';
                                            else
                                                $retour .= '<option value=' . $value . '>' . $label . '</option>';
                                        }
                                        $retour .= '</select>';

                                        //add comments textarea
                                        if (Mage::getStoreConfig('dropshipping/misc/display_comments_textarea')) {
                                            $name = 'dropshipcomments[' . $row->getId() . '][' . $item->getId() . ']';
                                            $retour .= '<p><textarea name="' . $name . '" id="' . $name . '" cols="70" rows="3"></textarea></p>';
                                        }
                                        break;
                                }
                                    
                                $retour .= '</div>';
                            } else {
                                $availableStock = mage::helper('AdvancedStock/Product_Base')->getAvailableQty($productId, $websiteId);
                                if ($remaining_qty <= $availableStock)
                                    $retour .= '<font color="blue">' . ( (int) $remaining_qty) . 'x ' . $name . '</font>';
                                else
                                    $retour .= "<font color=\"red\">" . ((int) $remaining_qty) . 'x ' . $name . "</font>";
                            }
                        }
                    }
                    $retour .= "<br>";
                }
                else
                    $retour .= "<i>" . $name . "</i><br>";
            }
            else {
                $retour .= "<s>" . ((int) $item->getqty_ordered()) . 'x ' . $name . "</s><br>";
            }
        }

        $retour .= '</div>';
        
        return $retour;
    }

}