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


class Mirasvit_Email_Model_Event_Customer_Activity extends Mirasvit_Email_Model_Event_Abstract
{
    const EVENT_CODE = 'customer_activity';

    public function getEventsGroup()
    {
        return __('Customer');
    }

    public function getEvents()
    {
        $result = array();

        $result[self::EVENT_CODE] = __('Customer Activity');

        return $result;
    }

    public function findEvents($eventCode, $timestamp)
    {
        $events = array();

        $nowDate  = Mage::getSingleton('core/date')->date(null, time());
        $lastDate = Mage::getSingleton('core/date')->date(null, $timestamp);

        $resource   = Mage::getSingleton('core/resource');
        $collection = Mage::getModel('log/visitor')->getCollection();

        $collection->getSelect()
            ->join(array('c' => $resource->getTableName('log/customer')),
                'main_table.visitor_id = c.visitor_id',
                array(
                    'customer_id',
                    'login_at',
                    'store_id'
                )
            )
            ->where('`last_visit_at` BETWEEN "'.$lastDate.'" AND "'.$nowDate.'" ')
            ->group('c.customer_id');

        foreach ($collection as $customerInfo) {
            $customer = Mage::getModel('customer/customer')->load($customerInfo->getCustomerId());

            $event = array(
                'time'           => strtotime($customerInfo->getLastVisitAt()),
                'customer_email' => $customer->getEmail(),
                'customer_name'  => $customer->getName(),
                'customer_id'    => $customer->getId(),
                'store_id'       => $customerInfo['store_id'],
            );

            $events[] = $event;
        }


        return $events;
    }
}