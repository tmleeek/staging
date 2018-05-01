<?php

/**
 * Sales orders controller
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once ('Mage/Shipping/controllers/TrackingController.php');
class Tatva_Shipping_TrackingController extends Mage_Shipping_TrackingController
{
    /**
     * Popup action
     * Shows tracking info if it's present, otherwise redirects to 404
     */
    public function popupAction()
    {   
        $this->loadLayout();
        $this->renderLayout();
    }
}
