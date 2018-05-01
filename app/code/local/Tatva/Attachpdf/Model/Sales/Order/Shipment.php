<?php
class Tatva_Attachpdf_Model_Sales_Order_Shipment extends Mage_Sales_Model_Order_Shipment
{
	public function sendEmail($notifyCustomer=true, $comment='')
    {
        if (!Mage::helper('sales')->canSendNewShipmentEmail($this->getOrder()->getStore()->getId())) {
            return $this;
        }

        $currentDesign = Mage::getDesign()->setAllGetOld(array(
            'package' => Mage::getStoreConfig('design/package/name', $this->getStoreId()),
            'store' => $this->getStoreId()
        ));

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $order  = $this->getOrder();
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $this->getStoreId());

        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }
        $paymentBlock   = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true);
        $paymentBlock->getMethod()->setStore($order->getStore()->getId());

        $mailTemplate = Mage::getModel('core/email_template');

        if ($order->getCustomerIsGuest()) {
            $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $order->getStoreId());
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $order->getStoreId());
            $customerName = $order->getCustomerName();
        }

        if ($notifyCustomer) {
            $sendTo[] = array(
                'name'  => $customerName,
                'email' => $order->getCustomerEmail()
            );
            if ($copyTo && $copyMethod == 'bcc') {
                foreach ($copyTo as $email) {
                    $mailTemplate->addBcc($email);
                }
            }

        }

        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $sendTo[] = array(
                    'name'  => null,
                    'email' => $email
                );
            }
        }
		if(Mage::getStoreConfig('sales_email/shipment/pdfattach',$this->getStoreId()))
		{
			$rootPdf = Mage::getStoreConfig ( 'sales/pdf/path_shipment', $order->getStoreId() );
		    $pdf = $rootPdf . $this->getPdfFile();
    	    $mailTemplate->addPdfAttachment($pdf);
		}

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$order->getStoreId()))
                ->sendTransactional(
                    $template,
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $order->getStoreId()),
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'order'       => $order,
                        'shipment'    => $this,
                        'comment'     => $comment,
                        'billing'     => $order->getBillingAddress(),
                        'payment_html'=> $paymentBlock->toHtml(),
                    )
                );
        }

        $translate->setTranslateInline(true);

        Mage::getDesign()->setAllGetOld($currentDesign);

        return $this;
    }

    /**
     * Sending email with Shipment update information
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function sendUpdateEmail($notifyCustomer = true, $comment='')
    {
        if (!Mage::helper('sales')->canSendShipmentCommentEmail($this->getOrder()->getStore()->getId())) {
            return $this;
        }

        $currentDesign = Mage::getDesign()->setAllGetOld(array(
            'package' => Mage::getStoreConfig('design/package/name', $this->getStoreId()),
        ));

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $order  = $this->getOrder();

        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $this->getStoreId());

        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        $mailTemplate = Mage::getModel('core/email_template');

        if ($order->getCustomerIsGuest()) {
            $template = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $order->getStoreId());
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $template = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $order->getStoreId());
            $customerName = $order->getCustomerName();
        }

        if ($notifyCustomer) {
            $sendTo[] = array(
                'name'  => $customerName,
                'email' => $order->getCustomerEmail()
            );
            if ($copyTo && $copyMethod == 'bcc') {
                foreach ($copyTo as $email) {
                    $mailTemplate->addBcc($email);
                }
            }

        }

        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $sendTo[] = array(
                    'name'  => null,
                    'email' => $email
                );
            }
        }
		if(Mage::getStoreConfig('sales_email/shipment/pdfattach',$this->getStoreId()))
		{
			$rootPdf = Mage::getStoreConfig ( 'sales/pdf/path_shipment', $order->getStoreId() );
		    $pdf = $rootPdf . $this->getPdfFile();
    	    $mailTemplate->addPdfAttachment($pdf);
		}

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$order->getStoreId()))
                ->sendTransactional(
                    $template,
                    Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $order->getStoreId()),
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'order'   => $order,
                        'billing' => $order->getBillingAddress(),
                        'shipment'=> $this,
                        'comment' => $comment
                    )
                );
        }

        $translate->setTranslateInline(true);

        Mage::getDesign()->setAllGetOld($currentDesign);

        return $this;
    }
}
?>