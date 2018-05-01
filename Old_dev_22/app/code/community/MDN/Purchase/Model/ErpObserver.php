<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Purchase_Model_ErpObserver {

    /**
     * Add custom tabs to erp product sheet
     *
     */
    public function advancedstock_product_edit_create_tabs(Varien_Event_Observer $observer) {

        //init vars
        $tab = $observer->getEvent()->gettab();
        $product = $observer->getEvent()->getproduct();
        $layout = $observer->getEvent()->getlayout();


        if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/products/view/purchase_settings')) {
            $tab->addTab('tab_purchase_settings', array(
                'label' => Mage::helper('purchase')->__('Purchase Settings'),
                'content' => $layout->createBlock('Purchase/Product_Edit_Tabs_Settings')
                        ->setTemplate('Purchase/Product/Edit/Tab/Settings.phtml')
                        ->setProduct($product)
                        ->toHtml(),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/products/view/purchase_orders')) {
            $tab->addTab('tab_po', array(
                'label' => Mage::helper('purchase')->__('Purchase Orders'),
                'content' => $layout->createBlock('Purchase/Product_Edit_Tabs_AssociatedOrdersGrid')->setProduct($product)->toHtml(),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/products/view/suppliers')) {
            $tab->addTab('tab_suppliers', array(
                'label' => Mage::helper('purchase')->__('Suppliers'),
                'content' => $layout
                        ->createBlock('Purchase/Product_Edit_Tabs_AssociatedSuppliers')
                        ->setShowForm(true)
                        ->setTemplate('Purchase/Product/Edit/Tab/AssociatedSuppliers.phtml')
                        ->setProduct($product)
                        ->toHtml(),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/products/view/packaging')) {
            if (mage::helper('purchase/Product_Packaging')->isEnabled()) {
                $tab->addTab('tab_packaging', array(
                    'label' => Mage::helper('purchase')->__('Packaging'),
                    'content' => $layout
                            ->createBlock('Purchase/Product_Edit_Tabs_Packaging')
                            ->setTemplate('Purchase/Product/Edit/Tab/Packaging.phtml')
                            ->setProduct($product)
                            ->toHtml(),
                ));
            }
        }
    }

    /**
     * Save custom data from erp product sheet
     *
     * @param Varien_Event_Observer $observer
     */
    public function advancedstock_product_sheet_save(Varien_Event_Observer $observer) {


        //init vars
        $data = $observer->getEvent()->getpost_data();
        $product = $observer->getEvent()->getproduct();
        


        if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/products/view/purchase_settings')) {
            //save custom data
            $purchaseData = $data['purchase'];

            if (!isset($purchaseData['exclude_from_supply_needs']))
                $purchaseData['exclude_from_supply_needs'] = '0';
            if (!isset($purchaseData['default_supply_delay']) || ($purchaseData['default_supply_delay'] == ''))
                $purchaseData['default_supply_delay'] = new Zend_Db_Expr('null');

            foreach ($purchaseData as $key => $value) {
                $product->setData($key, $value);
            }


            $product->save();
            
        }

        //******************************
        //save packaging data
        if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/products/view/packaging')) {
            if (mage::helper('purchase/Product_Packaging')->isEnabled()) {

                //save existing packages
                $packagingData = $data['packaging'];
                $packagings = mage::helper('purchase/Product_Packaging')->getPackagingForProduct($product->getId());
                foreach ($packagings as $packaging) {
                    $id = $packaging->getId();

                    if (!isset($packagingData[$id]['delete']) || !$packagingData[$id]['delete']) {
                        //force radio values
                        if (!isset($packagingData[$id]['pp_is_default_sales']))
                            $packagingData[$id]['pp_is_default_sales'] = 0;
                        if (!isset($packagingData[$id]['pp_is_default']))
                            $packagingData[$id]['pp_is_default'] = 0;

                        foreach ($packagingData[$id] as $key => $value)
                            $packaging->setData($key, $value);
                        $packaging->save();
                    }
                    else
                        $packaging->delete();
                }


                //add new packaging
                if ($packagingData['new']['pp_name'] != '') {
                    $newPackaging = mage::getModel('Purchase/Packaging')
                            ->setpp_product_id($product->getId())
                            ->setpp_name($packagingData['new']['pp_name'])
                            ->setpp_qty($packagingData['new']['pp_qty'])
                            ->setpp_is_default(isset($packagingData['new']['pp_is_default']) ? ($packagingData['new']['pp_is_default']) : 0)
                            ->setpp_is_default_sales((isset($packagingData['new']['pp_is_default_sales']) ? $packagingData['new']['pp_is_default_sales'] : 0))
                            ->save();
                }
            } //endif product packaging enabled
        }
    }

    /**
     * Event raised after catalog_product saved
     *
     * @param Varien_Event_Observer $observer
     */
    public function advancedstock_product_aftersave(Varien_Event_Observer $observer) {
        $product = $observer->getEvent()->getproduct();
        $productId = $product->getId();

        $manualSupplyNeedQtyAfterSave = $product->getmanual_supply_need_qty();
        $manualSupplyNeedQtyBeforeSave = $product->getOrigData('manual_supply_need_qty');
        $excludeFromSupplyNeedAfterSave = $product->getexclude_from_supply_needs();
        $excludeFromSupplyNeedBeforeSave = $product->getOrigData('exclude_from_supply_needs');

        //check if manufacturer as changed
        if (Mage::getStoreConfig('purchase/manufacturer_supplier_synchronization/auto_sync')) {
            $manufacturerAttributeName = Mage::getStoreConfig('purchase/manufacturer_supplier_synchronization/manufacturer_attribute');
            if ($manufacturerAttributeName) {
                $manufacturerBefore = $product->getOrigData($manufacturerAttributeName);
                $manufacturerAfter = $product->getData($manufacturerAttributeName);
                if ($manufacturerBefore != $manufacturerAfter) {
                    $manufacturerCode = $manufacturerAfter;
                    $manufacturerName = $product->getAttributeText($manufacturerAttributeName);

                    //remove product from "old" supplier
                    $helper = Mage::helper('purchase/Supplier');
                    if ($manufacturerBefore) {
                        $helper->removeProductManufacturerAssociation($productId, $manufacturerBefore);
                    }

                    //link product to supplier
                    if ($manufacturerName) {
                        $supplier = $helper->createSupplierFromManufacturer($manufacturerCode, $manufacturerName);
                        $helper->linkProductToSupplier($productId, $supplier->getId());
                    }
                }
            }
        }
    }

    /**
     * Waiting for delivery qty has changed for product
     * */
    public function product_waiting_for_delivery_qty_change(Varien_Event_Observer $observer) {
        $productId = $observer->getEvent()->getproduct_id();
    }

    /**
     * Event raised when stock changes
     * Check if we have to update supply needs
     *
     * @param Varien_Event_Observer $observer
     */
    public function advancedstock_stock_aftersave(Varien_Event_Observer $observer) {
        $stock = $observer->getEvent()->getstock();

        $qtyAfterSave = $stock->getqty();
        $qtyBeforeSave = $stock->getOrigData('qty');
        $stockOrderedQtyAfterSave = $stock->getstock_ordered_qty();
        $stockOrderedQtyBeforeSave = $stock->getOrigData('stock_ordered_qty');
        $stockOrderedQtyForValidOrdersAfterSave = $stock->getstock_ordered_qty_for_valid_orders();
        $stockOrderedQtyForValidOrdersBeforeSave = $stock->getOrigData('stock_ordered_qty_for_valid_orders');
        $manageStockAfterSave = $stock->getmanage_stock();
        $manageStockBeforeSave = $stock->getOrigData('manage_stock');
        $notifyQtyAfterSave = $stock->getnotify_stock_qty();
        $notifyQtyBeforeSave = $stock->getOrigData('notify_stock_qty');
        $idealStockAfterSave = $stock->getideal_stock_level();
        $idealStockBeforeSave = $stock->getOrigData('ideal_stock_level');

        //update product cost if qty change
        if ($qtyAfterSave != $qtyBeforeSave) {
            $productId = $stock->getproduct_id();
            mage::helper('BackgroundTask')->AddTask('Update cost for product #' . $productId, 'purchase/Product', 'updateProductCost', $productId, null, true, 5
            );
        }
    }

    /**
     * Add product waiting for delivery qty in stock error check
     *
     */
    public function advancedstock_check_stock_error(Varien_Event_Observer $observer) {

        //if disabled
        if (Mage::getStoreConfig('advancedstock/stock_errors/check_purchase_values') == 0)
            return;

        //init vars
        $stock = $observer->getEvent()->getstock();
        $comments = $observer->getEvent()->getcomments();

        $productId = $stock->getproduct_id();
        $product = mage::getModel('catalog/product')->load($productId);       

        if($product->getId()>0){
            //Waiting for delivery Qty :
            $expectedWaitingForDeliveryQty = mage::helper('purchase/Product')->computeProductWaitingForDeliveryQty($productId);
            $waitingForDeliveryQty = $product->getwaiting_for_delivery_qty();
            if ($waitingForDeliveryQty != $expectedWaitingForDeliveryQty) {
                $comments = mage::helper('purchase')->__('Qty to be delivered by supplier (%s) is incorrect, expected : (%s)', $waitingForDeliveryQty, $expectedWaitingForDeliveryQty) . ', ';
                throw new Exception($comments);
            }


            //Next Supply date :
            // 10 is the length of a date : ex : 2013-10-23 is 10 char len
            $nextSupplyDate = $product->getsupply_date();
            $nextSupplyDate = trim((string) $nextSupplyDate);
            if (strlen($nextSupplyDate) >= 10)
                $nextSupplyDate = substr($nextSupplyDate, 0, 10);

            $expectedNextSupplyDate = mage::helper('purchase/Product')->computeProductDeliveryDate($productId);
            $expectedNextSupplyDate = trim((string) $expectedNextSupplyDate);
            if (strlen($expectedNextSupplyDate) >= 10)
                $expectedNextSupplyDate = substr($expectedNextSupplyDate, 0, 10);

            if ($expectedNextSupplyDate != $nextSupplyDate) {
                $comments = mage::helper('purchase')->__('Next supply date (%s) is incorrect, expected : (%s)', $nextSupplyDate, $expectedNextSupplyDate);
                throw new Exception($comments);
            }
        }
        
    }

    /**
     * Add purchase logic for error fixing
     *
     * @param Varien_Event_Observer $observer
     */
    public function advancedstock_fix_stock_error(Varien_Event_Observer $observer) {
        $product = $observer->getEvent()->getproduct();
        if($product){
            if($product->getId()>0){
                mage::helper('purchase/Product')->updateProductWaitingForDeliveryQty($product->getId());
                mage::helper('purchase/Product')->updateProductDeliveryDate($product->getId());
            }
        }
    }

    /**
     * Handle to recalculate product waiting for delivery qty and date for product
     *
     * @param Varien_Event_Observer $observer
     */
    public function advancedstock_product_force_stocks_update_requested(Varien_Event_Observer $observer) {
        $product = $observer->getEvent()->getproduct();

        mage::helper('purchase/Product')->updateProductWaitingForDeliveryQty($product->getId());
        mage::helper('purchase/Product')->updateProductDeliveryDate($product->getId());
    }

    /**
     * Add supplier and supplier reference columns in erp > products grid
     *
     * @param Varien_Event_Observer $observer
     */
    public function advancedstock_product_grid_preparecolumns(Varien_Event_Observer $observer) {
        $grid = $observer->getEvent()->getgrid();

        if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/products/productlist_columns/suppliers')) {
            $grid->addColumn('suppliers', array(
                'header' => Mage::helper('purchase')->__('Suppliers'),
                'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_ProductSuppliers',
                'filter' => 'Purchase/Widget_Column_Filter_ProductSupplier',
                'index' => 'entity_id'
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/products/productlist_columns/supplier_skus')) {
            $grid->addColumn('suppliers_sku', array(
                'header' => Mage::helper('purchase')->__('Suppliers Sku'),
                'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_ProductSuppliersSku',
                'filter' => 'Purchase/Widget_Column_Filter_ProductSupplierSku',
                'index' => 'entity_id'
            ));
        }
    }

    /**
     *  Add supplier & sku columns in mass stock editor
     * @param Varien_Event_Observer $observer
     */
    public function advancedstock_masstockeditor_grid_preparecolumns(Varien_Event_Observer $observer) {
        $grid = $observer->getEvent()->getgrid();

        $grid->addColumn('suppliers', array(
            'header' => Mage::helper('purchase')->__('Suppliers'),
            'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_ProductSuppliers',
            'filter' => 'Purchase/Widget_Column_Filter_ProductSupplier',
            'index' => 'product_id'
        ));

        $grid->addColumn('suppliers_sku', array(
            'header' => Mage::helper('purchase')->__('Suppliers Sku'),
            'renderer' => 'MDN_Purchase_Block_Widget_Column_Renderer_ProductSuppliersSku',
            'filter' => 'Purchase/Widget_Column_Filter_ProductSupplierSku',
            'index' => 'product_id'
        ));
    }


    /**
     * Add massaction "link to supplier" to ERP product grid
     *
     * @param Varien_Event_Observer $observer
     */
    public function advancedstock_product_grid_preparemassaction(Varien_Event_Observer $observer) {
        $grid = $observer->getEvent()->getgrid();
        $grid->setMassactionIdField('entity_id');
        $grid->getMassactionBlock()->setFormFieldName('ProductsList');

        $helper = Mage::helper('purchase');

        $grid->getMassactionBlock()->addItem('suppier', array(
            'label' => $helper->__('Link to a supplier'),
            'url' => mage::helper('adminhtml')->getUrl('Purchase/Products/MassAssociateToSupplier/supplier_id/', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'suppliers',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => $helper->__('Supplier'),
                    'values' => $this->getSupplierMenuForMassAction()
                )
            )
        ));
    }

    private function getSupplierMenuForMassAction(){
       $menu = array();

       //Supplier List
       $collection = Mage::getModel('Purchase/Supplier')
                ->getCollection()
                ->setOrder('sup_name', 'asc');

       foreach ($collection as $item) {
           $menu[$item->getsup_id()] = $item->getsup_name();
       }

       return  $menu;
    }

}

