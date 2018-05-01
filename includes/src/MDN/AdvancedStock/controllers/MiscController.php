<?php

class MDN_AdvancedStock_MiscController extends Mage_Adminhtml_Controller_Action {

    /**
     * Display mass stock editor grid
     *
     */
    public function MassStockEditorAction() {
        $this->loadLayout();

        $this->_setActiveMenu('erp');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Mass Stock Editor'));

        $this->renderLayout();
    }
    
    /**
     * Return mass stock editor grid using Ajax 
     */
    public function MassStockEditorAjaxAction()
    {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('AdvancedStock/MassStockEditor_Grid');
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * apply mass stock editor changes
     *
     */
    public function MassStockSaveAction() {
        
        $datas = $this->getRequest()->getPost('mass_stock_editor_logs');
        $datas = $this->convertChangesData($datas);

        foreach($datas as $stockItemId => $data)
        {
            $stockItem = Mage::getModel('cataloginventory/stock_item')->load($stockItemId);
            foreach($data as $name => $value)
            {
                $stockItem->setData($name, $value);
            }
            $stockItem->save();
        }
        
        //confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Data saved'));
        $this->_redirect('AdvancedStock/Misc/MassStockEditor');
        
    }

    /**
     * Convert data from persistant grid to array
     * 
     * @param type $data 
     */
    protected function convertChangesData($flatDatas)
    {
        $datas = array();
        
        $flatDatas = explode(';', $flatDatas);
        foreach($flatDatas as $flatData)
        {
            $fields = explode('=', $flatData);
            if (count($fields) != 2)
                continue;
            $value = $fields[1];
            $lastUnderscore = strrpos($fields[0], '_');
            $fieldName = substr($fields[0], 0, $lastUnderscore);
            $pk = substr($fields[0], $lastUnderscore + 1);
            
            if (!isset($datas[$pk]))
                $datas[$pk] = array();
            $datas[$pk][$fieldName] = $value;
        }
        
        return $datas;
    }
    
    
    /**
     * Mass action to validate payment
     *
     */
    public function ValidatepaymentAction() {
        $orderIds = $this->getRequest()->getPost('order_ids');
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $order = mage::getModel('sales/order')->load($orderId);
                $order->setpayment_validated(1)->save();
            }
        }

        //Confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Payments validated'));
        $this->_redirect('adminhtml/sales_order/');
    }

    /**
     * Mass action to cancel payment
     *
     */
    public function CancelpaymentAction() {
        $orderIds = $this->getRequest()->getPost('order_ids');
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $order = mage::getModel('sales/order')->load($orderId);
                $order->setpayment_validated(0)->save();
            }
        }

        //Confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Payments canceled'));
        $this->_redirect('adminhtml/sales_order/');
    }

    /**
     * Change sales order payment (from sales order sheet)
     *
     */
    public function SavepaymentAction() {
        //recupere les infos
        $orderId = $this->getRequest()->getParam('order_id');
        $value = $this->getRequest()->getParam('payment_validated');

        //Charge la commande et modifie
        $order = mage::getModel('sales/order')->load($orderId);
        $order->setpayment_validated($value)->save();

        //Confirme
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Payment state updated'));

        //redirige
        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
    }

    /**
     * Change sales order is_valid (from sales order sheet)
     *
     */
    public function SaveIsValidAction() {
        //recupere les infos
        $orderId = $this->getRequest()->getParam('order_id');
        $value = $this->getRequest()->getParam('is_valid');

        //Charge la commande et modifie
        $order = mage::getModel('sales/order')->load($orderId);
        $order->setis_valid($value);
        $order->save();

        //Confirme
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Order validity updated'));

        //redirige
        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
    }


    //************************************************************************************************************************************************************
    //************************************************************************************************************************************************************
    //STOCK ERRRORS
    //************************************************************************************************************************************************************
    //************************************************************************************************************************************************************

    /**
     * Display stock error grid
     *
     */
    public function IdentifyErrorsAction() {
        $this->loadLayout();

        $this->_setActiveMenu('erp');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Stock Errors'));

        $this->renderLayout();
    }

    /**
     * Refresh stock error list
     *
     */
    public function RefreshErrorListAction() {
        mage::helper('AdvancedStock/StockError')->refresh();
    }

    /**
     * try to fix error
     *
     */
    public function FixErrorAction() {
        //retrieve data
        $stockErrorId = $this->getRequest()->getParam('se_id');

        try {
            $stockError = mage::getModel('AdvancedStock/StockError')->load($stockErrorId);
            if ($stockError->getId())
                $stockError->fix();
            else
                throw new Exception('Unable to find stock !');
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Error fixed'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : ') . $ex->getMessage());
        }

        //redirect
        $this->_redirect('AdvancedStock/Misc/IdentifyErrors');
    }

    /**
     * Try to fix all errors
     *
     */
    public function MassFixErrorsAction() {
        mage::helper('AdvancedStock/StockError')->fixAllErrors();
    }

    /**
     * Update is valid for all orders
     *
     */
    public function UpdateIsValidForAllOrdersAction() {
        $taskGroup = 'refresh_is_valid';
        mage::helper('BackgroundTask')->AddGroup($taskGroup, mage::helper('AdvancedStock')->__('Refresh is_valid for orders'), 'AdvancedStock/Misc/ConfirmUpdateIsValidForAllOrders');

        //plan task for each orders
        $collection = mage::getModel('sales/order')
                ->getCollection()
                ->addAttributeToFilter('state', array('nin' => array('complete', 'canceled')));

        $ids = array();

        //browse collection differently depending of magento version to avoid crash and to fast up process
        if(mage::helper('AdvancedStock/MagentoVersionCompatibility')->useGetAllIdsOnSaleOrderModelCollection()){          
          $ids = $collection->getAllIds();
        }else{
          foreach ($collection as $order) {
              $ids[] = $order->getId();
          }
        }

        foreach ($ids as $orderId) {
              mage::helper('BackgroundTask')->AddTask('Update is_valid for order #' . $orderId,
                      'AdvancedStock/Sales_ValidOrders',
                      'UpdateIsValidWithSave',
                      $orderId, $taskGroup
              );
        }

        //execute task group
        mage::helper('BackgroundTask')->ExecuteTaskGroup($taskGroup);
    }

    public function ConfirmUpdateIsValidForAllOrdersAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function pdfordersAction()
   {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds))
		{
            foreach ($orderIds as $orderId)
			{
            	$order = Mage::getModel('sales/order')->load($orderId);

                $orders = Mage::getResourceModel('sales/order_collection')
                    ->addAttributeToSelect('*')
					->addAttributeToFilter('entity_id', $orderId)
					->load();


				if ($orders->getSize() > 0) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('attachpdf/sales_order_pdf_order')->getPdf($orders);
                    } else {
                        $pages = Mage::getModel('attachpdf/sales_order_pdf_order')->getPdf($orders);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse('order'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders'));
                $this->_redirect('*/*/');
            }

        }
        $this->_redirect('*/*/');

    }


     public function undoOrderAction() {

        $orderIds = $this->getRequest()->getPost('order_ids', array());

        $countCancelOrder = 0;
        $countNonCancelOrder = 0;
        foreach ($orderIds as $orderId) {
               $order = Mage::getModel('sales/order')->load($orderId);
               $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
               $order->setStatus('pending');

              $order->setBaseDiscountCanceled(0);
              $order->setBaseShippingCanceled(0);
              $order->setBaseSubtotalCanceled(0);
              $order->setBaseTaxCanceled(0);
              $order->setBaseTotalCanceled(0);
              $order->setDiscountCanceled(0);
              $order->setShippingCanceled(0);
              $order->setSubtotalCanceled(0);
              $order->setTaxCanceled(0);
              $order->setTotalCanceled(0);

              foreach($order->getAllItems() as $item){
                  $item->setQtyCanceled(0);
                  $item->setTaxCanceled(0);
                  $item->setHiddenTaxCanceled(0);
                  $item->save();
              }
               $countCancelOrder++;
               $order->save();
            }

        if ($countNonCancelOrder) {
            if ($countCancelOrder) {
                $this->_getSession()->addError($this->__('%s order(s) could not be updated', $countNonCancelOrder));
                //$this->_redirect('adminhtml/sales_order/');
            } else {
                $this->_getSession()->addError($this->__('Order(s) can not be updated'));
                // $this->_redirect('adminhtml/sales_order/');
            }
        }
        if ($countCancelOrder) {
            $this->_getSession()->addSuccess($this->__('%s order(s) successfully updated', $countCancelOrder));
            //$this->_redirect('adminhtml/sales_order/');
        }
         $this->_redirect('adminhtml/sales_order/');   
    }

}