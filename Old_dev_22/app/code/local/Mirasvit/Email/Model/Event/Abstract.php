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


abstract class Mirasvit_Email_Model_Event_Abstract
{
    public abstract function getEvents();

    /**
     * Return name of event group, like Customer, Cart, Base, Wishlist etc
     *
     * @return string
     */
    public function getEventsGroup()
    {
        return Mage::helper('email')->__('Base');
    }

    public abstract function findEvents($eventCode, $timestamp);

    public function check($eventCode, $timestamp = false)
    {
        $timeVar = 'last_check_'.$eventCode;

        if (!$timestamp) {
            $timestamp = Mage::helper('email')->getVar($timeVar);
            if (!$timestamp) {
                $timestamp = Mage::getSingleton('core/date')->gmtTimestamp();
            }
        }

        $events = $this->findEvents($eventCode, $timestamp);

        foreach ($events as $event) {
            $uniqKey = $this->getEventUniqKey($event);

            $this->saveEvent($eventCode, $uniqKey, $event);
        }

        Mage::helper('email')->setVar($timeVar, Mage::getSingleton('core/date')->gmtTimestamp());

        return true;
    }

    /**
     * default args
     * ! customer_name
     * ! customer_email
     * ! store_id
     * ? customer_id
     * ? customer
     * ? order
     */
    public function saveEvent($code, $uniqKey, $args)
    {
        $event = Mage::getModel('email/event')->getCollection()
            ->addFieldToFilter('uniq_key', $uniqKey)
            ->addFieldToFilter('code', $code)
            ->getFirstItem();

        if ($event->getId()) {
            return $event;
        }

        $event = Mage::getModel('email/event');
        $event->setCode($code)
            ->setUniqKey($uniqKey)
            ->setArgs($args)
            ->setStoreIds(isset($args['store_id']) ? $args['store_id'] : 0);

        if (isset($args['time'])) {
            $event->setCreatedAt($args['time']);
        }

        $event->save();

        return $event;
    }

    public function getEventUniqKey($args)
    {
        $keys = array('customer_email', 'quote_id', 'order_id');
        $result = array();

        foreach ($keys as $key) {
            if (isset($args[$key])) {
                $result[] = $args[$key];
            }
        }

        return implode('_', $result);
    }
}