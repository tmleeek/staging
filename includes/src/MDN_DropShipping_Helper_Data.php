<?php

class MDN_DropShipping_Helper_Data extends Mage_Core_Helper_Abstract {

    //statuses for order items drop ship status
    const STATUS_DROPSHIP_PRICE_REQUEST_SENT = 'price_request_sent';
    const STATUS_DROPSHIP_REQUEST_SENT = 'dropship_request_sent';
    const STATUS_DROPSHIP_REQUEST_CONFIRMED = 'dropship_request_confirmed';
    const STATUS_DROPSHIPPED = 'dropshipped';
    const STATUS_PO_CREATED = 'po_created';

    //drop ship modes
    const DROPSHIPMODE_PRICEREQUEST = 'price_request';
    const DROPSHIPMODE_DROPSHIPREQUEST = 'dropshiprequest';
    const DROPSHIPMODE_DROPSHIP = 'dropship';
    const DROPSHIPMODE_CREATEPO = 'createpo';
    
    /**
     * Return drop shippable order ids
     */
    public function getDropShippingOrderIds() {

        //Drop shipping order ids = orders having 1+ product that is not in stock in main warehouse
        //and that is drop shippable in product / supplier information
        // add filter on order status set in dropshipping configuration

        $prefix = Mage::getConfig()->getTablePrefix();

        // get the allowed status from config
        $allowedStatus = Mage::getStoreConfig("dropshipping/drop_shippable_order/display_order_with_status");
        $status = explode(",", $allowedStatus);
        $requiredStatus = "";
        foreach ($status as $statu) {
            $requiredStatus .= "'" . $statu . "',";
        }
       $requiredStatus = substr($requiredStatus, 0, strlen($requiredStatus) -1);

        $sql = "SELECT DISTINCT(a.entity_id)
				FROM 
                                `" . $prefix . "sales_flat_order` AS a
				LEFT JOIN `" . $prefix . "sales_flat_order_item` AS b ON (a.entity_id = b.order_id)
				LEFT JOIN `" . $prefix . "purchase_product_supplier` AS c ON (c.pps_product_id = b.product_id)
				LEFT JOIN `" . $prefix . "erp_sales_flat_order_item` AS d ON (b.item_id = d.esfoi_item_id)
				WHERE a.status IN ( ".$requiredStatus." )
				AND (b.product_id = c.pps_product_id)
				and dropship_status is null
                                and stocks_updated = 1
                                and d.preparation_warehouse > 0
                                ";
        if ($requiredStatus)
            $sql .= "	AND a.status IN ( " . $requiredStatus . " ) ";
        if (Mage::getStoreConfig('dropshipping/drop_shippable_order/require_dropshippable') == 1)
            $sql .= " AND (c.pps_can_dropship = 1) ";

        if (Mage::getStoreConfig('dropshipping/drop_shippable_order/display_orders_with_stock') != 1)
            $sql .= " AND (b.qty_ordered - b.qty_shipped - d.reserved_qty > 0) ";
        else
            $sql .= " AND (b.qty_ordered - b.qty_shipped > 0) ";


        $checkStocks = Mage::getStoreConfig('dropshipping/drop_shippable_order/check_supplier_stock');
        if ($checkStocks)
            $sql .= " AND (c.pps_quantity_product > 0) ";
        
        $ids = mage::getResourceModel('catalog/product')->getReadConnection()->fetchCol($sql);

        return $ids;
    }

    /**
     * Return order ids for which we are pending a price response from supplier
     * 
     * @return type
     */
    public function getPendingPriceResponseOrderIds()
    {
        $prefix = Mage::getConfig()->getTablePrefix();

        $sql = "SELECT DISTINCT(a.entity_id)
				FROM `" . $prefix . "sales_flat_order` AS a
				LEFT JOIN `" . $prefix . "sales_flat_order_item` AS b
				ON (a.entity_id = b.order_id)
                                LEFT JOIN `" . $prefix . "erp_sales_flat_order_item` AS d ON (b.item_id = d.esfoi_item_id)
				WHERE a.state NOT IN ('canceled', 'complete')
				AND (b.qty_ordered - b.qty_shipped)
				and dropship_status = '".self::STATUS_DROPSHIP_PRICE_REQUEST_SENT."'";
        $ids = mage::getResourceModel('catalog/product')->getReadConnection()->fetchCol($sql);

        return $ids;
    }
    
    /*
     * Return suppliers able to drop ship product
     */

    public function getDropshipSuppliers($productId, $qty) {
        $suppliers = mage::getModel('Purchase/Supplier')
                ->getCollection()
                ->join('Purchase/ProductSupplier', 'pps_supplier_num=sup_id')
                ->addFieldToFilter('pps_product_id', $productId);

        if (Mage::getStoreConfig('dropshipping/drop_shippable_order/check_supplier_stock'))
            $suppliers->addFieldToFilter('pps_quantity_product', array('gt' => $qty - 1));

        //FIX for dropshiping
        if (Mage::getStoreConfig('dropshipping/drop_shippable_order/require_dropshippable') == 1)
            $suppliers->addFieldToFilter('pps_can_dropship', 1);
        
        return $suppliers;
    }


    /**
     * Cancel association on order item
     */
    public function cancelAssociation($orderItemId) {
        $orderItem = mage::getModel('sales/order_item')->load($orderItemId);

        switch ($orderItem->getdropship_status()) {
            case MDN_DropShipping_Helper_Data::STATUS_PO_CREATED:
                //delete PO
                $poId = $orderItem->getpurchase_order_id();
                $po = mage::getModel('Purchase/Order')->load($poId);
                $po->delete();
                break;
            case MDN_DropShipping_Helper_Data::STATUS_DROPSHIP_REQUEST_SENT:
                //nothing
                break;
        }

        //reset datas
        $orderItem
                ->setdropship_status(new Zend_Db_Expr('null'))
                ->setpurchase_order_id(new Zend_Db_Expr('null'))
                ->setdropship_supplier_id(new Zend_Db_Expr('null'))
                ->save();
    }

    /**
     * Process drop ship orders (used for cron)
     *
     * @param array $data
     * @param array $modes
     * @param array $comments 
     */
    public function processDropShip($datas, $modes, $comments) {

        $orderToDropShip = array();
        $orderToCreatePo = array();
        $orderToRequestDropShip = array();
        $requestCancel = array();

        $createdPo = array();

        $error = false;
        $errorMessage = '';
        $successMessage = '';

        foreach ($datas as $orderId => $orderItems) {
            foreach ($orderItems as $orderItemId => $supplierId) {
                //do not consider orderItems without supplier
                if (!$supplierId)
                    continue;

                //init array with suppliers
                if (!isset($orderToDropShip[$supplierId]))
                    $orderToDropShip[$supplierId] = array();
                if (!isset($orderToCreatePo[$supplierId]))
                    $orderToCreatePo[$supplierId] = array();

                //add order items in the right array
                $mode = $modes[$orderId][$orderItemId];
                switch ($mode) {
                    case 'dropship':
                        if (!isset($orderToDropShip[$supplierId][$orderId]))
                            $orderToDropShip[$supplierId][$orderId] = array();
                        $orderToDropShip[$supplierId][$orderId][] = $orderItemId;
                        break;
                    case 'createpo':
                        if (!isset($orderToCreatePo[$supplierId][$orderId]))
                            $orderToCreatePo[$supplierId][$orderId] = array();
                        $orderToCreatePo[$supplierId][$orderId][] = $orderItemId;
                        break;
                    case 'dropshiprequest':
                        if (!isset($orderToRequestDropShip[$supplierId][$orderId]))
                            $orderToRequestDropShip[$supplierId][$orderId] = array();
                        $orderToRequestDropShip[$supplierId][$orderId][] = $orderItemId;
                        break;
                    case 'cancel':
                        $requestCancel[] = $orderItemId;
                        break;
                }
            }
        }

        //process drop ship
        foreach ($orderToDropShip as $supplierId => $orders) {
            foreach ($orders as $orderId => $orderItems) {
                try {
                    //merge item comments
                    $orderComments = '';
                    if (isset($comments[$orderId])) {
                        foreach ($comments[$orderId] as $key => $itemComment)
                            $orderComments .= $itemComment . "\n";
                    }

                    $createdPo[] = mage::helper('DropShipping/DropShip')->dropShipOrder($supplierId, $orderId, $orderItems, $orderComments);
                } catch (Exception $ex) {
                    $error = true;
                    $errorMessage .= "\n" . $ex->getMessage();
                }
            }
        }

        //process PO creation
        foreach ($orderToCreatePo as $supplierId => $orders) {
            if (count($orders) == 0)
                continue;

            try {
                $createdPo[] = mage::helper('DropShipping/CreatePo')->createPo($supplierId, $orders, $comments);
            } catch (Exception $ex) {
                $error = true;
                $errorMessage .= "\n" . $ex->getMessage();
            }
        }

        //process drop ship request
        foreach ($orderToRequestDropShip as $supplierId => $orders) {
            if (count($orders) == 0)
                continue;

            try {
                $requestedDropShip[] = mage::helper('DropShipping/DropShipRequest')->sendRequest($supplierId, $orders, $comments);
            } catch (Exception $ex) {
                $error = true;
                $errorMessage .= "\n" . $ex->getMessage();
            }
        }

        //process cancel action
        foreach ($requestCancel as $orderItemId) {
            mage::helper('DropShipping')->cancelAssociation($orderItemId);
        }

        //confirm & redirect
        $poCount = count($createdPo);
        if ($poCount > 0) {
            $confirmMsg = $this->__('%s PO created : ', $poCount);
            foreach ($createdPo as $po) {
                $confirmMsg .= '<a href="' . mage::helper('adminhtml')->getUrl('Purchase/Orders/Edit', array('po_num' => $po->getId())) . '">' . $po->getpo_order_id() . '</a>, ';
            }
            $successMessage .= "\n" . $confirmMsg;
        }
        if (count($orderToRequestDropShip) > 0) {
            $successMessage .= "\n" . sprintf('%s dropship request sent', count($orderToRequestDropShip));
        }
        if (count($requestCancel) > 0) {
            $successMessage .= "\n" . sprintf('%s supplier request cancelled', count($requestCancel));
        }

        return array(
            'error' => $error,
            'errorMessage' => $errorMessage,
            'successMessage' => $successMessage
        );
    }
    
    /**
    * return the table with the dropship mode
    * @return type array
    */
    public function getDropShipMode(){
        
        $dropShipMode = array();
        
        if (Mage::getStoreConfig('dropshipping/available_actions/dropship'))
            $dropShipMode['dropship'] = mage::helper('DropShipping')->__('DropShip');
        
        if (Mage::getStoreConfig('dropshipping/available_actions/dropship_request'))
            $dropShipMode['dropshiprequest'] = mage::helper('DropShipping')->__('Dropship Request');
        
        if (Mage::getStoreConfig('dropshipping/available_actions/pricerequest'))
            $dropShipMode[self::DROPSHIPMODE_PRICEREQUEST] = mage::helper('DropShipping')->__('Price request');
        
        if (Mage::getStoreConfig('dropshipping/available_actions/create_purchase_order'))
            $dropShipMode[self::DROPSHIPMODE_CREATEPO] = mage::helper('DropShipping')->__('Create Purchase Order');
        
        return $dropShipMode;
    }

    /**
     * 
     * @param type $ex
     */
    public function logException($ex)
    {
        mage::log($ex->getMessage().' : '.$ex->getTraceAsString(), null, 'dropship_exception.log');
    }

   public function log($msg)
    {
        mage::log($msg, null, 'dropship.log');
    }
}