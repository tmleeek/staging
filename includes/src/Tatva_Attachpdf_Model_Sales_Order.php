<?php
class Tatva_Attachpdf_Model_Sales_Order extends Mage_Sales_Model_Order
{


   protected $points_earned = null;
   protected $points_spent  = null;
  

    
	public function sendNewOrderEmail()
    {
        $storeId = $this->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);


		// Start store emulation process

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            throw $exception;
        }


        // Retrieve corresponding email template id and customer name
        if ($this->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }


		$mailTemplate = Mage::getModel('core/email_template');

		$sendTo[] = array(
                'name'  => $customerName,
                'email' => $this->getCustomerEmail()
            );

        if ($copyTo && $copyMethod == 'bcc') {
            // Add bcc to customer email
            foreach ($copyTo as $email) {

				$mailTemplate->addBcc($email);
            }
        }

        // Email copies are sent as separated emails if their copy method is 'copy'
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
				$sendTo[] = array(
                    'name'  => null,
                    'email' => $email
                );
            }
        }

		if(Mage::getStoreConfig('sales_email/order/pdfattach',$this->getStoreId()))
		{
			$rootPdf = Mage::getStoreConfig ( 'sales/pdf/path_order', $this->getStoreId() );
		    $pdf = $rootPdf . $this->getPdfFile();
            //echo $pdf; exit;
            if($this->getPdfFile())
            {
    	      $mailTemplate->addPdfAttachment($pdf);
            }
		}

		foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$this->getStoreId()))
                ->sendTransactional(
                    $templateId,
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $this->getStoreId()),
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'order'       => $this,
                        'billing'     => $this->getBillingAddress(),
                        'payment_html'=> $paymentBlockHtml,
                    )
                );
        }

		$translate->setTranslateInline(true);

        Mage::getDesign()->setAllGetOld($currentDesign);

		$this->_getResource()->saveAttribute($this, 'email_sent');

        Mage::dispatchEvent('tatvaorder_send_new_order_email', array('order' => $this));

        return $this;
    }

    public function addAttributeUpdate($code, $value)
    {
        $oldValue = $this->getData($code);

        $this->setData($code, $value);
        $this->getResource()->saveAttribute($this, $code);

        $this->setData($code, $oldValue);
    }

    /**
     * Send email with order update information
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Mage_Sales_Model_Order
     */
    public function sendOrderUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $storeId = $this->getStore()->getId();

        if (!Mage::helper('sales')->canSendOrderCommentEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $storeId);

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }
		$mailTemplate = Mage::getModel('core/email_template');

        // Retrieve corresponding email template id and customer name
        if ($this->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }



		$sendTo[] = array(
                'name'  => $customerName,
                'email' => $this->getCustomerEmail()
            );

        $mailer = Mage::getModel('core/email_template_mailer');
        if ($notifyCustomer) {
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $mailTemplate->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
            	$sendTo[] = array(
                    'name'  => null,
                    'email' => $email
                );
            }
        }
		if(Mage::getStoreConfig('sales_email/order/pdfattach',$this->getStoreId()))
		{
		    $rootPdf = Mage::getStoreConfig ( 'sales/pdf/path_order', $this->getStoreId() );
		    $pdf = $rootPdf . $this->getPdfFile();
    	    $mailTemplate->addPdfAttachment($pdf);
		}

       foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$this->getStoreId()))
                ->sendTransactional(
                    $templateId,
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $this->getStoreId()),
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'order'       => $this,
                        'billing'     => $this->getBillingAddress(),
                        'payment_html'=> $paymentBlockHtml,
                        'comment'=> $comment
                    )
                );
        }

		$translate->setTranslateInline(true);

        Mage::getDesign()->setAllGetOld($currentDesign);

		$this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }
	
	





    
}