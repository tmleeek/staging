<?php

class MDN_DropShipping_AdminController extends Mage_Adminhtml_Controller_Action {

    /**
     * Display main screen
     */
    public function GridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function ajaxDropShippableGridAction() {
        $block = $this->getLayout()->createBlock('DropShipping/Tabs_DropShippable');
        $this->getResponse()->setBody($block->toHtml());
    }

    public function ajaxPendingPriceResponseGridAction() {
        $block = $this->getLayout()->createBlock('DropShipping/Tabs_PendingPriceResponse');
        $this->getResponse()->setBody($block->toHtml());
    }

    public function ajaxPendingSupplierDeliveryGridAction() {
        $block = $this->getLayout()->createBlock('DropShipping/Tabs_PendingSupplierDelivery');
        $this->getResponse()->setBody($block->toHtml());
    }

    public function ajaxPendingSupplierResponseGridAction() {
        $block = $this->getLayout()->createBlock('DropShipping/Tabs_PendingSupplierResponse');
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * display the grid log in tools -> drop shipped order
     */
    public function GridDropShippingPOAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Method to refresh supplier import log grid using ajax for DROPSHIPPING
     */
    public function ImportLogGridAction() {
        $this->loadLayout();
        $Block = $this->getLayout()
                ->createBlock('DropShipping/Supplier_Edit_Tabs_Log')
                ->toHtml();
        $this->getResponse()->setBody($Block);
    }

    /**
     * Method to refresh supplier import log grid using ajax for DROPSHIPPING
     */
    public function DropshippedHistoryAction() {
        $this->loadLayout();
        $Block = $this->getLayout()
                ->createBlock('DropShipping/Tabs_DropshippedHistory')
                ->toHtml();
        $this->getResponse()->setBody($Block);
    }

    /**
     * Cancel order item association
     */
    public function CancelAction() {
        $itemId = $this->getRequest()->getParam('item_id');

        mage::helper('DropShipping')->cancelAssociation($itemId);

        Mage::getSingleton('adminhtml/session')->addSuccess('Action cancelled');
        $this->_redirect('DropShipping/Admin/Grid');
    }

    /**
     * Simple test action 
     */
    public function cancelDropShipAction() {
        $id = $this->getRequest()->getParam('po_id');

        
        $result = array('success' => true, 'msg' => '');
        try
        {
            Mage::helper('DropShipping/DropShipRequest')->cancel($id);
        }
        catch(Exception $ex)
        {
            $result['success'] = false;
            $result['msg'] = $ex->getMessage();
            Mage::helper('DropShipping')->logException($ex);
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * 
     */
    public function confirmDropShipRequestAction() {
        //load PO
        $data = $this->getRequest()->getPost();
        $po = Mage::getModel('Purchase/Order')->load($data['po_num']);

        $result = array('success' => true, 'msg' => '');
        try
        {
            Mage::helper('DropShipping/DropShipRequest')->confirmDropShipRequest($po, $data['shipping'], $data['products'], $data['po_supplier_order_ref']);
        }
        catch(Exception $ex)
        {
            $result['success'] = false;
            $result['msg'] = $ex->getMessage();
            Mage::helper('DropShipping')->logException($ex);
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * 
     */
    public function confirmDropShipShippingAction() {
        //get data
        $poId = $this->getRequest()->getParam('po_id');
        $tracking = $this->getRequest()->getParam('tracking');
        $po = Mage::getModel('Purchase/Order')->load($poId);

        
        $result = array('success' => true, 'msg' => '');
        try
        {
            //confirm the delivery
            Mage::helper('DropShipping/DropShipRequest')->confirmDropShipShipping($po, $tracking);
        
        }
        catch(Exception $ex)
        {
            $result['success'] = false;
            $result['msg'] = $ex->getMessage();
            Mage::helper('DropShipping')->logException($ex);
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * 
     */
    public function applyDropShippableAction() {
        
        //get data
        $data = $this->getRequest()->getPost();
        $orderId = $data['order_id'];
        $dropShips = array();
        $dropShipRequests = array();
        $priceRequests = array();
        $createPos = array();

        $result = array('success' => true, 'msg' => '');
        try
        {
        
            //parse items and group per mode
            foreach ($data['item'] as $itemId => $itemData) {
                $supplier = $itemData['supplier'];
                $mode = $itemData['mode'];
               
                switch ($mode) {
                    case MDN_DropShipping_Helper_Data::DROPSHIPMODE_DROPSHIP:
                        if ($supplier)
                        {
                            if (!isset($dropShips[$supplier]))
                                $dropShips[$supplier] = array();
                            $dropShips[$supplier][$itemId] = array();    // array('comments' => '', 'price' => '');
                        }
                        break;
                    case MDN_DropShipping_Helper_Data::DROPSHIPMODE_DROPSHIPREQUEST:
                        if ($supplier)
                        {
                            if (!isset($dropShipRequests[$supplier]))
                                $dropShipRequests[$supplier] = array();
                            $dropShipRequests[$supplier][$itemId] = array();    // array('comments' => '', 'price' => '');
                        }
                        break;
                    case MDN_DropShipping_Helper_Data::DROPSHIPMODE_PRICEREQUEST:
                        $priceRequests[] = $itemId;
                        break;
                    case MDN_DropShipping_Helper_Data::DROPSHIPMODE_CREATEPO:
                        if ($supplier)
                        {
                            if (!isset($createPos[$supplier]))
                                $createPos[$supplier] = array();
                            $createPos[$supplier][$itemId] = array();    // array('comments' => '', 'price' => '');
                        }
                        break;
                }
            }

            //process price requests
            if (count($priceRequests) > 0) {
                Mage::helper('DropShipping/PriceRequest')->sendRequest($priceRequests);
            }

            //process drop ship
            if (count($dropShips) > 0) {
                foreach ($dropShips as $supId => $items) {
                    $po = Mage::helper('DropShipping/DropShipRequest')->sendRequest($supId, $orderId, $items);
                    Mage::helper('DropShipping/DropShipRequest')->confirmDropShipRequest($po);
                }
            }

            //process create PO
            if (count($createPos) > 0) {
                foreach ($createPos as $supId => $items) {
                    $po = Mage::helper('DropShipping/CreatePo')->process($supId, $items);
                }
            }
            
            //process drop ship requests
            if (count($dropShipRequests) > 0) {
                foreach ($dropShipRequests as $supId => $items) {
                    Mage::helper('DropShipping/DropShipRequest')->sendRequest($supId, $orderId, $items);
                }
            }
            
        }
        catch(Exception $ex)
        {
            $result['success'] = false;
            $result['msg'] = $ex->getMessage();
            Mage::helper('DropShipping')->logException($ex);
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            
    }

    /**
     * 
     */
    public function applyPendingPriceResponseAction() {
        //get data
        $data = $this->getRequest()->getPost();
        $orderId = $data['order_id'];
        $suppliers = array();

        $result = array('success' => true, 'msg' => '');
        try
        {
        
            //browse items
            foreach ($data['item'] as $itemId => $itemData) {
                switch ($itemData['mode']) {
                    case 'cancel':
                        Mage::helper('DropShipping')->cancelAssociation($itemId);
                        break;
                    case 'confirm':
                        $supplierId = $itemData['supplier'];
                        $price = $itemData['price'];
                        $shipping = $itemData['shipping'];
                        if (!isset($suppliers[$supplierId]))
                            $suppliers[$supplierId] = array('shipping' => $shipping, 'items' => array());
                        $suppliers[$supplierId]['items'][$itemId] = array('price' => $price);
                        break;
                }
            }

            //perform operation
            if (count($suppliers) > 0) {
                foreach ($suppliers as $supplierId => $supplierData) {
                    //create drop ship request
                    $po = Mage::helper('DropShipping/DropShipRequest')->sendRequest($supplierId, $orderId, $supplierData['items'], array('shipping' => $supplierData['shipping']));

                    //confirm it if enabled
                    if (Mage::getStoreConfig('dropshipping/price_response/confirmation_action') == 'dropship')
                        Mage::helper('DropShipping/DropShipRequest')->confirmDropShipRequest($po);
                }
            }
        }
        catch(Exception $ex)
        {
            $result['success'] = false;
            $result['msg'] = $ex->getMessage();
            Mage::helper('DropShipping')->logException($ex);
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

}
