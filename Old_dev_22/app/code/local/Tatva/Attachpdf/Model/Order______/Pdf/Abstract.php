<?php

/**
 * Sales Order PDF abstract model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Tatva_Attachpdf_Model_Sales_Order_Pdf_Abstract extends Mage_Sales_Model_Order_Pdf_Abstract
{
    public function insertOrder(&$page, $obj, $putOrderId = true)
    {
        if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

        /* @var $order Mage_Sales_Model_Order */
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.5));

        $page->drawRectangle(25, 790, 570, 755);

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $this->_setFontRegular($page);


        if ($putOrderId) {
            $page->drawText(Mage::helper('sales')->__('Order # ').$order->getRealOrderId(), 35, 770, 'UTF-8');
        }
        //$page->drawText(Mage::helper('sales')->__('Order Date: ') . date( 'D M j Y', strtotime( $order->getCreatedAt() ) ), 35, 760, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate($order->getCreatedAtStoreDate(), 'medium', false), 35, 760, 'UTF-8');

        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, 755, 275, 730);
        $page->drawRectangle(275, 755, 570, 730);

        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));

        /* Payment */
        $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->toPdf();
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key=>$value){
            if (strip_tags(trim($value))==''){
                unset($payment[$key]);
            }
        }
        reset($payment);

        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */

            if(Mage::getSingleton('core/session')->getData('set_relay_name') == '')
            {
                if($order->getShippingMethod() == 'colissimocityssimo_colissimocityssimo' || $order->getShippingMethod() == 'colissimolocalstore_colissimolocalstore' || $order->getShippingMethod() == 'colissimopostoffice_colissimopostoffice')
                {
                    $shippingAddress[] = '<span style="text-transform: uppercase">'.$order->getShippingAddress()->getFirstname().' '.$order->getShippingAddress()->getLastname().'</span><br />';
                    $shippingAddress[] = $order->getRelayName().'<br />';
                    $shippingAddress[] = $order->getRelayAddress().'<br />';
                    if($order->getRelayAddress2() != '')
                    {
                        $shippingAddress[] = $order->getRelayAddress2().'<br />';
                    }
                    if($order->getRelayAddress3() != '')
                    {
                        $shippingAddress[] = $order->getRelayAddress3().'<br />';
                    }

                    $shippingAddress[] = '<span style="text-transform: uppercase">'.$order->getRelayCity().', '.$order->getShippingAddress()->getRegion().', '.$order->getRelayPostalcode().'</span><br />';

                    $shippingAddress[] = '<span style="text-transform: uppercase">'.Mage::getModel('directory/country')->load($order->getShippingAddress()->getCountry())->getName().'</span><br />';

                    if($order->getShippingAddress()->getTelephone() != '')
                    {
                        $shippingAddress[] = "T: ".$order->getShippingAddress()->getTelephone().'<br />';
                    }
                    if($order->getShippingAddress()->getMobilephone() != '')
                    {
                        $shippingAddress[] = "M: ".$order->getShippingAddress()->getMobilephone().'<br />';
                    }
                    if($order->getShippingAddress()->getFax() != '')
                    {
                        $shippingAddress[] = "F: ".$order->getShippingAddress()->getFax().'<br />';
                    }
                }
                else
                {
                    $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
                }
            }
            else
            {
                if($order->getShippingMethod() == 'colissimocityssimo_colissimocityssimo' || $order->getShippingMethod() == 'colissimolocalstore_colissimolocalstore' || $order->getShippingMethod() == 'colissimopostoffice_colissimopostoffice')
                {
                    $shippingAddress[] = '<span style="text-transform: uppercase">'.$order->getShippingAddress()->getFirstname().' '.$order->getShippingAddress()->getLastname().'</span><br />';
                    $shippingAddress[] = Mage::getSingleton('core/session')->getData('set_relay_name').'<br />';
                    $shippingAddress[] = Mage::getSingleton('core/session')->getData('set_relay_address').'<br />';
                    if(Mage::getSingleton('core/session')->getData('set_relay_address2') != '')
                    {
                        $shippingAddress[] = Mage::getSingleton('core/session')->getData('set_relay_address2').'<br />';
                    }
                    if(Mage::getSingleton('core/session')->getData('set_relay_address3') != '')
                    {
                        $shippingAddress[] = Mage::getSingleton('core/session')->getData('set_relay_address3').'<br />';
                    }

                    $shippingAddress[] = '<span style="text-transform: uppercase">'.Mage::getSingleton('core/session')->getData('set_relay_city').', '.$order->getShippingAddress()->getRegion().', '.Mage::getSingleton('core/session')->getData('set_relay_postalcode').'</span><br />';

                    $shippingAddress[] = '<span style="text-transform: uppercase">'.Mage::getModel('directory/country')->load($order->getShippingAddress()->getCountry())->getName().'</span><br />';

                    if($order->getShippingAddress()->getTelephone() != '')
                    {
                        $shippingAddress[] = "T: ".$order->getShippingAddress()->getTelephone().'<br />';
                    }
                    if($order->getShippingAddress()->getMobilephone() != '')
                    {
                        $shippingAddress[] = "M: ".$order->getShippingAddress()->getMobilephone().'<br />';
                    }
                    if($order->getShippingAddress()->getFax() != '')
                    {
                        $shippingAddress[] = "F: ".$order->getShippingAddress()->getFax().'<br />';
                    }
                }
                else
                {
                    $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
                }
            }

            $shippingMethod  = $order->getShippingDescription();
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page);
        $page->drawText(Mage::helper('sales')->__('SOLD TO:'), 35, 740 , 'UTF-8');

        if (!$order->getIsVirtual()) {
            $page->drawText(Mage::helper('sales')->__('SHIP TO:'), 285, 740 , 'UTF-8');
        }
        else {
            $page->drawText(Mage::helper('sales')->__('Payment Method:'), 285, 740 , 'UTF-8');
        }

        if (!$order->getIsVirtual()) {
            $y = 730 - (max(count($billingAddress), count($shippingAddress)) * 10 + 5);
        }
        else {
            $y = 730 - (count($billingAddress) * 10 + 5);
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, 730, 570, $y);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page);
        $this->y = 720;

        foreach ($billingAddress as $value){
            if ($value!=='') {
                $page->drawText(strip_tags(ltrim($value)), 35, $this->y, 'UTF-8');
                $this->y -=10;
            }
        }

        if (!$order->getIsVirtual()) {
            $this->y = 720;
            foreach ($shippingAddress as $value){
                if ($value!=='') {
                    $page->drawText(strip_tags(ltrim($value)), 285, $this->y, 'UTF-8');
                    $this->y -=10;
                }

            }

            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 275, $this->y-25);
            $page->drawRectangle(275, $this->y, 570, $this->y-25);

            $this->y -=15;
            $this->_setFontBold($page);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText(Mage::helper('sales')->__('Payment Method'), 35, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Shipping Method:'), 285, $this->y , 'UTF-8');

            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments   = $this->y - 15;
        }
        else {
            $yPayments   = 720;
            $paymentLeft = 285;
        }

        foreach ($payment as $value){
            if (trim($value)!=='') {
                $page->drawText(strip_tags(trim($value)), $paymentLeft, $yPayments, 'UTF-8');
                $yPayments -=10;
            }
        }

        if (!$order->getIsVirtual()) {
            $this->y -=15;

            $page->drawText($shippingMethod, 285, $this->y, 'UTF-8');

            $yShipments = $this->y;


            $totalShippingChargesText = "(" . Mage::helper('sales')->__('Total Shipping Charges') . " " . $order->formatPriceTxt($order->getShippingAmount()) . ")";

            $page->drawText($totalShippingChargesText, 285, $yShipments-7, 'UTF-8');
            $yShipments -=10;

            $tracks = array();
            if ($shipment) {
                $tracks = $shipment->getAllTracks();
            }
            if (count($tracks)) {
                $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(380, $yShipments, 380, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page);
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(Mage::helper('sales')->__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Number'), 385, $yShipments - 7, 'UTF-8');

                $yShipments -=17;
                $this->_setFontRegular($page, 6);
                foreach ($tracks as $track) {

                    $CarrierCode = $track->getCarrierCode();
                    if ($CarrierCode!='custom')
                    {
                        $carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($CarrierCode);
                        $carrierTitle = $carrier->getConfigData('title');
                    }
                    else
                    {
                        $carrierTitle = Mage::helper('sales')->__('Custom Value');
                    }

                    //$truncatedCarrierTitle = substr($carrierTitle, 0, 35) . (strlen($carrierTitle) > 35 ? '...' : '');
                    $maxTitleLen = 45;
                    $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
                    $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
                    //$page->drawText($truncatedCarrierTitle, 285, $yShipments , 'UTF-8');
                    $page->drawText($truncatedTitle, 300, $yShipments , 'UTF-8');
                    $page->drawText($track->getNumber(), 395, $yShipments , 'UTF-8');
                    $yShipments -=7;
                }
            } else {
                $yShipments -= 7;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25, $this->y + 15, 25, $currentY);
            $page->drawLine(25, $currentY, 570, $currentY);
            $page->drawLine(570, $currentY, 570, $this->y + 15);

            $this->y = $currentY;
            $this->y -= 15;
        }
    }
}
