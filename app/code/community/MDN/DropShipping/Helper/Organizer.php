<?php

class MDN_DropShipping_Helper_Organizer extends Mage_Core_Helper_Abstract {

    /**
     *
     * @param type $order
     * @param type $msg 
     */
    public function addOrganizerToOrder($order, $msg) {
        if (Mage::getSingleton('admin/session')->getUser())
            $userId = Mage::getSingleton('admin/session')->getUser()->getId();
        else
            $userId = 1;
        $Task = Mage::getModel('Organizer/Task')
                ->setot_author_user($userId)
                ->setot_created_at(date('Y-m-d H:i'))
                ->setot_caption($msg)
                ->setot_description('')
                ->setot_entity_type('order')
                ->setot_entity_id($order->getId())
                ->setot_entity_description('Order #' . $order->getincrement_id())
                ->save();
    }

    /**
     *
     * @param type $purchaseOrder
     * @param type $msg 
     */
    public function addOrganizerToPurchaseOrder($purchaseOrder, $msg) {
        if (Mage::getSingleton('admin/session')->getUser())
            $userId = Mage::getSingleton('admin/session')->getUser()->getId();
        else
            $userId = 1;
        $Task = Mage::getModel('Organizer/Task')
                ->setot_author_user($userId)
                ->setot_created_at(date('Y-m-d H:i'))
                ->setot_caption($msg)
                ->setot_description('')
                ->setot_entity_type('purchase_order')
                ->setot_entity_id($purchaseOrder->getId())
                ->setot_entity_description('Purchase order #' . $purchaseOrder->getpo_order_id())
                ->save();
    }

}