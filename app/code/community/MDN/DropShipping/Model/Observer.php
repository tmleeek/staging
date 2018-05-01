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
class MDN_DropShipping_Model_Observer extends Mage_Core_Model_Abstract {

    public $error = ""; // log error

    /**
     * Method called each 12 PM to log dropshipping supplier import file
     *
     */

    public function checkSupplierImportedFiles() {
        Mage::helper("DropShipping/SupplierStockImport")->importAllSuppliersStocks(); //ok
    }

    /**
     * Send dropship orders     
     */
    public function sendDropShipOrders() {

        $logs = array();
        
        try {

            if (Mage::getStoreConfig('dropshipping/auto_send_order/automaticaly_send_po') == 1) {

                $dropship = array();
                $dropshipmodes = array();
                $dropshipcomments = array();

                $dropShippingOrderIds = mage::helper('DropShipping')->getDropShippingOrderIds();
                // status of order must be processing (the order must be invoiced, so paid )
                $collection = mage::getModel('sales/order')
                        ->getCollection()
                        ->addFieldToFilter('entity_id', array('in' => $dropShippingOrderIds));

                $logs[] = $collection->getSize().' orders found';
                
                foreach ($collection as $order) {

                    $logs[] = 'Process order #'.$order->getIncrementId();
                    
                    foreach ($order->getItemsCollection() as $item) {

                        $logs[] = 'Process product '.$item->getSku();
                        
                        $remaining_qty = $item->getRemainToShipQty();
                        $productId = $item->getproduct_id();

                         $logs[] = '--> Remaining qty is : '.$remaining_qty;
                        
                        if ($remaining_qty > 0) {

                            $productStockManagement = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
                            if ($productStockManagement->getManageStock()) {

                                $logs[] = '--> Product manage stocks';
                         
                                $suppliers = mage::helper('DropShipping')->getDropshipSuppliers($productId, $remaining_qty);
                                $logs[] = '--> '.count($suppliers).' found';

                                // todo : gérer le cas ou ya pas de fournisseur : pour reproduire le bug, faire une commande avec 2 produits, un dropshippable, l'autre non, et lancer le code
                                //we always consider the first supplier, we'll implement additional rules later
                                if($suppliers && count($suppliers)>0){
                                  $supplier = $suppliers->getFirstItem();
                                  $dropship[$order->getId()][$item->getId()] = $supplier->getId();
                                  $dropshipmodes[$order->getId()][$item->getId()] = Mage::getStoreConfig('dropshipping/auto_send_order/automatic_dropship_mode');
                                  // dropship comment
                                  $dropshipcomments[$order->getId()][$item->getId()] = '';
                                }
                            }
                        }
                    }
                }

                $res = Mage::Helper('DropShipping')->processDropShip($dropship, $dropshipmodes, $dropshipcomments);

                //todo : ne pas sortir de la boucle sur erreur, mais "cumuler les msg d'erreur pour les retourner ensuite" (et s'assurer que le mail est bien envoyé
                if ($res['error'] === true)
                {
                    $this->error = $res['errorMessage'] . "\n\r";
                    $logs[] = $res['errorMessage'];
                }
            }
            else
            {
                $logs[] = 'dropshipping/auto_send_order/automaticaly_send_po is not enabled';
            }
        } catch (Exception $err) {

            $emailReport = Mage::getStoreConfig('dropshipping/drop_shippable_order/email_report');

            if ($emailReport != "") {
                $this->error .= $error->getMessage();
                mail($emailReport, 'Drop ship errors', $this->error);
                
            }
        }
        
        //log
        Mage::log(implode("\n", $logs), null, "dropshipping.log");
    }

}