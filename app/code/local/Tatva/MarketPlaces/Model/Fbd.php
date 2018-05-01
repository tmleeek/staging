<?php
/**
 * created : 8 oct. 2009
 *
 *
 * updated by <user> : <date>
 * Description of the update
 *
 * @category SQLI
 * @package Tatva_MarketPlaces
 * @author emchaabelasri
 * @copyright SQLI - 2009 - http://www.tatva.com
 *
 * EXIG : REF-005
 * REG  :
 */

/**
 *
 * @package Tatva_MarketPlaces
 */
class Tatva_MarketPlaces_Model_Fbd extends Tatva_MarketPlaces_Model_Abstract
{

	protected $_xmlData ;
	protected $_logFile ;

	protected $_code                   = 'fbd';
	//protected $_pathXmlShippingMethod  = 'tatvamarketplaces_fbd/shipping_methods/mapping';
	protected $_pathXmlEnabled 		   = 'tatvamarketplaces_fbd/get_order/active';
	//protected $_canUseInternal              = true;
    //protected $_canUseCheckout              = false;
    //protected $_canUseForMultishipping      = false;

	const GET_ORDER_URL           	   = 'tatvamarketplaces_fbd/get_order/url';
    const GET_ORDER_FTP_Address        = 'tatvamarketplaces_fbd/get_order/ftp_adress';
	const GET_ORDER_login     		   = 'tatvamarketplaces_fbd/get_order/login';
	const GET_ORDER_password           = 'tatvamarketplaces_fbd/get_order/password';
    const GET_ORDER_port               = 'tatvamarketplaces_fbd/get_order/port';

	const SET_ORDER_URL           	   = 'tatvamarketplaces_fbd/set_order/url';
    const SET_ORDER_FTP_Address        = 'tatvamarketplaces_fbd/set_order/ftp_adress';
	const SET_ORDER_login     		   = 'tatvamarketplaces_fbd/set_order/login';
	const SET_ORDER_password           = 'tatvamarketplaces_fbd/set_order/password';
    const SET_ORDER_port               = 'tatvamarketplaces_fbd/set_order/port';

	const LOG_FILE_NAME                = 'Ventes-Multi-Canal-FBD System';

	const STATUS_CANCELED = "canceled";
	const STATUS_COMPLETE = "complete";

	/**
	 * Initialisation
	 */
	protected function init()
    {
		try
        {
		    $this->_prepareDownloadXml();
		}
        catch(Exception $e)
        {
			throw $e;
		}
	}

	/**
	 * Active / DÃ©sactive
	 */
	protected function isEnabled()
    {
		try
        {
			return Mage::getStoreConfigFlag ( $this->_pathXmlEnabled );
		}
        catch(Exception $e)
        {
			throw $e;
		}
	}
	/**
	 * Check the configuration data
	 */
	protected function _checkConfig()
    {
		if(!$this->getConfigData( self:: GET_ORDER_FTP_Address)||!$this->getConfigData(self:: GET_ORDER_login)||!$this->getConfigData(self:: GET_ORDER_password)||!$this->getConfigData(self:: GET_ORDER_port))
        {
			throw new Exception( "l'url de connexion et/ou le login et/ou le mot de passe, ne sont pas configurÃ©s correctement. Voir: system > configuration > Ventes multi-canal > FBD System ", Zend_Log::ERR );
			return ;
		}
	}

	/**
	 *
	 */
	protected function _execute()
    {

        $this->_checkConfig();

        //$localDir  = $_SERVER['DOCUMENT_ROOT'].'/fbd/';
        $localDir  = '/home/azboutik/public_html/fbd/';

        // download all the files
        $local_files    = scandir($localDir);

        //@mail("sagarshahitprofessional@gmail.com","FBD Import","2.Local file checked");
        if (!empty($local_files))
        {
            foreach ($local_files as $local_file)
            {
                if ($local_file != '.' && $local_file != '..' && $local_file != '_archive')
                {
                    if(@chmod($localDir.$local_file, 0777))
                    {
                        //@mail("sagarshahitprofessional@gmail.com","FBD Import","3.".$localDir.$local_file." permission changed");
                    }

                    $str = file_get_contents($localDir.$local_file);

                    //replace something in the file string - this is a VERY simple example
                    $str=str_replace("##DONE##", "",$str);

                    //write the entire string
                    file_put_contents($localDir.$local_file, $str);

                    //echo $localDir.$local_file;
                    //exit;
                    if (!$xml = simplexml_load_file ($localDir.$local_file))
                    {
                        Mage::log ( 'FBD - Status update at FBD: NO New Order File Found in Local', Zend_log::INFO, $this->getLogFile() );
			            return;
                        //throw new Exception ( "Impossible to load the file $file " );
                        //continue;
			        }

                    $orders = $xml;
                    /*echo "<pre>";
                            echo "--------------------------";
                            print_r($orders);
                            echo "</pre>";
                            exit;*/
                    if(!$orders)
                    {
                        Mage::log ( 'Loading the XML file did not retrieve any information', Zend_log::INFO, $this->getLogFile() );
			            return;
                        //throw new Exception ( "Loading the XML file did not retrieve any information" );
		            }

		            //Lecture des commandes
		            //Mage::log ( 'FBD System - Reading commands: DEBUT >>> ', Zend_log::INFO, $this->getLogFile() );

                    if(!empty($orders))
                    {
                        try
                        {
                            $errorOrder = false;

                            $OrderFbdId = $orders->header->order_fbd_id[0];

                    		Mage::log('DEBUT Treatment Order No. =  '.$OrderFbdId[0].' >>> ', Zend_log::INFO, $this->getLogFile());

                            //@mail("sagarshahitprofessional@gmail.com","FBD Import","3.".$localDir.$local_file."Local file import started");
                            //exit;
                            //- Prepare une nouvelle commande
                    		$this->_prepareNewOrder();
                    		$this->_currentOrder->setMarketplacesPartnerCode($this->_code);
                    		$this->_currentOrder->setMarketplacesOrderSended('N');



                            foreach($orders->header as $general)
                            {
                                //Initialisation des totaux
                    		    $totalTTC = (float)$general->total_amount[0];
                            }

                            foreach($orders->shipment as $shipment)
                            {

                             /*echo "<pre>";
                            echo "--------------------------";
                            print_r($shipment);
                            echo "</pre>";*/
                            //exit;
                                $shipment_code = $shipment->code[0];
                                if($shipment_code == "owsh1_az_boutique_collisimo")
                                {
                                    $shipping_method = "Socolissimo";
                                }
                                else if($shipment_code == "owsh1_az_boutique_tnt")
                                {
                                    $shipping_method = "TNT Express";
                                }
                                else
                                {
                                    $shipping_method = "Socolissimo";
                                }

                                //echo $shipping_method;
                                //- Methode de livraison

                    		    //$this->addShippingMethod(utf8_encode((string)$shipment->code[0]));
                                $this->addShippingMethod(utf8_encode((string)$shipping_method),$totalTTC);
                                //@mail("sagarshahitprofessional@gmail.com","FBD Import","4.Shipment added");
                                $shippingAmount = (float)$shipment->amount[0];
                                $totalTTC = $totalTTC +  $shippingAmount;
                            }

                            foreach($orders->address->billing as $billing)
                            {
                                //- Adresse de facturation
                                $country = $billing->country[0];
                                $societe = $billing->company[0];
                                $email = $orders->header->customer_email[0];
                                $prefix = "";

                                $this->addCustomer(
                                (string) $prefix,
                    			(string) $email,
                                (string) $billing->lastname[0],
                                (string) $billing->firstname[0]
                    		    );

                        		$billingAddress = $this->addBillingAddress(
                                    $prefix,
                                    $email,
                                    (string) $billing->lastname[0],
                                    (string) $billing->firstname[0],
                                    array( $billing->address1[0],
                        			       $billing->address2[0]),
                        			(string)$billing->postal_code[0],
                        			(string)$billing->city[0],
                        			(string)$country,
                        			(string)$billing->phone[0],
                                    $societe
                        		);
                            }

                            //@mail("sagarshahitprofessional@gmail.com","FBD Import","5.Billing added");
                             /*echo "<pre>";
                            echo "--------------------------";
                            print_r($this);
                            echo "</pre>";
                            exit;*/
                            foreach($orders->address->shipping as $shipping)
                            {
                                //- Adresse de livraison
                        		$country = $shipping->country[0];
                                $societe = $shipping->company[0];
                                $email = $orders->header->customer_email[0];
                                $prefix = "";
                        		$shippingAddress = $this->addShippingAddress(
                                    $prefix,
                        			(string)$email,
                                    (string) $shipping->lastname[0],
                                    (string) $shipping->firstname[0],
                                    array( $shipping->address1[0],
                        			       $shipping->address2[0]),
                        			(string)$shipping->postal_code[0],
                        			(string)$shipping->city[0],
                        			(string)$country,
                        			(string)$shipping->phone[0],
                                    $societe
                        		);
                            }

                            //@mail("sagarshahitprofessional@gmail.com","FBD Import","6.Shipping added");
                    		$totalTaxAmount = 0;
                    		$subTotal = 0;

                    		//Ajout des produits
                    		foreach($orders->items->item as $item)
                            {

                              $qty                 = 0;
                              $taxPercent          = 0;
                              $priceHT             = 0;
                              $totalLigneTTC       = 0;
                              $totalLigneTaxAmount = 0;
                              $product_id = 0;


                              $market_place_orderline_id = $item->id[0];

                              $_sku = $item->supplier_sku[0];

                              $product = Mage::getModel('catalog/product');

                              $productId = $product->getIdBySku($_sku);

                              if($productId)
                              {
                                $product->load($productId);
                              }

                              $product_id = $product->getEntityId();

                  			  if($product_id ==0)
                  			  {
                  			  	   Mage::log ( 'The product does not exist >> '.$OrderFbdId[0] , Zend_log::INFO, $this->getLogFile() );
                  					$this->addError( Mage::helper('tatvamarketplaces')->__("Commande " . (string)$orders->header->order_fbd_id[0]  . " : The product " . $product_id . " does not exist " ) );
                  					$this->_orderError = true;
                  					$errorOrder = true;
                  					break;
                  			  }

                              /*echo "<pre>";
                              print_r($item);
                              echo "</pre>";
                              exit;*/
                  			    //$product = Mage::getModel('catalog/product')->load( $product_id );

                    			$qty                 = (float)$item->qty[0];
                    			$totalLigneTTC       = (float)$item->amount->price[0] * 1;

                    			//$taxPercent = $this->getTaxPercent($shippingAddress,$billingAddress,$product);
                                $taxPercent = 0;

                    			if(!$taxPercent  )
                                {
                    				$taxPercent = 20;
                    			}

                                $totalLigneTaxAmount = ($totalLigneTTC - ($totalLigneTTC / (1 + ($taxPercent / 100)))) * $qty;
                                //$totalLigneTaxAmount = $item->amount->tax[0];

                                //echo $totalLigneTTC."<br />";
                                //echo $taxPercent."<br />";
                                $priceHT = $totalLigneTTC / ( 1 + ($taxPercent / 100) );

                    			$subTotal = $subTotal + $priceHT * $qty;
                    			$totalTaxAmount += $totalLigneTaxAmount;
                    			$this->addItem((int)$item->id[0], $product, $qty, round($taxPercent,2), $priceHT, $totalLigneTTC, round($totalLigneTaxAmount,2));

                    			Mage::log ( 'FIN Product Processing >> '.$OrderFbdId[0] , Zend_log::INFO, $this->getLogFile() );
                    		}

                    		if(!$errorOrder)
                            {
                    			//Frais de port
                    			$shippingTaxAmount = round($shippingAmount - ($shippingAmount / (1 + ($taxPercent / 100))),2);
                    			$shippingAmount -= 	$shippingTaxAmount;
                    			$totalTaxAmount += $shippingTaxAmount;
                    			$this->addShippingAmount($shippingAmount,$shippingTaxAmount,$taxPercent);

                    			//Totaux
                    			$totalTaxAmount = round($totalTaxAmount,2);
                    			$subTotal = round($subTotal,2);
                    			$this->addTotals($totalTTC,$subTotal, $totalTaxAmount,$taxPercent);

                                $order_date = str_replace("-","/",$orders->header->created_at[0]);

                                /*echo "<pre>";
                            echo "--------------------------";
                            print_r($this->_currentOrder->getData());
                            echo "</pre>";
                            exit;*/
                    			//Donnees partenaire
                    			$this->addPartnerValues((string)$orders->header->order_fbd_id[0],$order_date);

                    			//Sauvegarde la commande en cours
                    			$this->_saveOrder('fbd');

                                //@mail("sagarshahitprofessional@gmail.com","FBD Import","7.Order saved");
                                if($order_date != "")
                          		{
                          		    $partnerDate = explode(" ",$order_date);
                          	        $date = explode("/",date($partnerDate[0]));
                          		    $partner_date = date('Y-m-d h:i:s',strtotime($date[2].'-'.$date[1].'-'.$date[0].' '.$partnerDate[1]));
                                }

                                $order_collection = Mage::getModel('sales/order')->getCollection()
                                                    ->join(
                                                        array('payment' => 'sales/order_payment'),
                                                        'main_table.entity_id=payment.parent_id',
                                                        array()
                                                    );

                                $order_collection->addFieldToFilter('marketplaces_partner_order', array(array('eq' => $orders->header->order_fbd_id[0])))
                                                ->addFieldToFilter('marketplaces_partner_date', array(array('eq' => $partner_date)));

                                //echo $order_collection->getSelect();
                                //exit;
                                foreach($order_collection as $order_data)
                                {


                                    if ($order_data->getState() == Mage_Sales_Model_Order::STATE_NEW)
                                    {
                                        try
                                        {
                                            if(!$order_data->canInvoice())
                                            {
                                                $order_data->addStatusHistoryComment('Inchoo_Invoicer: Order cannot be invoiced.', false);
                                                $order_data->save();
                                            }

                                            //START Handle Invoice
                                            $invoice = Mage::getModel('sales/service_order', $order_data)->prepareInvoice();

                                            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                                            $invoice->register();

                                            $invoice->getOrder()->setCustomerNoteNotify(false);
                                            $invoice->getOrder()->setIsInProcess(true);
                                            $order_data->addStatusHistoryComment('Automatically Invoice With FBD.', false);

                                            $transactionSave = Mage::getModel('core/resource_transaction')
                                                ->addObject($invoice)
                                                ->addObject($invoice->getOrder());

                                            $transactionSave->save();
                                            //END Handle Invoice

                                            $store = $invoice->getStore ();

                                            //@mail("sagarshahitprofessional@gmail.com","FBD Import","8.invoice saved");
                                            // Création du répertoire
				                            $root = Mage::getStoreConfig ( 'sales/pdf/path_invoice', $store->getId () );
                                            $subdir = $this->createDir ( $root, 'invoice' );
				                            $dir = $root . $subdir;
				                            if(!$subdir)
					                            return;

                                            // Création du PDF
				                            if ($invoice->getStoreId ())
                                            {
					                            Mage::app ()->setCurrentStore ( $invoice->getStoreId () );
				                            }

                                            $filename = '';
				                            if ($invoice->getOrder ()->getCustomerIsGuest ())
                                            {
					                            $filename = "Guest-O{$invoice->getOrder()->getIncrementId()}-I{$invoice->getIncrementId()}-Invoice.pdf";
				                            }
                                            else
                                            {
					                            $customer = Mage::getModel ( 'customer/customer' )->load ( $invoice->getOrder ()->getCustomerId () );
					                            $filename = "C{$customer->getIncrementId()}-O{$invoice->getOrder()->getIncrementId()}-I{$invoice->getIncrementId()}-Invoice.pdf";
				                            }

                                            /*echo $filename;
                                            exit;*/
                                            $pdf = Mage::getModel ( 'attachpdf/sales_order_pdf_invoice' )->getPdf(array($invoice));
				                            $pdf->save ( "$dir/$filename" );

                                            // enregistrement du nom de fichier
				                            $invoice->setPdfFile("$subdir/$filename");

                                            if($filename!='')
                                            {
                                                $write = Mage::getSingleton("core/resource")->getConnection("core_write");

                                                if($invoice->getIncrementId())
                                                {
                                                    $collection_add_sql="UPDATE `sales_flat_invoice` SET `pdf_file` = '".$subdir."/".$filename."' WHERE `increment_id` =".$invoice->getIncrementId();
                                                    $write->query($collection_add_sql);
                                                }
                                            }
                                        rename($localDir.$local_file, $localDir."_archive/".$local_file);

                                        }
                                        catch (Exception $e)
                                        {
                                            //print_r($e->getMessage());
                                            //exit;
                                            Mage::log ( 'Exception occurred during automaticallyInvoiceShipCompleteOrder action. Exception message:  '.$e->getMessage().' >>> ', Zend_log::INFO, $this->getLogFile() );
                                            $order_data->addStatusHistoryComment('Exception occurred during automaticallyInvoiceShipCompleteOrder action. Exception message: '.$e->getMessage(), false);
                                            $order_data->save();
                                        }
                                    }
                                }


                                //echo $order_collection->getSelect();
                                //exit;

                    			Mage::log ( '{SUCCES} Treatment Order No. =  '.(int)$orders->header->order_fbd_id[0].' >>> ', Zend_log::INFO, $this->getLogFile() );
                    		}
                            else
                            {
                    			Mage::log ( '{ECHEC} Treatment Order No. =  '.(int)$orders->header->order_fbd_id[0].' >>> ', Zend_log::INFO, $this->getLogFile() );
                    		}
                            //exit;
                        }
                        catch(Exception $e)
                        {
            				Mage::logException($e);
                            echo $e->getMessage();
            				//$this->addError( Mage::helper('tatvamarketplaces')->__("Commande " . (string)$order->attributes()->morid  . " : " . $e->getMessage() ) );
            				//$this->_orderError = true;
            			}
                    }
                    /*echo $localDir.$local_file;
                    echo "<br />";
                    echo $localDir."_archive/".$local_file;*/
                    //unlink($localDir.$local_file);

                    //rename($localDir.$local_file, $localDir."_archive/".$local_file);
                    //@mail("sagarshahitprofessional@gmail.com","FBD Import","7.Order saved");
                    Mage::log ('FBD System - Reading Commands: END >>> ', Zend_log::INFO, $this->getLogFile());
                }
            }
        }
	}

    protected function createDir($root, $type)
    {
	    if (! $root || ! is_dir ( $root ))
        {
			Mage::log ( "storage path  \"$type\" is incorrectly configured", Zend_Log::ERR );
			return false;
		}
		$subdir = date ( "Y/m/d" );
		$dir = $root . $subdir;
		if (! is_dir ( $dir ))
        {
			if (! mkdir ( $dir, 0755, true ))
            {
				Mage::log ( "Error creating new directory \"$type\"", Zend_Log::ERR );
				return false;
			}
		}
		return $subdir;
	}

	/**
	 * Charge le fichier
	 */
	protected function _prepareDownloadXml()
    {

        //$url   = $this->getConfigData( self:: GET_ORDER_URL );
		$ftp_address   = $this->getConfigData( self:: GET_ORDER_FTP_Address );
        //echo "<br />";
		$login = $this->getConfigData( self:: GET_ORDER_login );
        //echo "<br />";
		$pass  = $this->getConfigData( self:: GET_ORDER_password );
        //echo "<br />";
        $port   = $this->getConfigData( self:: GET_ORDER_port );
        //echo "<br />";
        //exit;
		try
        {
			//$file = $this->getXmlFileName();
            //$resConnection = ssh2_connect($ftp_address, $port);

            //if (!function_exists("ssh2_connect"))
            //{
                //Mage::log ('Function ssh2_connect not found, you cannot use ssh2 here', Zend_log::INFO, $this->getLogFile() );
			    //return;
                //die('Function ssh2_connect not found, you cannot use ssh2 here');
            //}


            if (!$resConnection = ssh2_connect($ftp_address, $port))
            {
                Mage::log ('Unable to connect', Zend_log::INFO, $this->getLogFile() );
			    return;
                //die('Unable to connect');
            }


            if (!ssh2_auth_password($resConnection, $login, $pass))
            {
                 Mage::log ('Unable to authenticate', Zend_log::INFO, $this->getLogFile() );
			    return;
                //die('Unable to authenticate.');
            }


            if (!$resSFTP = ssh2_sftp($resConnection))
            {
                 Mage::log ('Unable to create a stream', Zend_log::INFO, $this->getLogFile() );
			     return;
                 //die('Unable to create a stream.');
            }

            //@mail("sagarshahitprofessional@gmail.com","FBD","1.Download file called");
            //$localDir  = $_SERVER['DOCUMENT_ROOT'].'/fbd/';
            $localDir  = '/home/azboutik/public_html/fbd/';
            //Mage::log ('create local file in: '.$localDir, Zend_log::INFO, $this->getLogFile() );
            $remoteDir = '/from_fbd/order_create';
            $archiveDir = '/from_fbd/order_create/_archive';
            // download all the files
            $files    = scandir('ssh2.sftp://'.$resSFTP.$remoteDir);

            if (!empty($files))
            {
                foreach ($files as $file)
                {
                    if ($file != '.' && $file != '..' && $file != '_archive')
                    {

                        //echo "<br /><br />"."Copying file: ssh2.sftp://{$resSFTP}{$remoteDir}/{$file}"."<br /><br />";
                        if (!$remote = @fopen("ssh2.sftp://{$resSFTP}{$remoteDir}/{$file}", 'r'))
                        {
                            Mage::log ('Unable to open remote file:  '.$file, Zend_log::INFO, $this->getLogFile() );
			                //return;
                            //echo "Unable to open remote file: $file\n";
                            continue;
                        }

                        if (!$local = @fopen($localDir.$file, 'w'))
                        {
                            Mage::log ('Unable to create local file: '.$localDir.$file, Zend_log::INFO, $this->getLogFile() );
			                //return;
                            //echo "Unable to create local file: $file\n"."<br /><br />";
                            continue;
                        }

                        $read = 0;
                        $filesize = filesize("ssh2.sftp://{$resSFTP}{$remoteDir}/{$file}");

                        while ($read < $filesize && ($buffer = fread($remote, $filesize - $read)))
                        {
                            $read += strlen($buffer);
                            if (fwrite($local, $buffer) === FALSE)
                            {
                                Mage::log ('Unable to write to local file: '.$file, Zend_log::INFO, $this->getLogFile() );
                                //echo "Unable to write to local file: $file\n"."<br /><br />";
                                break;
                            }
                        }

                        //echo $org_remote_source = 'ssh2.sftp://'.$resSFTP.$remoteDir.'/'.$file;
                        //echo "<br />";
                        //echo $archive_remote_source = 'ssh2.sftp://'.$resSFTP.$archiveDir.'/'.$file;
                        $org_remote_source = $remoteDir.'/'.$file;

                        $archive_remote_source = $archiveDir.'/'.$file;
                        ssh2_sftp_rename($resSFTP, $org_remote_source, $archive_remote_source);
                        //unlink($org_remote_source);
                        //echo "done";

                        fclose($local);
                        fclose($remote);
                    }
                }
            }
            else
            {

            }
		}
        catch(Exception $e)
        {
            echo 'hey1234564657=='.$e->getMessage();exit;
			throw $e;
		}
	}

	/**
	 * return log file name
	 * @return string
	 */
	public function getLogFile ()
    {
		if( ! $this->_logFile )
        {
			$this->_logFile = date('Ymd').'-'.self::LOG_FILE_NAME.'.log';
		}
		return $this->_logFile;
	}

	/**
	 * get market place code
	 * @see app1/code/local/Tatva/MarketPlaces/Model/Tatva_MarketPlaces_Model_Abstract#getCode()
	 */
	public function getCode()
    {
		return $this->_code;
	}

	/**
	 * return xml file name
	 * @return string
	 */
	/*public function getXmlFileName()
    {
        ///data/devazb/public_html/tatvascript/fbd/
		return "/data/devazb/public_html/tatvascript/fbd/az-boutique_order_000000035_20161005_121002.xml";
	}*/

    /**
     * Ajoute une ligne Ã  la commande
     * @param Mage_Catalog_Model_Product $product
     * @param int $qty
     * @param $taxPercent
     * @param $priceHT
     * @param $rowTotalTTC
     */
    protected function addItem($market_place_orderline_id, $product, $qty, $taxPercent, $priceHT, $rowTotalTTC, $taxAmount, $ref=NULL)
    {
    	$orderItem = Mage::getModel('sales/order_item');

    	$orderItem->setData('product', $product)
	            ->setProductId($product->getId())
	            ->setProductType($product->getTypeId())
	            ->setSku($product->getSku())
	            ->setName($product->getName())
	            ->setWeight($product->getWeight())
	            ->setQty($qty)
	            ->setQtyOrdered($qty)

	            ->setPrice($priceHT)
	            ->setOriginalPrice($priceHT)

	            ->setBasePrice($priceHT)
	            ->setBaseOriginalPrice($priceHT)
	            ->setTaxAmount($taxAmount)
	            ->setBaseTaxAmount($taxAmount)

	            ->setBaseTaxBeforeDiscount($taxAmount)
	            ->setTaxBeforeDiscount($taxAmount)

	            ->setDiscountAmount(0)
	            ->setBaseDiscountAmount(0)
	            ->setRowTotal($priceHT * $qty)
	            ->setBaseRowTotal($priceHT * $qty)
	            ->setRowTotalWithDiscount($priceHT * $qty)
	            ->setRowWeight($qty * $product->getWeight())
	            ->setTaxPercent($taxPercent)
				->setAappliedRuleIds(1)
	            ->setIsVirtual($product->getIsVirtual())
                  ->setMarketplacesOrderItemId($market_place_orderline_id);


                if( isset( $ref ) )
                {
	                $orderItem->setItemPixmaniaId( $ref );
	            }
                /*echo "<pre>";
                print_r($orderItem->getData());
                exit;*/
	    		$this->_currentOrder->addItem($orderItem);
    }

    function xml2array ( $xmlObject, $out = array () )
    {
        foreach ( (array) $xmlObject as $index => $node )
        {
            $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;
        }
        return $out;
    }

    protected function addShippingMethod($shippingMethod,$totalTTC)
    {
    	$code = "";

		$shipping = array('Socolissimo' => 'socolissimo','TNT Express' => 'tnt');

        //echo $shipping[$shippingMethod];
        /*if(isset($shipping[$shippingMethod]))
		{
		  $code = $shipping[$shippingMethod];
		}
		else
		{
			throw new Exception ( Mage::helper('tatvamarketplaces')->__("The modes of transport " . $shippingMethod . " Is not configured" ));
		}*/


        //echo $shippingMethod." - sagar";
        if($shippingMethod == "Socolissimo")
        {
            if($totalTTC > '99')
            {
                $shipping = "socolissimo_domicile_sign_fr";
            }
            else
            {
                $shipping = "socolissimo_domicile_fr";
            }



    	    $this->_currentOrder->setShippingMethod($shipping);
       	    $this->_currentOrder->setShippingDescription("Colissimo");
        }
        elseif($shippingMethod == "TNT Express")
        {
            $this->_currentOrder->setShippingMethod('tnt');
       	    $this->_currentOrder->setShippingDescription("TNT Expresss");
        }

    }

}