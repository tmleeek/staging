<?php 
/**
 * Overload of default shipment controller to handle UPS package logic
 * @author Arnaud P <arnaud@boostmyshop.com>
 * @version 1.1.0
 */
require_once Mage::getModuleDir('controllers','Mage_Adminhtml') . DS .'Sales' . DS . 'Order' . DS . 'ShipmentController.php';

class MDN_Colissimo_Adminhtml_Sales_Order_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController
{
    /**
     * Rewrite of parent _initSHipment() to handle UPS package creation
     * @author Arnaud P <arnaud@boostmyshop.com>
     * @version 1.1.0
     * @param void
     * @return Mage_Sales_Model_Order_Shipment Shipment to work on
     */
    protected function _initShipment()
    {

        $this->_title($this->__('Sales'))->_title($this->__('Shipments'));

        $shipment = false;
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $orderId = $this->getRequest()->getParam('order_id');

        if ($shipmentId) {
            $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
        } elseif ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);

            /**
             * Check order existing
             */
            if (!$order->getId()) {
                $this->__getSession()->addError($this->__('The order no longer exists.'));
                return false;
            }
            /**
             * Check shipment is available to create separate from invoice
             */
            if ($order->getForcedDoShipmentWithInvoice()) {
                $this->_getSession()->addError($this->__('Cannot do shipment for the order separately from invoice.'));
                return false;
            }
            /**
             * Check shipment create availability
             */
            if (!$order->canShip()) {
                $this->_getSession()->addError($this->__('Cannot do shipment for the order.'));
                return false;
            }

            $savedQtys = $this->_getItemQtys();
            $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
            if ($colissimoInformations = $this->getRequest()->getPost('colissimo')) {

                foreach ($colissimoInformations as $key => $value) {
                    $shipment->{'setups_' . $key}($value);
                }


                if ( false === ($shipment = $this->_buildColissimoCustomPackage($shipment))) {
                    $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
                    return false;
                }


            } else {
                $tracks = $this->getRequest()->getPost('tracking');
                if ($tracks) {
                    foreach ($tracks as $data) {
                        if (empty($data['number'])) {
                            Mage::throwException($this->__('Tracking number cannot be empty.'));
                        }
                        $track = Mage::getModel('sales/order_shipment_track')
                            ->addData($data);
                        $shipment->addTrack($track);
                    }
                }    
            }
        }

        Mage::register('current_shipment', $shipment);

        return $shipment;
    }

    /**
     * Builds packages informations based on UPS informations
     * @author Arnaud P <arnaud@boostmyshop.com>
     * @version 1.1.0
     * @param Mage_Sales_Model_Order_Shipment $shipment Shipment to work on
     * @return Mage_Sales_Model_Order_Shipment Shipment filled with package information
     */
    protected function _buildColissimoCustomPackage($shipment)
    {
        $informations = $shipment->getups_package();

        /**
         * Setting base values for packages
         */
        $packageID = 1;
        foreach ($informations as $key => $packageInfo) {

            $return[$key] = array(
                'packageID'          => $packageID,
                'weight'                => $packageInfo['weight'],
                'deliverymode'          => $packageInfo['deliverymode'],
                'regatecode'            => $packageInfo['regatecode'],
                'shipmenttype'          => $packageInfo['shipmenttype'],
                'parceltype'            => $packageInfo['parceltype']
            );

            /**
             * Checking dimensions
             */

            if($packageInfo['parceltype'] == 1){
                if ($packageInfo['length'] != '' && $packageInfo['height'] != '' && $packageInfo['height'] != '') {
                    $return[$key]['length'] = $packageInfo['length'];
                    $return[$key]['width'] = $packageInfo['width'];
                    $return[$key]['height'] = $packageInfo['height'];
                }
            }else if($packageInfo['parceltype'] == 2){
                if($packageInfo['length'] != '' && $packageInfo['diam'] != ''){
                    $return[$key]['length'] = $packageInfo['length'];
                    $return[$key]['diam'] = $packageInfo['diam'];
                }
            }


            /**
             * Adding products informations to package
             */
            if (isset($packageInfo['products'])) {
                foreach ($packageInfo['products'] as $product) {
                    $product = json_decode($product);

                    /**
                     * Calculating package value and filling package products
                     */
                    $totalPrice = 0;
                    $qty = (int)$product->qty_ordered;
                    $totalPrice += ($qty * $product->price); 

                    $return[$key]['items'][$product->order_item_id] = array(
                        'qty'           => $qty,
                        'customs_value' => $qty * $product->price,
                        'price'         => $product->price,
                        'name'          => $product->name,
                        'weight'        => $product->weight,
                        'product_id'    => $product->product_id,
                        'order_item_id' => $product->order_item_id,
                    );
                }

                $return[$key]['customs_value'] = $totalPrice;
            }
        $packageID++;
        }

        $shipment->setPackages($return);

        return $shipment;
    }

//    protected function _checkGabarit($packageInfo){
//        //        - Les dimensions minimales du colis : 16 cm (Longueur) × 11cm (largeur) x 1 cm (hauteur)
//        //        - Les dimensions maximales du colis : L+l+h < ou = 150 cm et avec L < ou = 100 cm
//
//            if($packageInfo['parceltype'] == 1){
//                $totaldim = $packageInfo['length'] + $packageInfo['width'] + $packageInfo['height'];
//                if($packageInfo['length'] <= 100 && $packageInfo['width'] >= 11 && $packageInfo['height'] >= 1 && $packageInfo['length'] >= 16 && $totaldim  <= 150){
//                    return 0;
//                }else{
//                    if($totaldim > 200 || $packageInfo['width'] < 11 || $packageInfo['height'] < 1 || $packageInfo['length'] < 16){
//                        return false;
//                    }
//                    return 1;
//                }
//
//            }else if($packageInfo['parceltype'] == 2){
//                $totaldim = $packageInfo['length'] + $packageInfo['diam'] * 2;
//                if($packageInfo['length'] >= 16 && $packageInfo['diam'] >= 5 && $totaldim <= 150 && $totaldim >= 26){
//                    return 0;
//                }else{
//                    return false;
//                }
//            }
//
//    }

    /**
     * Rewrite of parent's newAction() to add package index to registry
     * @author Arnaud P <arnaud@boostmyshop.com>
     * @version 1.2.0
     * @param void
     * @return void
     */
    public function newAction()
    {
        Mage::getSingleton('adminhtml/session')->setpackage_index(1);

        parent::newAction();
    }
}