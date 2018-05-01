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


class Mirasvit_Email_Model_Observer extends Varien_Object
{
    public function sendQueue($observer)
    {
        $queueCollection = Mage::getModel('email/queue')->getCollection()
            ->addReadyFilter()
            ->setPageSize(10);

        foreach ($queueCollection as $item) {
            $item->send();
        }
    }

    public function checkEvents()
    {
        $events = $this->getActiveEvents();

        foreach ($events as $eventCode) {
            $event = Mage::helper('email/event')->getEventModel($eventCode);
            $event->check($eventCode);
        }

        $triggers = Mage::getModel('email/trigger')->getCollection()
            ->addActiveFilter();

        foreach ($triggers as $trigger) {
            $trigger->processNewEvents();
        }

        return true;
    }

    public function getActiveEvents()
    {
        $events = array();

        $triggers = Mage::getModel('email/trigger')->getCollection()
            ->addActiveFilter();

        foreach ($triggers as $trigger) {
            $events = array_merge($events, $trigger->getEvents());
        }

        $events = array_values(array_unique($events));

        return $events;
    }

    public function onWishlistProductAdd($observer)
    {
        Mage::getModel('email/event_wishlist_wishlist')->observer('wishlist_wishlist_productadded', $observer);
    }

    public function onWishlistShared($observer)
    {
        Mage::getModel('email/event_wishlist_wishlist')->observer('wishlist_wishlist_shared', $observer);
    }
}