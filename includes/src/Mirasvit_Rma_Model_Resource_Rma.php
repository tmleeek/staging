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


class Mirasvit_Rma_Model_Resource_Rma extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('rma/rma', 'rma_id');
    }

    protected function loadStoreIds(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('rma/rma_store'))
            ->where('rs_rma_id = ?', $object->getId());
        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['rs_store_id'];
            }
            $object->setData('store_ids', $array);
        }
        return $object;
    }

    protected function saveStoreIds($object)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('rs_rma_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('rma/rma_store'), $condition);
        foreach ((array)$object->getData('store_ids') as $id) {
            $objArray = array(
                'rs_rma_id'  => $object->getId(),
                'rs_store_id' => $id
            );
            $this->_getWriteAdapter()->insert(
                $this->getTable('rma/rma_store'), $objArray);
        }
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassDelete()) {
            $this->loadStoreIds($object);
        }
        return parent::_afterLoad($object);
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
            $object->setCode($this->normalize($object->getCode()));
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());
        return parent::_beforeSave($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassStatus()) {
            $this->saveStoreIds($object);
        }
        return parent::_afterSave($object);
    }

    /************************/

    public function normalize($string)
    {
        $string = Mage::getSingleton('catalog/product_url')->formatUrlKey($string);
        $string = str_replace('-', '_', $string);
        return 'f_'.$string;
    }
}