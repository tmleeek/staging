<?php



class Tatva_Attachpdf_Model_Sales_Order_Invoice extends Mage_Sales_Model_Order_Invoice

{
   const XML_PATH_EMAIL_MANDATADMINISTRATIF_TEMPLATE  = 'sales_email/invoice/mandatadministratif_template';
   const XML_PATH_EMAIL_BlOCK_ORDER_CIC_TEMPLATE  = 'sales_email/invoice/cybermut_template';
	public function sendEmail($notifyCustomer=true, $comment='')

    {
    	
	    $order  = $this->getOrder();
		if ($order->getPayment()->getMethod() != 'mandatadministratif') { 
	        if (!Mage::helper('sales')->canSendNewInvoiceEmail($this->getOrder()->getStore()->getId())) {

	            return $this;

	        }
		}



        $currentDesign = Mage::getDesign()->setAllGetOld(array(

            'package' => Mage::getStoreConfig('design/package/name', $this->getStoreId()),

            'store'   => $this->getStoreId()

        ));



        $translate = Mage::getSingleton('core/translate');

        /* @var $translate Mage_Core_Model_Translate */

        $translate->setTranslateInline(false);



        

        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);

        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $this->getStoreId());


		if ($order->getPayment()->getMethod() != 'mandatadministratif') { 
	        if (!$notifyCustomer && !$copyTo) {

	            return $this;

	        }
		}	
		

        $paymentBlock   = Mage::helper('payment')->getInfoBlock($order->getPayment())

            ->setIsSecureMode(true);

        $paymentBlock->getMethod()->setStore($order->getStore()->getId());



        $mailTemplate = Mage::getModel('core/email_template');
        $rootPdf = Mage::getStoreConfig ( 'sales/pdf/path_invoice', $order->getStoreId());
		

        if ($order->getCustomerIsGuest()) {

            $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $order->getStoreId());

            $customerName = $order->getBillingAddress()->getName();

        }
        else if ($order->getPayment()->getMethod() == 'mandatadministratif') {  
            $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_MANDATADMINISTRATIF_TEMPLATE, $order->getStoreId());
            $customerName = $order->getBillingAddress()->getName();
            $pdf_mandat = $rootPdf . 'PREUVE_DE_PAIEMENT_PAR_MANDAT_ADMINISTRATIF.pdf';
            $mailTemplate->addPdfAttachment($pdf_mandat);
        }
        else {

            $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $order->getStoreId());

            $customerName = $order->getCustomerName();

        }

		

        if ($notifyCustomer || $order->getPayment()->getMethod() == 'mandatadministratif') { 

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


	    if ($order->getPayment()->getMethod() != 'mandatadministratif') { 	
	        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {

	            foreach ($copyTo as $email) {

	                $sendTo[] = array(

	                    'name'  => null,

	                    'email' => $email

	                );

	            }

	        }
		}



		//if(Mage::getStoreConfig('sales_email/invoice/attachpdf',$this->getStoreId())){

            //Create Pdf and attach to email - play nicely with PDF Customiser

		if(Mage::getStoreConfig('sales_email/invoice/pdfattach',$this->getStoreId()))

		{

            $rootPdf = Mage::getStoreConfig ( 'sales/pdf/path_invoice', $order->getStoreId() );

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

                        'invoice'     => $this,

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

     * Sending email with invoice update information

     *

     * @return Mage_Sales_Model_Order_Invoice

     */

    public function sendUpdateEmail($notifyCustomer=true, $comment='')

    {

        if (!Mage::helper('sales')->canSendInvoiceCommentEmail($this->getOrder()->getStore()->getId())) {

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



        $sendTo = array();



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



		if(Mage::getStoreConfig('sales_email/invoice/pdfattach',$this->getStoreId()))

		{

            $rootPdf = Mage::getStoreConfig ( 'sales/pdf/path_invoice', $order->getStoreId() );

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

                        'order'  => $order,

                        'billing'=> $order->getBillingAddress(),

                        'invoice'=> $this,

                        'comment'=> $comment

                    )

                );

        }



        $translate->setTranslateInline(true);



        Mage::getDesign()->setAllGetOld($currentDesign);



        return $this;

    }
    public function saveInvoiceAfterOrder($order)
    {

            try {
              if(!$order->canInvoice())
              {
              	Mage::getModel('core/session')->addError(Mage::helper('sales')->__('Cannot create an invoice.'));
              }

              $invoiceId = Mage::getModel('sales/order_invoice_api')->create($order->getIncrementId(), array());
              if($invoiceId)
              {
			   Mage::getModel('core/session')->addSuccess(Mage::helper('sales')->__('Invoice successfully saved.'));
              }

              }
              catch (Mage_Core_Exception $e) {
              }
    }
	
	public function saveInvoiceAfterOrder_CIC($order)
    {

            try {
              if(!$order->canInvoice())
              {
              	Mage::getModel('core/session')->addError(Mage::helper('sales')->__('Cannot create an invoice.'));
              }

              $invoiceId = Mage::getModel('sales/order_invoice_api')->create_cic($order->getIncrementId(), array(),'paiement accepte');
              if($invoiceId)
              {
			   Mage::getModel('core/session')->addSuccess(Mage::helper('sales')->__('Invoice successfully saved.'));
              }

              }
              catch (Mage_Core_Exception $e) {
              }
    }
	
	public function sendblockordercicEmail()
    {
        $currentDesign = Mage::getDesign()->setAllGetOld(array(
            'package' => Mage::getStoreConfig('design/package/name', 0),
            'store'   => $this->getStoreId()
        ));

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $order  = $this->getOrder();

        if(!$order->getCustomerEmail()){
        	return $this;
        }


        $mailTemplate = Mage::getModel('core/email_template');



             $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_BlOCK_ORDER_CIC_TEMPLATE, $order->getStoreId());
            $customerName = $order->getCustomerName();

            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$order->getStoreId()))
                ->sendTransactional(
                    $template,
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $order->getStoreId()),
                    $order->getCustomerEmail(),
                    $order->getCustomerName(),
                       array(
                        'order'       => $order

                    )
                );


        $translate->setTranslateInline(true);

        Mage::getDesign()->setAllGetOld($currentDesign);

        return $this;
    }

}