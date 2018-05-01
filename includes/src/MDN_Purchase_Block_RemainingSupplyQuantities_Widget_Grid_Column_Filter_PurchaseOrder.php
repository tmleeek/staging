<?php

class MDN_Purchase_Block_RemainingSupplyQuantities_Widget_Grid_Column_Filter_PurchaseOrder extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Text {

    public function getCondition() {
        $searchString = trim($this->getValue());
        if ($searchString == '')
            return;

        $purchaseOrderIds = array();

        //create purchase order ids array
        $model = mage::getResourceModel('Purchase/Order_collection');
        $sql = $model->getSelect()
                        ->join(array('supplier' => $model->getTable('Supplier')),
                                'po_sup_num = sup_id',
                                array('*'))
                        ->where("(po_order_id like '%" . $searchString . "%' OR sup_name like '%" . $searchString . "%')");
        $collection = $model->getConnection()->fetchAll($sql);
        
        foreach ($collection as $item) {
            $purchaseOrderIds[] = $item['po_num'];
        }     

        return array('in' => $purchaseOrderIds);
    }

}