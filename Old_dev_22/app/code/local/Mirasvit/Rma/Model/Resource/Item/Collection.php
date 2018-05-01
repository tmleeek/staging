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


class Mirasvit_Rma_Model_Resource_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('rma/item');
    }

    public function toOptionArray($emptyOption = false)
    {
        $arr = $this->_toOptionArray('item_id', 'name');
        if ($emptyOption) {
            array_unshift($arr, array('value' => 0, 'label' => Mage::helper('rma')->__('-- Please Select --')));
        }
        return $arr;
    }

    public function getOptionArray($emptyOption = false)
    {
        $arr = array();
        if ($emptyOption) {
            $arr[0] = Mage::helper('rma')->__('-- Please Select --');
        }
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }
        return $arr;
    }


    protected function initFields()
    {
        $select = $this->getSelect();
        // $select->joinLeft(array('product' => $this->getTable('catalog/product')), 'main_table.product_id = product.entity_id', array('product_name' => 'product.name'));
        $select->joinLeft(array('reason' => $this->getTable('rma/reason')), 'main_table.reason_id = reason.reason_id', array('reason_name' => 'reason.name'));
        $select->joinLeft(array('resolution' => $this->getTable('rma/resolution')), 'main_table.resolution_id = resolution.resolution_id', array('resolution_name' => 'resolution.name'));
        $select->joinLeft(array('condition' => $this->getTable('rma/condition')), 'main_table.condition_id = condition.condition_id', array('condition_name' => 'condition.name'));
        //$select->joinLeft(array('rma' => $this->getTable('rma/rma')), 'main_table.rma_id = rma.rma_id', array('rma_increment_id' => 'rma.increment_id'));
        // $select->columns(array('is_replied' => new Zend_Db_Expr("answer <> ''")));
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->initFields();
    }

     /************************/

}