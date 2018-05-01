<?php

/**
 * Order preparation item
 *
 */
class MDN_Orderpreparation_Model_OrderToPrepareItem extends Mage_Core_Model_Abstract {

    private $_salesOrderItem = null;

    /**
     * Construct 
     */
    public function _construct() {
        parent::_construct();
        $this->_init('Orderpreparation/ordertoprepareitem');
    }

    /**
     * Return sales order item
     * @return type 
     */
    public function getSalesOrderItem() {
        if ($this->_salesOrderItem == null)
        {
            $id = $this->getorder_item_id();
            $this->_salesOrderItem = mage::getModel('sales/order_item')->load($id);
        }
        return $this->_salesOrderItem;
    }

    /**
     * Before delete
     * @return type 
     */
    protected function _beforeDelete()
    {
        //delete child items
        $child = Mage::getModel('sales/order_item')
                        ->getCollection()
                        ->addFieldToFilter('parent_item_id', $this->getorder_item_id());
        foreach($child as $children)
        {
            $orderToPrepareItem = Mage::getModel('Orderpreparation/ordertoprepareitem')->load($children->getId(), 'order_item_id');
            $orderToPrepareItem->delete();
        }
        
        return parent::_beforeDelete();
    }
    

}
