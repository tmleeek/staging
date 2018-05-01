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


class Mirasvit_Email_Model_Trigger extends Mage_Core_Model_Abstract
{
    protected $_chainCollection = null;

    protected function _construct()
    {
        $this->_init('email/trigger');
    }

    /**
     * Chain Collection sorted by delay
     *
     * @return collection
     */
    public function getChainCollection()
    {
        if ($this->_chainCollection == null) {
            $this->_chainCollection = Mage::getModel('email/trigger_chain')->getCollection()
                ->addFieldToFilter('trigger_id', $this->getId())
                ->setOrder('delay', 'asc');
        }

        return $this->_chainCollection;
    }

    /**
     * List of triggering events
     *
     * @return array
     */
    public function getTriggeringEvents()
    {
        return array($this->getData('event'));
    }

    /**
     * List of cancellation events
     *
     * @return array
     */
    public function getCancellationEvents()
    {
        return array_filter(explode(',', $this->getData('cancellation_event')));
    }

    /**
     * List of all events (triggering + cancellation)
     *
     * @return array
     */
    public function getEvents()
    {
        return array_values(array_unique(array_merge($this->getTriggeringEvents(), $this->getCancellationEvents())));
    }

    public function getRunRule()
    {
        return Mage::getModel('email/rule')
            ->load($this->getRunRuleId())
            ->setType('run');
    }

    public function getStopRule()
    {
        return Mage::getModel('email/rule')
            ->load($this->getStopRuleId())
            ->setType('stop');
    }

    /**
     * Sender email specified for trigger or global
     *
     * @return string
     */
    public function getSenderEmail()
    {
        if ($this->getData('sender_email')) {
            return $this->getData('sender_email');
        }

        return Mage::getStoreConfig('trans_email/ident_general/email');
    }

    /**
     * Sender name specified for trigger or global
     *
     * @return string
     */
    public function getSenderName()
    {
        if ($this->getData('sender_name')) {
            return $this->getData('sender_name');
        }

        return Mage::getStoreConfig('trans_email/ident_general/name');
    }

    /**
     * Collection of events with status "new" for current trigger
     * (unprocesssed events for current trigger)
     *
     * @return collection
     */
    public function getNewEvents()
    {
        $collection = Mage::getModel('email/event')->getCollection()
            ->addFieldToFilter('code', array('in' => $this->getEvents()))
            ->addNewFilter($this->getId(), $this->getStoreIds())
            ->setOrder('main_table.created_at', 'asc');

        return $collection;
    }

    /**
     * Processing all new events
     *
     * @return $this
     */
    public function processNewEvents()
    {
        $collection = $this->getNewEvents();

        foreach ($collection as $event) {
            $this->processNewEvent($event);
            $event->addProcessedTriggerId($this->getId());
        }

        return $this;
    }

    /**
     * Processing one event
     *
     * @param  object  $event
     * @param  boolean $isTest
     *
     * @return $this
     */
    public function processNewEvent($event, $isTest = false)
    {
        if (in_array($event->getCode(), $this->getCancellationEvents())) {
            $this->cancelEvent($event, $isTest);
        }

        if (in_array($event->getCode(), $this->getTriggeringEvents())) {
            $this->triggerEvent($event, $isTest);
        }

        return $this;
    }

    /**
     * Trigger Event!
     * Check run, stop rules
     * Generate mail chain
     *
     * @param  object  $event
     * @param  boolean $isTest
     *
     * @return $this
     */
    public function triggerEvent($event, $isTest = false)
    {
        $uniqKey   = $event->getUniqKey();
        $args      = $event->getArgs();

        $this->prepareArgs($uniqKey, $args);

        if (!$isTest) {
            $objArgs = $args;
            $this->loadArgs($uniqKey, $objArgs);
            $objArgs = new Varien_Object($objArgs);

            $runRule       = $this->getRunRule();
            $runRuleResult = $runRule->validate($objArgs);

            $stopRule       = $this->getStopRule();
            $stopRuleResult = $stopRule->validate($objArgs);

            if (!$runRuleResult || $stopRuleResult) {
                return $this;
            }
        }

        foreach ($this->getChainCollection() as $chain) {
            $scheduledAt = $args['time'] + $chain->getDelay();

            if ($isTest) {
                $scheduledAt = time();
            }

            $queueCollection = Mage::getModel('email/queue')->getCollection()
                ->addFieldToFilter('trigger_id', $this->getId())
                ->addFieldToFilter('chain_id', $chain->getId())
                ->addFieldToFilter('uniq_key', $uniqKey);

            if ($queueCollection->count() != 0) {
                continue;
            }

            $queue = Mage::getModel('email/queue');
            $queue->setTriggerId($this->getId())
                ->setChainId($chain->getId())
                ->setUniqKey($uniqKey)
                ->setSenderEmail($this->getSenderEmail())
                ->setSenderName($this->getSenderName())
                ->setRecipientEmail($args['customer_email'])
                ->setRecipientName($args['customer_name'])
                ->setArgs($args)
                ->setScheduledAt(date(DateTime::ISO8601, $scheduledAt))
                ->save();

            if ($isTest) {
                $queue->setTest(1);
                $queue->send();
            }
        }
    }

    public function cancelEvent($event, $isTest = false)
    {
        $args = $event->getArgs();

        $queueCollection = Mage::getModel('email/queue')->getCollection()
            ->addFieldToFilter('status', array('neq' => Mirasvit_Email_Model_Queue::STATUS_DELIVERED))
            ->addFieldToFilter('trigger_id', $this->getId())
            ->addFieldToFilter('recipient_email', $args['customer_email']);

        foreach ($queueCollection as $queue) {
            $queue->cancel();
        }

        return $this;
    }

    public function prepareArgs($key, &$args)
    {
        if (!isset($args['store_id'])) {
            $args['store_id'] = 1;
        }

        if (!isset($args['time'])) {
            $args['time'] = time();
        }

        $this->loadArgs($key, $args);

        return $this;
    }

    public function loadArgs($uniqKey, &$args)
    {
        if (isset($args['customer_id']) && $args['customer_id']) {
            $args['customer'] = Mage::getModel('customer/customer')->load($args['customer_id']);
        }

        if (isset($args['quote_id']) && $args['quote_id']) {
            $args['quote'] = Mage::getModel('sales/quote')
                ->setSharedStoreIds(array_keys(Mage::app()->getStores()))
                ->load($args['quote_id']);
        }

        if (isset($args['order_id']) && $args['order_id']) {
            $args['order'] = Mage::getModel('sales/order')->load($args['order_id']);
        }

        $args['store']      = Mage::app()->getStore($args['store_id']);
        $args['store_name'] = $args['store']->getFrontendName();

        $uniqCode = md5($uniqKey);

        $args['url_restore_cart'] = $args['store']->getUrl('eml/index/restoreCart', array('code' => $uniqCode));
        $args['url_unsubscribe']  = $args['store']->getUrl('eml/index/unsubscribe', array('code' => $uniqCode));
        $args['url_in_browser']   = $args['store']->getUrl('eml/index/view', array('code' => $uniqCode));
        $args['url_resume']       = strtok($args['store']->getUrl('eml/index/resume', array('code' => $uniqCode)), '?');
        $args['resume_url']       = $args['url_resume'];

        return $this;
    }

    public function generate($timestamp = null)
    {
        if ($timestamp) {
            $queueCollection = Mage::getModel('email/queue')->getCollection()
                ->addFieldToFilter('trigger_id', $this->getId())
                ->addFieldToFilter('created_at', array('gteq' => date(DateTime::ISO8601, $timestamp)))
                ->addFieldToFilter('status', array('neq' => Mirasvit_Email_Model_Queue::STATUS_DELIVERED));
            foreach ($queueCollection as $queue) {
                $queue->delete();
            }
        }

        foreach ($this->getEvents() as $eventCode) {
            $eventModel = Mage::helper('email/event')->getEventModel($eventCode);
            $eventModel->check($eventCode, $timestamp);
        }

        $this->processNewEvents();
    }

    public function sendTest($to = null)
    {
        $storeIds = $this->getStoreIds();
        if ($storeIds[0] == 0) {
            unset($storeIds[0]);
            foreach (Mage::app()->getStores() as $storeId => $data) {
                $storeIds[] = $storeId;
            }
        }

        foreach ($storeIds as $storeId) {
            $args = Mage::helper('email/event')->getRandomEventArgs();
            $args['store_id'] = $storeId;

            if ($to != null) {
                $args['customer_email'] = $to;
            }

            $event = Mage::getModel('email/event')
                ->setArgsSerialized(serialize($args))
                ->setUniqKey('test_'.time());
            ini_set('display_errors',1);
            try {
                $this->triggerEvent($event, true);
            } catch (Exception $e) {
                echo $e;
            }
        }
    }
}