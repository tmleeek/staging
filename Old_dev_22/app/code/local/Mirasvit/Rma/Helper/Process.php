<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   RMA
 * @version   1.0.1
 * @revision  135
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Rma_Helper_Process extends Mage_Core_Helper_Abstract
{

    public function getConfig()
    {
        return Mage::getSingleton('rma/config');
    }

    /**
    * save function for backend
    */
    public function createOrUpdateRmaFromPost($data, $items)
    {
        $rma = Mage::getModel('rma/rma');
        if (isset($data['rma_id']) && $data['rma_id']) {
            $rma->load((int)$data['rma_id']);
        } else {
            unset($data['rma_id']);
        }
        if ($data['street2'] != '') {
            $data['street'] .= "\n". $data['street2'];
            unset($data['street2']);
        }

        $order = Mage::getModel('sales/order')->load((int)$data['order_id']);
        $rma->addData($data);
        $rma->setCustomerId($order->getCustomerId());
        $rma->setStoreId($order->getStoreId());
        $rma->save();
        Mage::helper('mstcore/attachment')->saveAttachment('rma_return_label', $rma->getId(), 'return_label');

        foreach ($items as $item) {
            if ((int)$item['qty_requested'] == 0) {
                continue;
            }
            $rmaItem = Mage::getModel('rma/item');
            if (isset($item['item_id']) && $item['item_id']) {
                $rmaItem->load((int)$item['item_id']);
            } else {
                unset($item['item_id']);
            }
            if (!(int)$item['reason_id']) {
                unset($item['reason_id']);
            }
            if (!(int)$item['resolution_id']) {
                unset($item['resolution_id']);
            }
            if (!(int)$item['condition_id']) {
                unset($item['condition_id']);
            }
            $rmaItem->addData($item)
                    ->setRmaId($rma->getId());
// pr($rmaItem->getData());die;
            $orderItem = Mage::getModel('sales/order_item')->load((int)$item['order_item_id']);
            $rmaItem->initFromOrderItem($orderItem);
            $rmaItem->save();
        }

        Mage::helper('rma/process')->notifyRmaStatusChange($rma);
        return $rma;
    }

    /**
    * save function for frontend
    */
    public function createRmaFromPost($data, $items, $customer = false)
    {
        $order = Mage::getModel('sales/order')->load((int)$data['order_id']);
        if ($customer && $order->getCustomerId() != $customer->getId()) {
            throw new Exception("Error Processing Request 1");
        }

        $address = $order->getShippingAddress();

        $rma = Mage::getModel('rma/rma');
        $rma->addData($data)
            ->setStoreId($order->getStoreId())
            ->setEmail($order->getCustomerEmail())
            ->setFirstname($address->getFirstname())
            ->setLastname($address->getLastname())
            ->setCompany($address->getCompany())
            ->setTelephone($address->getTelephone())

            ->setStreet(implode("\n", $address->getStreet()))
            ->setCity($address->getCity())
            ->setCountryId($address->getCountryId())
            ->setRegionId($address->getRegionId())
            ->setRegion($address->getRegion())

            ;
        if ($customer) {
            $rma->setCustomerId($order->getCustomerId());
        }

        $rma->save();

        foreach ($items as $item) {
            if (!(int)$item['order_item_id']) {
                continue;
            }
            $rmaItem = Mage::getModel('rma/item');
            $rmaItem->addData($item)
                    ->setRmaId($rma->getId());

            $orderItem = Mage::getModel('sales/order_item')->load((int)$item['order_item_id']);
            if ($orderItem->getOrderId() != $order->getId()) {
                throw new Exception("Error Processing Request 2. Wrong order item.");
            }
            $rmaItem->initFromOrderItem($orderItem);
            $rmaItem->save();
        }
        Mage::helper('rma/process')->notifyRmaStatusChange($rma);
        if ($data['comment'] != '') {
            $rma->addComment($data['comment'], false, $rma->getCustomer(), false, false, true, false);
        }
        return $rma;
    }

    public function notifyRmaStatusChange($rma)
    {
        if ($rma->getStatusId() == $rma->getOrigData('status_id')) {
            return;
        }
        $status = $rma->getStatus();
        if ($message = $status->getCustomerMessage()) {
            $message = Mage::helper('rma')->parseVariables($message, $rma);
            Mage::helper('rma/mail')->sendNotificationCustomerEmail($rma, $message);
        }

        if ($message = $status->getAdminMessage()) {
            $message = Mage::helper('rma')->parseVariables($message, $rma);
            Mage::helper('rma/mail')->sendNotificationAdminEmail($rma, $message);
        }

        if ($message = $status->getHistoryMessage()) {
            $message = Mage::helper('rma')->parseVariables($message, $rma);
            $isNotified = $status->getCustomerMessage() != '';
            $rma->addComment($message, true, false, false, $isNotified, true);
        }
    }
}