<?php

class Tatva_Attachpdf_Model_Sales_Order_Pdf_Shipment extends Tatva_Attachpdf_Model_Sales_Order_Pdf_Abstract
{
	public function getPdf($shipments = array())
    {


    if (Mage::getStoreConfig('colissimo/account_shipment/pdf_overloading') == '1') {
            return Mage::getModel('colissimo/Pdf_PackingSlip')
                ->getPdfOverload($shipments);
        }


        
		$this->_beforeGetPdf ();
		$this->_initRenderer ( 'shipment' );

		$pdf = new Zend_Pdf ( );
		$this->_setPdf ( $pdf );
		$style = new Zend_Pdf_Style ( );
		$this->_setFontBold ( $style, 10 );
		foreach ( $shipments as $shipment ) {
			if ($shipment->getStoreId ()) {
				Mage::app ()->getLocale ()->emulate ( $shipment->getStoreId () );
			}
			$page = $pdf->newPage ( Zend_Pdf_Page::SIZE_A4 );
			$pdf->pages [] = $page;

			$order = $shipment->getOrder ();

			$this->shipment = $shipment;
			$this->order = $order;

			// initialisation axe Y
			$this->y = $page->getHeight ();

			/* Add image */
			$this->insertLogo ( $page, $shipment->getStore () );

			/* Add address */
			$this->insertAddress ( $page, $shipment->getStore () );

			/* Entête livraison */
			$this->insertShipmentHeader ( $page );

			if(strlen($this->order->getShippingDescription ()) > 50)
            {
    		    $this->y = $this->y - 22;
            }
            else
            {
                $this->y = $this->y - 8;
            }

			/* Adresse de livraison */
			if (! $this->order->getIsVirtual ()) {
				$this->insertShippingAddress ( $page, $shipment->getStore ());
			}

			/* Adresse de facturation */
			$this->insertBillingAddress ( $page, $shipment->getStore () );

			$this->y = $this->y - 140;

			// Liste des items
			$this->insertItems ( $page );

			// gérer le cas de la page suivante
			if ($this->y < 300) {
				$page = $this->newPage ( array (
						'table_header' => true,
						'table_bottom' => true ) );
				$this->y = $this->y - 12;
			}

			// Liste des items restant à livrer
			$this->insertReliquats ( $page );

			// gérer le cas de la page suivante
			if ($this->y < 150) {
				$page = $this->newPage ( array (
						'table_header' => true,
						'table_bottom' => true ) );
				$this->y = $this->y - 12;
			}

			// Conditions de règlement
			$this->insertConditions ($page, $order);

		}

		$this->_afterGetPdf ();

		if ($shipment->getStoreId ()) {
			Mage::app ()->getLocale ()->revert ();
		}
		return $pdf;
	}
	
	public function getPdf_Mondial($shipments = array()) {

		$this->_beforeGetPdf ();
		$this->_initRenderer ( 'shipment' );

		$pdf = new Zend_Pdf ( );
		$this->_setPdf ( $pdf );
		$style = new Zend_Pdf_Style ( );
		$this->_setFontBold ( $style, 10 );
        
		foreach ( $shipments as $shipment ) {
			if ($shipment->getStoreId ()) {
				Mage::app ()->getLocale ()->emulate ( $shipment->getStoreId () );
			}


           $path = Mage::getStoreConfig('sales/pdf/path_shipment').'mondiallabel/';

           $urlEtiquette = Mage::getModel('pointsrelais/carrier_pointsrelais')->getEtiquetteUrl($shipment->getId ());

            $url  = 'http://www.mondialrelay.fr' . $urlEtiquette;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $data = curl_exec($ch);

            curl_close($ch);

            file_put_contents($path.$shipment->getId ().'.pdf', $data);

            //595:842;
              /*$arr_pdf = explode(":",Zend_Pdf_Page::SIZE_A4);

              $w = round($arr_pdf[0]/29.7);

              $h = round($arr_pdf[1]/21);*/

            $pdf = new Zend_Pdf ( );
		    $this->_setPdf ( $pdf );
		    $style = new Zend_Pdf_Style ( );
	        $this->_setFontBold ( $style, 10 );

            $pdf->pages = "";
			$page = $pdf->newPage (Zend_Pdf_Page::SIZE_A4);

			$pdf->pages[] = $page;


            // $page->rotate(0, 0, M_PI_2/2);
			$order = $shipment->getOrder ();

			$this->shipment = $shipment;
			$this->order = $order;

			// initialisation axe Y
			$this->y = $page->getHeight ();

			/* Add image */
				/* Add image */
			$this->insertLogo ( $page, $shipment->getStore () );

			/* Add address */
			$this->insertAddress ( $page, $shipment->getStore () );

			/* Entête livraison */
			$this->insertShipmentHeader ( $page );

			if(strlen($this->order->getShippingDescription ()) > 50)
            {
    		    $this->y = $this->y - 22;
            }
            else
            {
                $this->y = $this->y - 8;
            }

			/* Adresse de livraison */
			if (! $this->order->getIsVirtual ()) {
				$this->insertShippingAddress ( $page, $shipment->getStore ());
			}

			/* Adresse de facturation */
			$this->insertBillingAddress ( $page, $shipment->getStore () );

			$this->y = $this->y - 140;

			// Liste des items
			$this->insertItems ( $page );

			// gérer le cas de la page suivante
			if ($this->y < 300) {
				$page = $this->newPage ( array (
						'table_header' => true,
						'table_bottom' => true ) );
				$this->y = $this->y - 12;
			}

			// Liste des items restant à livrer
			$this->insertReliquats ( $page );

			// gérer le cas de la page suivante
			if ($this->y < 150) {
				$page = $this->newPage ( array (
						'table_header' => true,
						'table_bottom' => true ) );
				$this->y = $this->y - 12;
			}

			// Conditions de règlement
			$this->insertConditions ($page, $order);



            /*$pdf1 = Zend_Pdf::load('d:/sticker_1.pdf');

            $extractor = new Zend_Pdf_Resource_Extractor();
            $pdfFirstPageFirst = $extractor->clonePage($pdf1->page[0]);
			$pdf->pages[] = $pdfFirstPageFirst;*/


            /*$pdfNew = new Zend_Pdf();
            $pdf1 = Zend_Pdf::load('d:\packingslip.pdf');
            $pdf2  = Zend_Pdf::load('d:\A4.pdf');

            $extractor = new Zend_Pdf_Resource_Extractor();

            //Create clone of first page of first pdf document.
            $pdfFirstPageFirst = $extractor->clonePage($pdf1->page[0]);
            //Add first page of first pdf to new pdf document.
            $pdfNew->pages[] = $pdfFirstPageFirst;

            //Second Pdf
            //Create clone of first page of second pdf document.
            $pdfSecondPageFirst = $extractor->clonePage($pdf2->page[0]);
            //Add first page of second pdf to new pdf document.
            $pdfNew->pages[] = $pdfSecondPageFirst;

            $pdfNew->save('d:/merge.pdf');*/


            /*$pdfTemp = Zend_Pdf::load('d:/sticker_1.pdf');
            $extractor = new Zend_Pdf_Resource_Extractor();
            foreach($pdfTemp->pages as $page1){
                $pdfExtract = $extractor->clonePage($page1);
                $pdf->pages [] = $pdfExtract;
            }*/

            $pdf->save($path.'packingslip_'.$shipment->getId().'.pdf');

            //$pdf_merge->addPDF($path.$shipment->getId().'.pdf')->addPDF($path.'packingslip_'.$shipment->getId().'.pdf');

            $pdfDocs[] = $path.$shipment->getId().'.pdf';
			$pdfDocs[] = $path.'packingslip_'.$shipment->getId().'.pdf';  
		}

		$this->_afterGetPdf ();
          
		if ($shipment->getStoreId ()) {
			Mage::app ()->getLocale ()->revert ();
		}
		
		
		$pdfNew = new Zend_Pdf();
		foreach($pdfDocs as $file){
		$pdf = Zend_Pdf::load($file);
		$extractor = new Zend_Pdf_Resource_Extractor();
						foreach($pdf->pages as $page){
						$pdfExtract = $extractor->clonePage($page);
						$pdfNew->pages[] = $pdfExtract;
						}
		}

		$mergePdf = "mergefile.pdf";
		$pdfNew ->save($path.$mergePdf);
        //$pdf_merge->merge('download', 'packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf');

        $fullPath = $path.'mergefile.pdf';

		if ($fd = fopen ($fullPath, "r")) {
		    $fsize = filesize($fullPath);
		    $path_parts = pathinfo($fullPath);
		    $ext = strtolower($path_parts["extension"]);
		    switch ($ext) {
		        case "pdf":
		        header("Content-type: application/pdf"); // add here more headers for diff. extensions
		        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
		        break;
		        default;
		        header("Content-type: application/octet-stream");
		        header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
		    }
		    header("Content-length: $fsize");
		    header("Cache-control: private"); //use this to open files directly
		    while(!feof($fd)) {
		        $buffer = fread($fd, 2048);
		        echo $buffer;
		    }
		}
		fclose ($fd);
		exit;


		//return $pdf;
	}
	public function getPdf_Gls($shipments = array()) {

		$this->_beforeGetPdf ();
		$this->_initRenderer ( 'shipment' );

		$pdf = new Zend_Pdf ( );
		$this->_setPdf ( $pdf );
		$style = new Zend_Pdf_Style ( );
		$this->_setFontBold ( $style, 10 );
        
		foreach ( $shipments as $shipment ) {
			if ($shipment->getStoreId ()) {
				Mage::app ()->getLocale ()->emulate ( $shipment->getStoreId () );
			}

            $shipment_data    = Mage::getModel('sales/order_shipment')->load($shipment->getId());
			
			//echo '<pre>';print_r($shipment_data->getShippingAddress()->getMethod());exit;
            $model = Mage::getModel('glsbox/shipment')->getCollection()->addFieldToFilter('shipment_id', $shipment->getId())->getLastItem();
            $labelId = $model->getId();
			$paketnummer = Mage::helper('glsbox')->getTagValue($model->getGlsMessage(), '400');
            
            
           
	        if (isset($labelId)) {
	            $tagdata = $this->_initGlsLabel($labelId);
	            if ($tagdata == false) {
	            	$this->_getSession()->addError('GLS label not available');
	            	$this->_redirect('*/*/');}
	            
	                $pdf = Mage::getModel('glsbox/pdf_label')->createLabel($tagdata);
					$path = Mage::helper('glsbox')->getFileDestination().'GLS_'.$paketnummer.'.pdf';
	           
	            $this->_saveToDiskIfPossible($pdf,$paketnummer);
	            
	        } else {
	        	$this->_getSession()->addError('GLS label not available');
	            $this->_redirect('*/*/');
	        }
		
		 
           

           

            //595:842;
              /*$arr_pdf = explode(":",Zend_Pdf_Page::SIZE_A4);

              $w = round($arr_pdf[0]/29.7);

              $h = round($arr_pdf[1]/21);*/

            $pdf = new Zend_Pdf ( );
		    $this->_setPdf ( $pdf );
		    $style = new Zend_Pdf_Style ( );
	        $this->_setFontBold ( $style, 10 );

            $pdf->pages = "";
			$page = $pdf->newPage (Zend_Pdf_Page::SIZE_A4);

			$pdf->pages[] = $page;


            // $page->rotate(0, 0, M_PI_2/2);
			$order = $shipment->getOrder ();

			$this->shipment = $shipment;
			$order = $shipment->getOrder ();

			$this->shipment = $shipment;
			$this->order = $order;

			// initialisation axe Y
			$this->y = $page->getHeight ();

			/* Add image */
			$this->insertLogo ( $page, $shipment->getStore () );

			/* Add address */
			$this->insertAddress ( $page, $shipment->getStore () );

			/* Entête livraison */
			$this->insertShipmentHeader ( $page );

			if(strlen($this->order->getShippingDescription ()) > 50)
            {
    		    $this->y = $this->y - 22;
            }
            else
            {
                $this->y = $this->y - 8;
            }

			/* Adresse de livraison */
			if (! $this->order->getIsVirtual ()) {
				$this->insertShippingAddress ( $page, $shipment->getStore ());
			}

			/* Adresse de facturation */
			$this->insertBillingAddress ( $page, $shipment->getStore () );

			$this->y = $this->y - 140;

			// Liste des items
			$this->insertItems ( $page );

			// gérer le cas de la page suivante
			if ($this->y < 300) {
				$page = $this->newPage(array (
						'table_header' => false,
						'table_bottom' => false ));
			   //$pdf->pages[] = $pdf->newPage (Zend_Pdf_Page::SIZE_A4);
				$page = $this->newPage ( array (
						'table_header' => true,
						'table_bottom' => true ) );
				$this->y = $this->y - 12;
			}

			// Liste des items restant à livrer
			$this->insertReliquats ( $page );

			// gérer le cas de la page suivante
			if ($this->y < 150) {
				$page = $this->newPage ( array (
						'table_header' => true,
						'table_bottom' => true ) );
				$this->y = $this->y - 12;
			}

			// Conditions de règlement
			$this->insertConditions ($page, $order);



            /*$pdf1 = Zend_Pdf::load('d:/sticker_1.pdf');

            $extractor = new Zend_Pdf_Resource_Extractor();
            $pdfFirstPageFirst = $extractor->clonePage($pdf1->page[0]);
			$pdf->pages[] = $pdfFirstPageFirst;*/


            /*$pdfNew = new Zend_Pdf();
            $pdf1 = Zend_Pdf::load('d:\packingslip.pdf');
            $pdf2  = Zend_Pdf::load('d:\A4.pdf');

            $extractor = new Zend_Pdf_Resource_Extractor();

            //Create clone of first page of first pdf document.
            $pdfFirstPageFirst = $extractor->clonePage($pdf1->page[0]);
            //Add first page of first pdf to new pdf document.
            $pdfNew->pages[] = $pdfFirstPageFirst;

            //Second Pdf
            //Create clone of first page of second pdf document.
            $pdfSecondPageFirst = $extractor->clonePage($pdf2->page[0]);
            //Add first page of second pdf to new pdf document.
            $pdfNew->pages[] = $pdfSecondPageFirst;

            $pdfNew->save('d:/merge.pdf');*/


            /*$pdfTemp = Zend_Pdf::load('d:/sticker_1.pdf');
            $extractor = new Zend_Pdf_Resource_Extractor();
            foreach($pdfTemp->pages as $page1){
                $pdfExtract = $extractor->clonePage($page1);
                $pdf->pages [] = $pdfExtract;
            }*/

            $pdf->save(Mage::helper('glsbox')->getFileDestination().'packingslip_'.$shipment->getId().'.pdf');

            //$pdf_merge->addPDF($path)->addPDF(Mage::helper('glsbox')->getFileDestination().'packingslip_'.$shipment->getId().'.pdf');  
			             
			$pdfDocs[] = $path;
			$pdfDocs[] = Mage::helper('glsbox')->getFileDestination().'packingslip_'.$shipment->getId().'.pdf';  
		}

		$this->_afterGetPdf ();
          
		if ($shipment->getStoreId ()) {
			Mage::app ()->getLocale ()->revert ();
		}
		
		
		$pdfNew = new Zend_Pdf();
		foreach($pdfDocs as $file){
		$pdf = Zend_Pdf::load($file);
		$extractor = new Zend_Pdf_Resource_Extractor();
						foreach($pdf->pages as $page){
						$pdfExtract = $extractor->clonePage($page);
						$pdfNew->pages[] = $pdfExtract;
						}
		}

		$mergePdf = "mergefile.pdf";
		$pdfNew ->save($path.$mergePdf);
        //$pdf_merge->merge('download', 'packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf');

        $fullPath = $path.'mergefile.pdf';

		if ($fd = fopen ($fullPath, "r")) {
		    $fsize = filesize($fullPath);
		    $path_parts = pathinfo($fullPath);
		    $ext = strtolower($path_parts["extension"]);
		    switch ($ext) {
		        case "pdf":
		        header("Content-type: application/pdf"); // add here more headers for diff. extensions
		        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
		        break;
		        default;
		        header("Content-type: application/octet-stream");
		        header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
		    }
		    header("Content-length: $fsize");
		    header("Cache-control: private"); //use this to open files directly
		    while(!feof($fd)) {
		        $buffer = fread($fd, 2048);
		        echo $buffer;
		    }
		}
		fclose ($fd);
		exit;

		//return $pdf;
	}
	protected function _initGlsLabel($id){
        $model = Mage::getModel('glsbox/unibox_parser');
        $data = $model->preparePrint($id);
        return $data;
    }
	
	protected function _saveToDiskIfPossible($pdf,$name)
    {
        if(Mage::helper('glsbox')->getSaveToDiskEnabled()){
            $path = Mage::helper('glsbox')->getFileDestination();

            if ( Mage::getStoreConfig('glsbox/labels/file_name_prefix', Mage::app()->getStore()->getId()) != "" ) {
                $prefix = Mage::getStoreConfig('glsbox/labels/file_name_prefix', Mage::app()->getStore()->getId());
            }else{
                $prefix = '';
            }
            $name = str_replace(' ', '', $prefix . $name . '.pdf');

            if(!is_dir(str_replace('//','/',$path))){
                mkdir(str_replace('//','/',$path), 0777, true);
                // .htaccess erstellen
                $htaccess = 'Order deny,allow
Deny from all';
                $handle = fopen($path.'.htaccess', 'w');
                fwrite($handle, $htaccess);
                fclose($handle);

                $pdf->save($path.$name);
            } else {
                $pdf->save($path.$name);
            }
        }
    }

    protected function insertHeaderReliquat(&$page) {
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setFillColor ( new Zend_Pdf_Color_Html ( '#C30245' ) );
		$page->setLineWidth ( 0.5 );
		$x1 = self::COORD_X_LEFT_MARGIN;
		$y1 = $this->y - 16;
		$x2 = self::COORD_X_RIGHT_MARGIN;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2 );
		// Titre
		$this->_setTitleFont ( $page );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'REMAINING' ), 34, $this->y - 12, 'UTF-8' );
		$this->y = $this->y - 16;
	}

	protected function insertReliquats(&$page) {

		// Calcul de ce qu'il reste en reliquat
		$tbRemainsQty = array ();
		$tbRemainsItem = array ();
		foreach ( $this->order->getAllItems () as $orderItem ) {
			if ($orderItem->getParentItem () || $orderItem->getIsVirtual ()) {
				continue;
			}
			$orderedQty = $orderItem->getQtyOrdered ();
			$shippedQty = $orderItem->getQtyShipped ();
			$remainedQty = $orderedQty - $shippedQty;
			$tbRemainsQty [$orderItem->getId ()."r"] = $remainedQty;
			$tbRemainsItem [$orderItem->getId ()."r"] = $orderItem;
		}

		$remaining = false;
		foreach ($tbRemainsQty as $qty) {
			if ($qty > 0) {
				$remaining = true;
			}
		}

		if ($remaining) {
			// Entête du tableau
			$this->insertHeaderReliquat ( $page );

			// Ajout des reliquats
			$heightLine = 27;
			foreach ( $tbRemainsQty as $key => $qty ) {

				if ($qty == 0) {
					continue;
				}

				if ($this->y < $this->minY) {
					$page = $this->newPage ( array (
							'table_header' => true,
							'remain_header' => true,
							'table_bottom' => true ) );
				}


				// Ajout d'une ligne d'item
				$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
				$page->setLineWidth ( 0.5 );
				$page->setLineDashingPattern ( array (
						1,
						1 ) );
				$x1 = self::COORD_X_LEFT_MARGIN;
				$y1 = $this->y - $heightLine;
				$x2 = self::COORD_X_RIGHT_MARGIN;
				$y2 = $this->y;
				$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );

				// Ajout de l'item
				$this->addRemainedItem ( $page, $tbRemainsItem [$key], $tbRemainsQty [$key] );

				// Mise à jour de l'axe Y
				$this->y = $this->y - $heightLine;

			}
			$page->setLineDashingPattern ( Zend_Pdf_Page::LINE_DASHING_SOLID );
			$this->y = $this->y - 10;
		}
	}

	protected function insertShipmentHeader(&$page) {
		// Récupération du client
		$customer = Mage::getModel ( 'customer/customer' )->load ( $this->order->getCustomerId () );
		// Conteneur Grand rectangle
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setLineWidth ( 0.5 );
		$x1 = self::COORD_X_LEFT_MARGIN;
		if(strlen($this->order->getShippingDescription ()) > 50)
        {
		    $y1 = $this->y - 76;
        }
        else
        {
            $y1 = $this->y - 64;
        }
		$x2 = self::COORD_X_RIGHT_MARGIN;
		$y2 = $this->y - 8;
		$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );
		// Rectangle de titre
		$x1 = self::COORD_X_LEFT_MARGIN + 0.5;
		$y1 = $this->y - 22;
		$x2 = self::COORD_X_RIGHT_MARGIN - 0.5;
		$y2 = $this->y - 8.5;
		$page->setFillColor ( new Zend_Pdf_Color_Html ( '#C30245' ) );
		$page->drawRectangle ( $x1, $y1, $x2, $y2 );
		// Titre
		$this->_setTitleFont ( $page );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'SHIPPING %s', $this->shipment->getIncrementId () ), 33, $this->y - 19, 'UTF-8' );
		// Contenu
		$this->_setBoldContentFont ( $page );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Shipping date : %s', Mage::helper ( 'core' )->formatDate ( $this->shipment->getCreatedAtStoreDate (), 'medium', false ) ), 34, $this->y - 33, 'UTF-8' );
		if (!$this->order->getCustomerIsGuest()) {
			$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Customer %s', $customer->getIncrementId () ), 310, $this->y - 33, 'UTF-8' );
		}
		$this->_setContentFont ( $page );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Order %s', $this->order->getIncrementId () ), 34, $this->y - 45, 'UTF-8' );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Order date : %s', Mage::helper ( 'core' )->formatDate ( $this->order->getCreatedAtStoreDate (), 'medium', false ) ), 34, $this->y - 57, 'UTF-8' );
		if(strlen($this->order->getShippingDescription ()) > 50)
        {
            $page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Shipping mode : %s', substr($this->order->getShippingDescription (),0,50) )."-", 310, $this->y - 45, 'UTF-8' );
            $page->drawText ( substr($this->order->getShippingDescription (),50) , 380, $this->y - 57, 'UTF-8' );
        }
        else
        {
            $page->drawText ( Mage::helper ( 'tatvasales' )->__ ( 'Shipping mode : %s', $this->order->getShippingDescription () ), 310, $this->y - 45, 'UTF-8' );
        }
        $this->_getInfoPayment ( $page );
		$this->y = $this->y - 64;
	}

	public function newPage(array $settings = array()) {
		/* Add new table head */
		$page = $this->_getPdf ()->newPage ( Zend_Pdf_Page::SIZE_A4 );
		$this->_getPdf ()->pages [] = $page;
		$this->y = $page->getHeight ();

		if (! empty ( $settings ['table_header'] )) {
			$this->insertLogo ( $page, $this->shipment->getStore () );
			$this->insertShipmentHeader ( $page );
		}

		if (! empty ( $settings ['item_header'] )) {
			$this->y = $this->y - 8;
			$this->insertHeaderItems ( $page );
		}

		if (! empty ( $settings ['table_bottom'] )) {
			$this->insertAddress ( $page, $this->shipment->getStore () );
		}

		if (! empty ( $settings ['remain_header'] )) {
			$this->y = $this->y - 8;
			$this->insertHeaderReliquat ( $page );
		}

		return $page;
	}

	protected function insertItems(&$page) {

		$this->insertHeaderItems ( $page );
		$heightLine = 27;
		foreach ( $this->shipment->getAllItems () as $item ) {

			if ($item->getOrderItem ()->getParentItem ()) {
				continue;
			}

			if ($this->y < $this->minY) {
				$page = $this->newPage ( array (
						'table_header' => true,
						'item_header' => true,
						'table_bottom' => true ) );
			}

			// Ajout d'une ligne d'item
			$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
			$page->setLineWidth ( 0.5 );
			$page->setLineDashingPattern ( array (
					1,
					1 ) );

			// Ajout de l'item
			$heightLine = $this->addItem ( $page, $item );
			$heightLine+= 10;

			$x1 = self::COORD_X_LEFT_MARGIN;
			$y1 = $this->y - $heightLine;
			$x2 = self::COORD_X_RIGHT_MARGIN;
			$y2 = $this->y;
			$page->setFillColor ( new Zend_Pdf_Color_Html ( '#C30245' ) );
			$page->drawRectangle ( $x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE );

			// Mise à jour de l'axe Y
			$this->y = $this->y - $heightLine;

		}
		$page->setLineDashingPattern ( Zend_Pdf_Page::LINE_DASHING_SOLID );
		$this->y = $this->y - 10;
	}

	protected function insertHeaderItems(&$page) {
		// Entête du tableau
		$page->setLineColor ( new Zend_Pdf_Color_GrayScale ( 0.7 ) );
		$page->setFillColor ( new Zend_Pdf_Color_Html ( '#C30245' ) );
		$page->setLineWidth ( 0.5 );
		$x1 = self::COORD_X_LEFT_MARGIN;
		$y1 = $this->y - 16;
		$x2 = self::COORD_X_RIGHT_MARGIN;
		$y2 = $this->y;
		$page->drawRectangle ( $x1, $y1, $x2, $y2 );
		// Labels
		$y = $this->y - 12;
		$this->_setSmallTitleFont ( $page );
		// Référence
		$string = Mage::helper ( 'tatvasales' )->__ ( 'Ref.' );
		$pos = $this->getAlignCenter ( $string, 34, 110 - 34, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $y, 'UTF-8' );
		// Désignation
		$string = Mage::helper ( 'tatvasales' )->__ ( 'Item name' );
		$pos = $this->getAlignCenter ( $string, 110, 246 - 110, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $y, 'UTF-8' );
		// Options

		$string = Mage::helper ( 'tatvasales' )->__ ( 'Qty' );
		$pos = $this->getAlignCenter ( $string, 456, 489 - 456, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $y, 'UTF-8' );
	   
		$this->y = $this->y - 16;
	}

	protected function addRemainedItem($page, $orderItem, $qty) {
		$baseY = $this->y - 17;
		// Référence
		$this->_setContentFont ( $page );
		$string = $this->getSku ( $orderItem );
		$page->drawText ( $string, 34, $baseY, 'UTF-8' );
		// Désignation
		$this->_setRedContentFont ( $page );
		$string = $orderItem->getName ();
		$page->drawText ( $string, 114, $baseY, 'UTF-8' );
		// Options
		$options = $this->getItemOptions ( $orderItem );
		if ($options) {
			$_height = 7;
			$_y = $this->y - $_height;
			switch (count ( $options )) {
				case 1 :
					$_y = $this->y - $_height * 2.5;
					break;
				case 2 :
					$_y = $this->y - $_height * 2;
					break;
				case 3 :
					$_y = $this->y - $_height * 1.5;
					break;
			}
			$this->_setSmallContentFont ( $page );

			foreach ( $options as $option ) {
				if ($option ['label']) {
					if ($option ['value']) {
						$_printValue = isset ( $option ['print_value'] ) ? $option ['print_value'] : strip_tags ( $option ['value'] );
						$values = (is_array ( $_printValue ) ? explode ( ', ', $_printValue ) : $_printValue);
					}
					$string = $option ['label'] . ($values ? ' : ' . $values : '');
					$pos = $this->getAlignCenter ( $string, 246, 332 - 246, $page->getFont (), $page->getFontSize () );
					$page->drawText ( $string, $pos, $_y, 'UTF-8' );
					$_y -= $_height;
				}
			}
		}
		// Tax
		$this->_setRedContentFont ( $page );
		$string = number_format ( $orderItem->getTaxPercent (), 2 );
		$pos = $this->getAlignCenter ( $string, 332, 385 - 332, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $baseY, 'UTF-8' );
		// Unit price
		$string = Mage::helper ( 'core' )->formatPrice ( $orderItem->getPrice (), false );
		$pos = $this->getAlignCenter ( $string, 385, 456 - 385, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $baseY, 'UTF-8' );
		// qty
		$string = $qty * 1;
		$pos = $this->getAlignCenter ( $string, 456, 489 - 456, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $baseY, 'UTF-8' );
		// total ht
		$string = Mage::helper ( 'core' )->formatPrice ( $orderItem->getPrice () * $qty, false );
		$pos = $this->getAlignCenter ( $string, 489, 561 - 489, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $baseY, 'UTF-8' );

	}

    public function getItemOptions($item) {
		$result = array ();
		if ($options = $item->getProductOptions ()) {
			if (isset ( $options ['options'] )) {
				$result = array_merge ( $result, $options ['options'] );
			}
			if (isset ( $options ['additional_options'] )) {
				$result = array_merge ( $result, $options ['additional_options'] );
			}
			if (isset ( $options ['attributes_info'] )) {
				$result = array_merge ( $result, $options ['attributes_info'] );
			}
		}
		return $result;
	}

    public function getTailCollection($prd_id) {

	  	$storeId = Mage::app()->getStore()->getId();
		$productEntityTypeId = Mage::getModel ( 'eav/entity' )->setType ( 'catalog_product' )->getTypeId ();
		$brandAttribute = Mage::getModel('eav/entity_attribute')->loadByCode($productEntityTypeId,'taille');
		$product = Mage::getModel('catalog/product')->load($prd_id);

	  $brands = Mage::getModel('eav/entity_attribute_option')->getCollection()
					->addFieldToFilter('attribute_id',array('='=>$brandAttribute->getAttributeId()))
					->setIdFilter($product->getTaille())
					->setStoreFilter($storeId, false)
					->load();

			return $brands->getFirstItem();
	}

	public function getMarqueCollection($prd_id) {

	  	$storeId = Mage::app()->getStore()->getId();
		$productEntityTypeId = Mage::getModel ( 'eav/entity' )->setType ( 'catalog_product' )->getTypeId ();
		$brandAttribute = Mage::getModel('eav/entity_attribute')->loadByCode($productEntityTypeId,'marque');
		$product = Mage::getModel('catalog/product')->load($prd_id);
	  $brands = Mage::getModel('eav/entity_attribute_option')->getCollection()
					->addFieldToFilter('attribute_id',array('='=>$brandAttribute->getAttributeId()))
					->setIdFilter($product->getMarque())
					->setStoreFilter($storeId, false)
					->load();

			return $brands->getFirstItem();
	}

   /**
     * Get brand
     */
	public function getBrand($brandId) {
		$storeId = Mage::app()->getStore()->getId();
		$productEntityTypeId = Mage::getModel ( 'eav/entity' )->setType ( 'catalog_product' )->getTypeId ();
		$brandAttribute = Mage::getModel('eav/entity_attribute')->loadByCode($productEntityTypeId,'marque');

		$brands = Mage::getModel('eav/entity_attribute_option')	->getCollection()
																->addFieldToFilter('attribute_id',array('='=>$brandAttribute->getAttributeId()))
																->setIdFilter($brandId)
																->setStoreFilter($storeId, false)
																->load();

		foreach ($brands as $brand)
			return $brand;
	}

	protected function addItem($page, $item) {
		$_height = 6;
		$sep = '';
		//$baseY = $this->y - 17;
		$reference = array();
		$marqueEtCollection = array();

		//Designation
		$name = $item->getName ();
		$name = utf8_decode($name);
		$sizeName = 40;

		if(strlen($name) > $sizeName){
			$tabName = split(" ", $name);
			$result = "";
			foreach($tabName as $value){
				$size = strlen($value) + strlen($result);
				if( $size > $sizeName ){
					$reference[] = $result;
					$result = $value . " ";
				}elseif( $size == $sizeName){
					$result .= $value;
					$reference[] = $result;
					$result = "";
				}else{
					$result .= $value . " ";
				}
			}

			if(strlen($result) > 0){
				$reference[] = $result;
			}
		}else{
			$reference[] = $name;
		}

		//Marque et collection


        /*$string = false;
		$string = utf8_decode($string);
		if($string && strlen($string) > 40){
			$deb = 0;
			$fin = 40;
			$str = substr($string, $deb, $fin);

			while($str){
				$marqueEtCollection[] = $str;

				$deb = $fin + 1;
				$fin = $fin * 2 + 1;
				$str = substr($string, $deb, $fin);
			}
		}else{
			if($string){
				$marqueEtCollection[] = $string;
			}
		}*/
		
		$string = false;
            $prd = Mage::getModel('catalog/product')->load($item->getProductId());

            if($prd['gamme_collection_new'])
            {
            $p = $prd['gamme_collection_new'];
			$attr = $prd->getResource()->getAttribute("gamme_collection_new");
			$colls = $attr->getSource()->getOptionText($p);
               if($string){
					$string .= " - ";
				}
               $string .= $colls;
            }

            if($prd->getManufacturer())
            {
            $p = $prd->getManufacturer();
			$attr = $prd->getResource()->getAttribute("manufacturer");
			$marque = $attr->getSource()->getOptionText($p);
               if($string){
					$string .= " - ";
				}
               $string .= $marque;
            }

		$string = utf8_decode($string);
		if($string && strlen($string) > 40){
			$deb = 0;
			$fin = 40;
			$str = substr($string, $deb, $fin);

			while($str){
				$marqueEtCollection[] = $str;

				$deb = $fin + 1;
				$fin = $fin * 2 + 1;
				$str = substr($string, $deb, $fin);
			}
		}else{
			if($string){
				$marqueEtCollection[] = $string;
			}
		}

		$sizeMarqueEtCollection = sizeof($marqueEtCollection);

		$_heightLine = (10 + (sizeof($reference) + $sizeMarqueEtCollection  ) * 6);
		$baseY = $this->y - ( ($_heightLine + ($_height * ( 1 + sizeof($reference) * 0.6 + $sizeMarqueEtCollection * 0.5) ) ) / 2 ) - 2;
		if(! sizeof($marqueEtCollection)){
			$baseY = $baseY - 1;
			if(sizeof($reference) == 1){
				$baseY = $baseY - 1.5;
			}
		}

		// Référence
		$this->_setContentFont ( $page );
		$string = $this->getSku ( $item );
		$page->drawText ( $string, 34, $baseY, 'UTF-8' );

		// Désignation
		if(! sizeof($marqueEtCollection)){
			if(sizeof($reference) == 1){
				$_y = $this->y - ($_height * ( 1.8 + sizeof($reference) ));
			}else{
				$_y = $this->y - ($_height * ( 1 + sizeof($reference) * 0.6)) - 3;
			}
		}elseif(sizeof($marqueEtCollection) == 1 && sizeof($reference) == 1){
				$_y = $this->y - $_height - 10;
		}else{
			$_y = $this->y - ($_height * ( 1 + sizeof($reference) * 0.6 + $sizeMarqueEtCollection * 0.5));
		}

		foreach($reference as $value){
			$this->_setRedContentFont ( $page );
			$page->drawText ( utf8_encode($value), 114, $_y, 'UTF-8' );
			$_y -= $_height;
		}

		//Marque et collection
		$_y -= 2;
		foreach($marqueEtCollection as $value){
			$this->_setSmallContentFont ( $page );
			$page->drawText ( utf8_encode($value), 114, $_y, 'UTF-8' );
			$_y -= $_height;
		}


		$string = $item->getQty () * 1;
		$pos = $this->getAlignCenter ( $string, 456, 489 - 456, $page->getFont (), $page->getFontSize () );
		$page->drawText ( $string, $pos, $baseY, 'UTF-8' );


		return $_heightLine;

	}

    protected function insertConditions($page, $order)
    {
		//Add tax sentence for PDF invoice
		$store = $order->getStoreId ();
		$taxSentenceConfig = unserialize(Mage::getStoreConfig ('sales/tax_sentences/tax_sentences', $store ));
		if ($order->getShippingAddress())
        {
			$countryId = $order->getShippingAddress()->getCountry();
			$address = $order->getShippingAddress();
		}
        else
        {
			$countryId = $order->getBillingAddress()->getCountry();
			$address = $order->getBillingAddress();
		}

		$area = Mage::getModel('tatvashipping/area')->getCollection()
					->addCountriesToResult()
					->joinCountries(array("'".$countryId."'"))
					->clear()
					->getFirstItem();

		if ($area && is_array($taxSentenceConfig) && $order->getTaxAmount()<=0)
        {
			$sentence = false;

			//Si on a une adresse
			if(is_object($address))
            {
				foreach ($taxSentenceConfig as $elmt)
                {
				    if (in_array($area->getAreaId(),$elmt['areas']))
                    {
					    $sentence = $elmt['sentence'];
						break;
					}
				}
            }
		}
        else
        {
			$sentence = false;
		}

		if($sentence) {
			$this->_setItalicContentFont ( $page );
			$this->y = $this->y - 2;

			$page->drawText ($sentence, self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );
			$this->y = $this->y -20;
		} else {
			$this->y = $this->y - 8;
		}

		$this->_setBoldContentFont ( $page );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( "PAYMENT RULES :" ), self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );
		$this->y = $this->y - 8;
		// Mode de paiement
		$lineHeight = 10;
		$this->_setContentFont ( $page );
		$this->y = $this->y - $lineHeight;
		$payment = $this->_getInfoValues ();
		foreach ( $payment as $value ) {
			if (trim ( $value ) !== '') {
				if(strtoupper ( rtrim ($value) ) == "PARTENAIRE"){
					$value = $this->order->getPayment ()->getMarketplacesPartnerCode();
				}

				$page->drawText ( strip_tags ( strtoupper(trim ( $value )) ), self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );
				$this->y = $this->y - $lineHeight;
			}
		}
		//$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( "Date of payment to date of billing" ), self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );
		$page->drawText ( Mage::helper ( 'tatvasales' )->__ ( "MATURITY AT BILLING DATE" ), self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );

		$this->y = $this->y - $lineHeight;

		// Retard
		$this->_setSmallContentFont($page);
		$lineHeight = 8;
		$store = $this->order->getStore ();
		$rules = Mage::getStoreConfig ( 'sales/identity/payment_rules', $store );
		$rules ? trim($rules) : '';
		if ($rules) {
			$tb = explode ( "\n", $rules );
			foreach ($tb as $itemTb) {
				$this->y = $this->y - $lineHeight;
				$page->drawText ( $itemTb, self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );
			}
		}
		$this->y = $this->y - 5;

		// Conditions
		$this->_setSmallSilverContentFont($page);
		$lineHeight = 8;
		$store = $this->order->getStore ();
		$rules = Mage::getStoreConfig ( 'sales/identity/conditions', $store );
		$rules ? trim($rules) : '';
		if ($rules) {
			$tb = explode ( "\n", $rules );
			foreach ($tb as $itemTb) {
				$this->y = $this->y - $lineHeight;
				$page->drawText ( $itemTb, self::COORD_X_LEFT_MARGIN, $this->y, 'UTF-8' );
			}
		}
		$this->y = $this->y - 5;

	}
	
	public function getPdf_TNT($shipments = array()) {

		$this->_beforeGetPdf ();
		$this->_initRenderer ( 'shipment' );

		$pdf = new Zend_Pdf ( );
		$this->_setPdf ( $pdf );
		$style = new Zend_Pdf_Style ( );
		$this->_setFontBold ( $style, 10 );
        
		$pdfDocs = array();
		foreach ( $shipments as $shipment ) {
			if ($shipment->getStoreId ()) {
				Mage::app ()->getLocale ()->emulate ( $shipment->getStoreId () );
			}


           $path = $_SERVER['DOCUMENT_ROOT'].'/media/pdf_bt/';

           $orderNum = Mage::getModel('sales/order_shipment')->load($shipment->getID())->getOrder()->getRealOrderId();

            

            

            //595:842;
              /*$arr_pdf = explode(":",Zend_Pdf_Page::SIZE_A4);

              $w = round($arr_pdf[0]/29.7);

              $h = round($arr_pdf[1]/21);*/

            $pdf = new Zend_Pdf ( );
		    $this->_setPdf ( $pdf );
		    $style = new Zend_Pdf_Style ( );
	        $this->_setFontBold ( $style, 10 );

            $pdf->pages = "";
			$page = $pdf->newPage (Zend_Pdf_Page::SIZE_A4);

			$pdf->pages[] = $page;


            // $page->rotate(0, 0, M_PI_2/2);
			$order = $shipment->getOrder ();

			$this->shipment = $shipment;
			$order = $shipment->getOrder ();

			$this->shipment = $shipment;
			$this->order = $order;

			// initialisation axe Y
			$this->y = $page->getHeight ();

			/* Add image */
			$this->insertLogo ( $page, $shipment->getStore () );

			/* Add address */
			$this->insertAddress ( $page, $shipment->getStore () );

			/* Entête livraison */
			$this->insertShipmentHeader ( $page );

			if(strlen($this->order->getShippingDescription ()) > 50)
            {
    		    $this->y = $this->y - 22;
            }
            else
            {
                $this->y = $this->y - 8;
            }

			/* Adresse de livraison */
			if (! $this->order->getIsVirtual ()) {
				$this->insertShippingAddress ( $page, $shipment->getStore ());
			}

			/* Adresse de facturation */
			$this->insertBillingAddress ( $page, $shipment->getStore () );

			$this->y = $this->y - 140;

			// Liste des items
			$this->insertItems ( $page );

			// gérer le cas de la page suivante
			if ($this->y < 300) {
				$page = $this->newPage(array (
						'table_header' => false,
						'table_bottom' => false ));
			   //$pdf->pages[] = $pdf->newPage (Zend_Pdf_Page::SIZE_A4);
				$page = $this->newPage ( array (
						'table_header' => true,
						'table_bottom' => true ) );
				$this->y = $this->y - 12;
			}

			// Liste des items restant à livrer
			$this->insertReliquats ( $page );

			// gérer le cas de la page suivante
			if ($this->y < 150) {
				$page = $this->newPage ( array (
						'table_header' => true,
						'table_bottom' => true ) );
				$this->y = $this->y - 12;
			}

			// Conditions de règlement
			$this->insertConditions ($page, $order);



            /*$pdf1 = Zend_Pdf::load('d:/sticker_1.pdf');

            $extractor = new Zend_Pdf_Resource_Extractor();
            $pdfFirstPageFirst = $extractor->clonePage($pdf1->page[0]);
			$pdf->pages[] = $pdfFirstPageFirst;*/


            /*$pdfNew = new Zend_Pdf();
            $pdf1 = Zend_Pdf::load('d:\packingslip.pdf');
            $pdf2  = Zend_Pdf::load('d:\A4.pdf');

            $extractor = new Zend_Pdf_Resource_Extractor();

            //Create clone of first page of first pdf document.
            $pdfFirstPageFirst = $extractor->clonePage($pdf1->page[0]);
            //Add first page of first pdf to new pdf document.
            $pdfNew->pages[] = $pdfFirstPageFirst;

            //Second Pdf
            //Create clone of first page of second pdf document.
            $pdfSecondPageFirst = $extractor->clonePage($pdf2->page[0]);
            //Add first page of second pdf to new pdf document.
            $pdfNew->pages[] = $pdfSecondPageFirst;

            $pdfNew->save('d:/merge.pdf');*/


            /*$pdfTemp = Zend_Pdf::load('d:/sticker_1.pdf');
            $extractor = new Zend_Pdf_Resource_Extractor();
            foreach($pdfTemp->pages as $page1){
                $pdfExtract = $extractor->clonePage($page1);
                $pdf->pages [] = $pdfExtract;
            }*/

            $pdf->save($path.'packingslip_'.$shipment->getId().'.pdf');

            //$pdf_merge->addPDF($path.$orderNum.'.pdf')->addPDF($path.'packingslip_'.$shipment->getId().'.pdf');

            $pdfDocs[] = $path.$orderNum.'.pdf';
			$pdfDocs[] = $path.'packingslip_'.$shipment->getId().'.pdf';  
		}

		$this->_afterGetPdf ();
          
		if ($shipment->getStoreId ()) {
			Mage::app ()->getLocale ()->revert ();
		}
		
		
		$pdfNew = new Zend_Pdf();
		foreach($pdfDocs as $file){
		$pdf = Zend_Pdf::load($file);
		$extractor = new Zend_Pdf_Resource_Extractor();
						foreach($pdf->pages as $page){
						$pdfExtract = $extractor->clonePage($page);
						$pdfNew->pages[] = $pdfExtract;
						}
		}

		$mergePdf = "mergefile.pdf";
		$pdfNew ->save($path.$mergePdf);
        //$pdf_merge->merge('download', 'packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf');

        $fullPath = $path.'mergefile.pdf';

if ($fd = fopen ($fullPath, "r")) {
    $fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);
    switch ($ext) {
        case "pdf":
        header("Content-type: application/pdf"); // add here more headers for diff. extensions
        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
        break;
        default;
        header("Content-type: application/octet-stream");
        header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
    }
    header("Content-length: $fsize");
    header("Cache-control: private"); //use this to open files directly
    while(!feof($fd)) {
        $buffer = fread($fd, 2048);
        echo $buffer;
    }
}
fclose ($fd);
exit;
 


		//return $pdf;
	}
}