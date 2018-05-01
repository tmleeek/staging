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


class Mirasvit_Rma_Model_Resource_Rma_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('rma/rma');
    }

    public function toOptionArray($emptyOption = false)
    {
        $arr = $this->_toOptionArray('rma_id', 'name');
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


    public function addStoreIdFilter($storeId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('rma/rma_store')}`
                AS `rma_store_table`
                WHERE main_table.rma_id = rma_store_table.rs_rma_id
                AND rma_store_table.rs_store_id in (?))", array(0, $storeId));
        return $this;
    }

    protected function initFields()
    {
        $select = $this->getSelect();
        $select->joinLeft(array('order' => $this->getTable('sales/order')), 'main_table.order_id = order.entity_id', array('order_increment_id' => 'order.increment_id'));        //$select->joinLeft(array('customer' => $this->getTable('customer/customer')), 'main_table.customer_id = customer.customer_id', array('customer_name' => 'customer.name'));
        $select->joinLeft(array('status' => $this->getTable('rma/status')), 'main_table.status_id = status.status_id', array('status_name' => 'status.name'));
        $select->columns(array('name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)")));
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->initFields();
    }

     /************************/

}