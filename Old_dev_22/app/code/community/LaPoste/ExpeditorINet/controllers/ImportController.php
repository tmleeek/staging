<?php
/**
 * LaPoste_ExpeditorINet
 * 
 * @category    LaPoste
 * @package     LaPoste_ExpeditorINet
 * @copyright   Copyright (c) 2010 La Poste
 * @author 	    Smile (http://www.smile.fr) & Jibé
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LaPoste_ExpeditorINet_ImportController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Constructor
     */
    protected function _construct()
    {        
        $this->setUsedModuleName('LaPoste_ExpeditorINet');
    }

    /**
     * Main action : show import form
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/expeditorinet/import')
            ->_addContent($this->getLayout()->createBlock('expeditorinet/import_form'))
            ->renderLayout();
    }

    /**
     * Import Action
     */
    public function importAction()
    {
        if ($this->getRequest()->isPost() && !empty($_FILES['import_expeditor_inet_file']['tmp_name'])) {
            try {
                $trackingTitle = $_POST['import_expeditor_inet_tracking_title'];
                $this->_importExpeditorInetFile($_FILES['import_expeditor_inet_file']['tmp_name'], $trackingTitle);
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->addError($this->__('Invalid file upload attempt'));
            }
        }
        else {
            $this->_getSession()->addError($this->__('Invalid file upload attempt'));
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Importation logic
     * @param string $fileName
     * @param string $trackingTitle
     */
    protected function _importExpeditorInetFile($fileName, $trackingTitle)
    {
        /**
         * File handling
         **/
        ini_set('auto_detect_line_endings', true);
        $csvObject = new Varien_File_Csv();
        $csvData = $csvObject->getData($fileName);

        /**
         * File expected fields
         */
        $expectedCsvFields  = array(
            0   => $this->__('Order Id'),
            1   => $this->__('Tracking Number')
        );

        /**
         * Get configuration
         */
        $sendEmail = Mage::helper('expeditorinet')->getConfigurationSendEmail();
        $comment = Mage::helper('expeditorinet')->getConfigurationShippingComment();
        $includeComment = Mage::helper('expeditorinet')->getConfigurationIncludeComment();

        /* debug */
        //$this->_getSession()->addSuccess($this->__('%s - %s - %s - %s', $sendEmail, $comment, $includeComment, $trackingTitle));

        /**
         * $k is line number
         * $v is line content array
         */
        foreach ($csvData as $k => $v) {

            /**
             * End of file has more than one empty lines
             */
            if (count($v) <= 1 && !strlen($v[0])) {
                continue;
            }

            /**
             * Check that the number of fields is not lower than expected
             */
            if (count($v) < count($expectedCsvFields)) {
                $this->_getSession()->addError($this->__('Line %s format is invalid and has been ignored', $k));
                continue;
            }

            /**
             * Get fields content
             */
            $orderId = $v[0];
            $trackingNumber = $v[1];

            /* for debug */
            //$this->_getSession()->addSuccess($this->__('Lecture ligne %s: %s - %s', $k, $orderId, $trackingNumber));

            /**
             * Try to load the order
             */
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            if (!$order->getId()) {
                $this->_getSession()->addError($this->__('Order %s does not exist', $orderId));
                continue;
            }

            /**
             * Try to create a shipment
             */
            $shipmentId = $this->_createShipment($order, $trackingNumber, $trackingTitle, $sendEmail, $comment, $includeComment);
            
            if ($shipmentId != 0) {
                $this->_getSession()->addSuccess($this->__('Shipment %s created for order %s, with tracking number %s', $shipmentId, $orderId, $trackingNumber));
            }
             
        }//foreach

    }

    /**
     * Create new shipment for order
     * Inspired by Mage_Sales_Model_Order_Shipment_Api methods
     *
     * @param Mage_Sales_Model_Order $order (it should exist, no control is done into the method)
     * @param string $trackingNumber
     * @param string $trackingTitle
     * @param booleam $email
     * @param string $comment
     * @param boolean $includeComment
     * @return int : shipment real id if creation was ok, else 0
     */
    public function _createShipment($order, $trackingNumber, $trackingTitle, $email, $comment, $includeComment)
    {
        /**
         * Check shipment creation availability
         */
        if (!$order->canShip()) {
            $this->_getSession()->addError($this->__('Order %s can not be shipped or has already been shipped', $order->getRealOrderId()));
            return 0;
        }

        /**
         * Initialize the Mage_Sales_Model_Order_Shipment object
         */
        $convertor = Mage::getModel('sales/convert_order');
        $shipment = $convertor->toShipment($order);

        /**
         * Add the items to send
         */
        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->getQtyToShip()) {
                continue;
            }
            if ($orderItem->getIsVirtual()) {
                continue;
            }

            $item = $convertor->itemToShipmentItem($orderItem);
            $qty = $orderItem->getQtyToShip();
            $item->setQty($qty);

        	$shipment->addItem($item);
        }//foreach

        $shipment->register();

        /**
         * Tracking number instanciation
         */
		$carrierCode = Mage::helper('expeditorinet')->getConfigurationCarrierCode();
		if(!$carrierCode) $carrierCode = 'custom';
		        
        $track = Mage::getModel('sales/order_shipment_track')
                	->setNumber($trackingNumber)
                    ->setCarrierCode($carrierCode)
                    ->setTitle($trackingTitle);
        $shipment->addTrack($track);

        /**
         * Comment handling
         */
        $shipment->addComment($comment, $email && $includeComment);

        /**
         * Change order status to Processing
         */
        $shipment->getOrder()->setIsInProcess(true);

        /**
         * If e-mail, set as sent (must be done before shipment object saving)
         */
        if ($email) {
            $shipment->setEmailSent(true);
        }

        try {
        	/**
             * Save the created shipment and the updated order
             */
            $shipment->save();
            $shipment->getOrder()->save();

            /**
             * Email sending
             */
            $shipment->sendEmail($email, ($includeComment ? $comment : ''));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($this->__('Shipment creation error for Order %s : %s', $orderId, $e->getMessage()));
            return 0;
        }

        /**
         * Everything was ok : return Shipment real id
         */
        return $shipment->getIncrementId();
    }

}