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
 * @package   Trigger Email Suite
 * @version   1.0.1
 * @revision  168
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Email_Model_Event_Order_Status extends Mirasvit_Email_Model_Event_Abstract
{
    const EVENT_CODE = 'order_status|';

    public function getEventsGroup()
    {
        return __('Order');
    }

    public function getEvents()
    {
        $result = array();
        $result[self::EVENT_CODE] = __('Order obtained new status');

        $orderStatuses = Mage::getSingleton('sales/order_config')->getStatuses();
        foreach ($orderStatuses as $code => $name) {
            $result[self::EVENT_CODE.$code] = __("Order obtained '%s' status", $name);
        }

        return $result;
    }

    public function findEvents($eventCode, $timestamp)
    {
        $events   = array();
        $fromDate = date('Y-m-d H:i:s', $timestamp);

        $historyCollection = Mage::getModel('sales/order_status_history')->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('created_at', array('gt' => $fromDate))
            ->setOrder('created_at', 'asc');

        foreach ($historyCollection as $history) {
            $code  = self::EVENT_CODE.$history->getStatus();
            $order      = Mage::getModel('sales/order')->load($history->getParentId());

            if ($code == $eventCode || $eventCode == self::EVENT_CODE) {
                $args = array(
                    'time'           => strtotime($history->getCreatedAt()),
                    'customer_email' => $order->getCustomerEmail(),
                    'customer_name'  => $order->getCustomerName(),
                    'customer_id'    => $order->getCustomerId(),
                    'order_id'       => $order->getId(),
                    'store_id'       => $order->getStoreId(),
                );

                $events[] = $args;
            }
        }

        return $events;
    }
}