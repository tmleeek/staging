<?php

/*
 * 
 */
class MDN_Orderpreparation_Block_Widget_Grid_Column_Renderer_Actions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $mode = $this->getColumn()->getmode();
        $retour = '';

        switch ($mode) {
            case 'selected':
                if (Mage::getSingleton('admin/session')->isAllowed('admin/sales/order/actions/view'))
                    $retour = '<a href="' . $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getorder_id())) . '">' . $this->__('View order') . '</a>';
                if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/order_preparation/prepare_order/mass_actions/add_to_selection'))
                    $retour .= '<br><a href="' . $this->getUrl('OrderPreparation/OrderPreparation/RemoveFromSelection', array('order_id' => $row->getorder_id())) . '">' . $this->__('Remove') . '</a>';
                break;
            case 'fullstock':
                if (Mage::getSingleton('admin/session')->isAllowed('admin/sales/order/actions/view'))
                    $retour = '<a href="' . $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getopp_order_id())) . '">' . $this->__('View order') . '</a>';
                if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/order_preparation/prepare_order/mass_actions/add_to_selection'))
                    $retour .= '<br><a href="' . $this->getUrl('OrderPreparation/OrderPreparation/AddToSelection', array('order_id' => $row->getopp_order_id())) . '">' . $this->__('Select') . '</a>';
                break;
            case 'stockless':
                if (Mage::getSingleton('admin/session')->isAllowed('admin/sales/order/actions/view'))
                    $retour = '<a href="' . $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getopp_order_id())) . '">' . $this->__('View order') . '</a>';
                if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/order_preparation/prepare_order/mass_actions/add_to_selection'))
                    $retour .= '<br><a href="' . $this->getUrl('OrderPreparation/OrderPreparation/AddToSelection', array('order_id' => $row->getopp_order_id())) . '">' . $this->__('Select') . '</a>';
                break;
            case 'ignored':
                if (Mage::getSingleton('admin/session')->isAllowed('admin/sales/order/actions/view'))
                    $retour = '<a href="' . $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getopp_order_id())) . '">' . $this->__('View order') . '</a>';
                if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/order_preparation/prepare_order/mass_actions/add_to_selection'))
                    $retour .= '<br><a href="' . $this->getUrl('OrderPreparation/OrderPreparation/AddToSelection', array('order_id' => $row->getopp_order_id())) . '">' . $this->__('Select') . '</a>';
                break;
        }

        return $retour;
    }

}