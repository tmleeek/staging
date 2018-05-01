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


class Mirasvit_Email_Model_Event_Customer_New extends Mirasvit_Email_Model_Event_Abstract
{
    const EVENT_CODE = 'customer_new';

    public function getEventsGroup()
    {
        return Mage::helper('email')->__('Customer');
    }

    public function getEvents()
    {
        $result = array();

        $result[self::EVENT_CODE] = Mage::helper('email')->__('New customer signup');

        return $result;
    }

    public function _check($eventCode, $from)
    {
        $events = $this->findEvents($eventCode, $from);

        foreach ($events as $event) {
            $key   = array();
            $key[] = $event['email'];


            $this->saveEvent($eventCode, $key, $event);
        }
    }

    public function findEvents($eventCode, $from)
    {
        $events     = array();
        $resource   = Mage::getSingleton('core/resource');
        $collection = Mage::getModel('customer/customer')->getCollection();

        $collection->getSelect()
            ->where('created_at >= ?', date('Y-m-d H:i:s', $from));
        foreach ($collection as $customer) {
            $event = array(
                'time'           => strtotime($customer->getCreatedAt()),
                'customer_email' => $customer->getEmail(),
                'customer_id'    => $customer->getId(),
                'store_id'       => $customer->getStoreId(),
            );

            $events[] = $event;
        }

        return $events;
    }
}