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


class Mirasvit_Rma_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getOrderLabel($order, $url = false)
    {
        if (!is_object($order)) {
            $order = Mage::getModel('sales/order')->load($order);
        }
        $res = "#{$order->getRealorderId()}";
        if ($url) {
            $res = "<a href='{$url}' target='_blank'>$res</a>";
        }
        $res .= Mage::helper('rma')->__(" at %s (%s)",
            Mage::helper('core')->formatDate($order->getCreatedAt(), 'medium'),
            strip_tags($order->formatPrice($order->getGrandTotal()))
        );
        return $res;
    }

    public function getOrderItemLabel($item)
    {
        $name = $item->getName();
        if (!$name && is_object($item->getProduct())) { //old versions support
            $name = $item->getProduct()->getName();
        }
        $options = $this->getItemOptions($item);
        if (count($options)) {
            $name .= ' (';
            foreach ($options as $option) {
                $name .= $option['label'].': '.$option['value'].', ';
            }
            $name = substr($name, 0, -2); //remove last ,
            $name .= ')';
        }

        return $name;
    }

    public function getItemOptions($orderItem)
    {
        $result = array();
        if ($options = $orderItem->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }


    public function generateIncrementId($rma)
    {
        $maxLen = 9;
        $id = (string)$rma->getId();
        $storeId = (string)$rma->getStoreId();

        $totalLen = strlen($id) + strlen($storeId);

        return $storeId. str_repeat('0', $maxLen - $totalLen).$id;
    }

    public function parseVariables($text, $rma)
    {
        $objects = array(
            'rma' => $rma,
            'order' => $rma->getOrder(),
            'status' => $rma->getStatus(),
            'customer' => $rma->getCustomer(),
            'store' => $rma->getStore(),
        );
        if (!$itemsBlock = Mage::app()->getLayout()->getBlock('rma_view_items')) {
            $itemsBlock = Mage::app()->getLayout()->createBlock('rma/rma_view_items', 'rma_view_items');
        }
        $itemsBlock->setRma($rma);
        $customer = $rma->getCustomer();
        $additional = array(
            'rma' => array(
                'return_address' => $rma->getReturnAddressHtml(),
                'items' => $itemsBlock->toHtml(),
                'guest_url' => $rma->getGuestUrl(),
                'guest_print_url' => $rma->getGuestPrintUrl()
            ),
            'customer' => array(
                'name' => $customer->getFirstname().' '.$customer->getLastname()
            )
        );

        $text = Mage::helper('mstcore/parsevariables')->parse($text, $objects, $additional, $rma->getStoreId());
        return $text;
    }

    public function convertToHtml($text)
    {
        $html =  nl2br($text);
        return $html;
    }

    public function getNewRmaGuestUrl()
    {
        return Mage::getUrl('rma/guest/new');
    }

    public function getStatusCollection()
    {
        $collection = Mage::getModel('rma/status')->getCollection()
            ->addFieldToFilter('is_active', true);
        return $collection;
    }
}