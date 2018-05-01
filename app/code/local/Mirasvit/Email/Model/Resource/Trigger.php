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


class Mirasvit_Email_Model_Resource_Trigger extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('email/trigger', 'trigger_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }

        if (is_array($object->getData('store_ids'))) {
            $object->setData('store_ids', implode(',', $object->getData('store_ids')));
        }

        if (is_array($object->getData('cancellation_event'))) {
            $object->setData('cancellation_event', implode(',', $object->getData('cancellation_event')));
        }

        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        if ($object->hasData('rule')) {
            $this->_saveRule($object);
        }

        return parent::_beforeSave($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->hasData('chain')) {
            $this->_saveChain($object);
        }
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getStoreIds()) {
            $object->setStoreIds(explode(',', $object->getStoreIds()));
        } else {
            $object->setStoreIds(array(0));
        }

        return parent::_afterLoad($object);
    }

    protected function _saveChain($object)
    {
        $collectionToDelete = Mage::getModel('email/trigger_chain')->getCollection()
            ->addFieldToFilter('trigger_id', $object->getId())
            ->addFieldToFilter('chain_id', array('nin' => array_keys($object->getChain())));

        foreach ($collectionToDelete as $item) {
            $item->delete();
        }

        foreach ($object->getChain() as $chainId => $chainData) {
            if ($chainId === 0) {
                // template (fake) row
                continue;
            }
            $chain = Mage::getModel('email/trigger_chain')->load($chainId);

            if (isset($chainData['days']) && isset($chainData['hours']) && isset($chainData['minutes'])) {
                $chainData['delay'] = $chainData['days'] * 24 * 60 * 60 + $chainData['hours'] * 60 * 60 + $chainData['minutes'] * 60;
            }

            $chain->addData($chainData)
                ->setTriggerId($object->getId())
                ->save();
        }

        return $this;
    }

    protected function _saveRule($object)
    {
        if ($object->getData('rule') && is_array($object->getData('rule'))) {
            $rule       = $object->getData('rule');
            $runRule    = array('conditions' => array());
            $stopRule   = array('conditions' => array());
            $conditions = $rule['conditions'];

            foreach ($conditions as $key => $value) {
                if (substr($key, 0, 3) == 'run') {
                    $key = str_replace('run_', '', $key);
                    $key = str_replace('run', '', $key);
                    $runRule['conditions'][$key] = $value;
                } else {
                    $key = str_replace('stop_', '', $key);
                    $key = str_replace('stop', '', $key);
                    $stopRule['conditions'][$key] = $value;
                }
            }

            $model = $object->getRunRule();
            $model->setIsActive(1)
                ->setIsSystem(1)
                ->loadPost($runRule)
                ->setTitle('Run Rule')
                ->save();
            $object->setRunRuleId($model->getId());

            $model = $object->getStopRule();
            $model->setIsActive(1)
                ->setIsSystem(1)
                ->loadPost($stopRule)
                ->setTitle('Stop Rule')
                ->save();
            $object->setStopRuleId($model->getId());
        }

        return $this;
    }
}