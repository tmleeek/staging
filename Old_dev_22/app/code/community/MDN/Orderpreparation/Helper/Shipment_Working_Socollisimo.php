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
class MDN_Orderpreparation_Helper_Shipment extends Mage_Core_Helper_Abstract
{
    public function getConfigData($field)
	{
        $path = 'carriers/tnt/'.$field;
        return Mage::getStoreConfig($path, Mage::app()->getStore());
	}

	public function getConfigData_mondial($field)
	{
        $path = 'carriers/pointsrelais/'.$field;
        return Mage::getStoreConfig($path, Mage::app()->getStore());
	}

	public function dateFR()
    {
		$dateFR = date('Y-m-d');
        /*$tmp = explode('-',$dateUS);
        $dateFR = $tmp[2].'-'.$tmp[1].'-'.$tmp[0];*/
        return $dateFR;
	}

    /*
     * Create shipment
     *
     */

    public function CreateShipment(&$order, $warehouseId = null, $operatorId = null,$weight,$telephone)
    {
        try
        {
            //$shipment_new = $order->getShipmentsCollection()->getFirstItem();
            /*echo "<pre>";
            print_r($shipment_new->getData());
            exit;*/

            $convertor = Mage::getModel('sales/convert_order');
            $shipment = $convertor->toShipment($order);

			$_order = $shipment->getOrder();

            Mage::dispatchEvent('orderpreparartion_before_create_shipment', array('order' => $order));

            //browse order items
            $items = $this->GetItemsToShipAsArray($order->getid(), $warehouseId, $operatorId);


            foreach ($order->getAllItems() as $orderItem)
            {
                //skip les cas sp�ciaux
                if (!$orderItem->isDummy(true) && !$orderItem->getQtyToShip())
                {
                    continue;
                }

                if ($orderItem->getIsVirtual())
                {
                    continue;
                }

                //add product to shipment
                if (isset($items[$orderItem->getitem_id()]))
                {
                    $ShipmentItem = $convertor->itemToShipmentItem($orderItem);
                    $ShipmentItem->setQty($items[$orderItem->getitem_id()]);
                    $shipment->addItem($ShipmentItem);
                }
            }

            //save shipmeent
            /* shipment flag added by bhargav  */

            $cuurent_time=''; $ship_time_by_erp='';
            $cuurent_time=date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));

            $planning = Mage::getModel('SalesOrderPlanning/Planning')->load($shipment['order_id'] , 'psop_order_id');

            $ship_time_by_erp= $planning['psop_shipping_date'];

            $ekomi_store_id = $order->getStoreId();

            $ekomi_enable_check = Mage::getStoreConfig('ekomi/ekomiconf/enable_review_email',$ekomi_store_id);

            if($ekomi_enable_check != 0)
            {
                if(strtotime($cuurent_time) <= strtotime($ship_time_by_erp))
                {
                    $shipment->setData('ekomi_flag',1);
                }
            }
            /* ended by bhargav  */


            $shipment->register();

            // add by nisha - add shipment track etc..which will be useful for create label -- start//
            $_shippingMethod = explode("_",$_order->getShippingMethod());

            /*echo "<pre>";
            print_r($shipment->getData());
            echo "</pre>";
            exit;*/

			//Expédition via TNT on créé une expé. et on récupère le tracking num via le WS.
            if ($_shippingMethod[0] == 'tnt')
            {
                //echo "adadad";
                //exit;
                // On met en place les paramètres de la requète pour l'expédition
                $send_city = $this->getConfigData('ville');

                $rec_typeid = '';
                $rec_name = '';

                if($_shippingMethod['1'] == "A" || $_shippingMethod['1'] == "T" || $_shippingMethod['1'] == "M" || $_shippingMethod['1'] == "J")
                {
                    $rec_type = 'ENTERPRISE';
                    $rec_name = trim($_order->getShippingAddress()->getCompany());
                }
                elseif($_shippingMethod['1'] == "AZ" || $_shippingMethod['1'] == "TZ" || $_shippingMethod['1'] == "MZ" || $_shippingMethod['1'] == "JZ")
                {
                    $rec_type = 'INDIVIDUAL';
                }
                else
                {
                    $rec_type = 'DROPOFFPOINT';
                    $extt = explode(' ',trim($_order->getShippingAddress()->getCompany()));
                    $rec_typeid = end($extt);
                    $rec_name = str_replace($rec_typeid, '', $_order->getShippingAddress()->getCompany());
                }

                $rec_address1 = $_order->getShippingAddress()->getStreet(1);
                $rec_address2 = $_order->getShippingAddress()->getStreet(2);

                if ( $rec_address2 == '' )
                {
                    if( strlen($rec_address1) > 32 )
                    {
                        $rec_address2 = substr($rec_address1,32,64);
                    }
                }

                $nb_colis = 1;
                $date_expe = $this->dateFR();

                $parcelsRequest = array();

                //$poids_restant = $_order->getWeight();
                $poids_restant = $weight;
                for($i=1;$i<=$nb_colis;$i++)
                {
                    if($i == $nb_colis)
                    {
                        if( $poids_restant > $this->getConfigData('max_package_weight') )
                        {
                    	    $parcelWeight = $this->getConfigData('max_package_weight');
                    	}
                        else
                        {
                    	    $parcelWeight = $poids_restant;
                    	}
                    }
                    else
                    {
                        $parcelWeight = $this->getConfigData('max_package_weight');
                    	$poids_restant = $poids_restant - $parcelWeight;
                    }

                    if($parcelWeight < '0.1')
                    {
                        $parcelWeight = '0.1';
                    }

                    $parcelsRequest[] = array('sequenceNumber'=>$i,'customerReference' => $_order->getRealOrderId(), 'weight' => $parcelWeight);
                }

                $rec_city = $_order->getShippingAddress()->getCity();

                $instructions = '';
                $phoneNumber = '';
                $accessCode = '';
                $floorNumber = '';
                $buildingId = '';

                $info_comp = explode('&&&', $_order->getShippingAddress()->getTntInfosComp());

                if( count($info_comp) > 0 )
                {
                    if( count($info_comp) == 1 )
                    {
                        $instructions = substr($info_comp[0], 0, 60);
                    }
                    else
                    {
                        $phoneNumber = $info_comp[0];
                    	$accessCode = $info_comp[1];
                    	$floorNumber = $info_comp[2];
                    	$buildingId = $info_comp[3];
                    }
                }

                if($phoneNumber == '')
                {
                    $phoneNumber = $_order->getShippingAddress()->getTelephone();
                }

                $phoneNumber = str_replace(' ', '', $phoneNumber);
                if( preg_match('/^0033/', $phoneNumber) )
                {
                    $phoneNumber = substr_replace($phoneNumber, '0', 0, 4);
                }

                $phoneNumber = str_replace('+33', '0', $phoneNumber);
                $phoneNumber = str_replace('(+33)', '0', $phoneNumber);
                $phoneNumber = str_replace('-', '', $phoneNumber);
                $phoneNumber = str_replace('.', '', $phoneNumber);
                $phoneNumber = str_replace(',', '', $phoneNumber);
                $phoneNumber = str_replace('/', '', $phoneNumber);

                $sender = array('zipCode' => $this->getConfigData('code_postal'), 'city' => $send_city);
                $receiver = array('zipCode' => $_order->getShippingAddress()->getPostcode(), 'city' => $rec_city, 'type' => $rec_type);
                $feasi_params = array('shippingDate' => $date_expe, 'accountNumber' => $this->getConfigData('account'), 'sender' => $sender, 'receiver' => $receiver );
                $feasi_result = Mage::getModel('tnt/shipping_carrier_tnt')->_tnt_feasibility( $feasi_params );

                if( is_string($feasi_result) )
                {
                    Throw new Exception( $feasi_result );
                }

                //correction du bug Paypal qui concatene nom/prenom et vide le nom de l'adresse de facturation !!
                if(trim($_order->getShippingAddress()->getLastname()) == '' && trim($_order->getShippingAddress()->getFirstname()) != '' )
                {
                    $nom = '';
                    $prenom = $_order->getShippingAddress()->getFirstname();
                    $tab_nom = explode(" ", $prenom);

                    for( $i=0;$i<count($tab_nom);$i++ )
                    {
                        if( $i == 0 )
                        {
                    	    $prenom = substr($tab_nom[$i],0,12);
                    	}
                        else
                        {
                    	    $nom.= $tab_nom[$i]." ";
                    	}
                    }

                    $nom = trim($nom);
                    $nom = substr($nom,0,19);

                }
                else
                {
                    $nom = substr($_order->getShippingAddress()->getLastname(),0,19);
                    $prenom = substr($_order->getShippingAddress()->getFirstname(),0,12);
                }

                $params = array('parameters' => array('shippingDate' => $date_expe,
                                                    'accountNumber' => $this->getConfigData('account'),
                    								'sender' => array(
                                                                'name' => substr($this->getConfigData('raison_sociale'),0,32),
						                    					'address1'   => substr($this->getConfigData('adresse'),0,32),
						                    					'address2'   => substr($this->getConfigData('adresse2'),0,32),
						                    					'zipCode'    => substr($this->getConfigData('code_postal'),0,5),
						                    					'city'       => substr($send_city,0,27)
						                    					),

													'receiver' => array(
                                                                'type' => $rec_type,
						                    					'typeId' => $rec_typeid,
																'name' => substr($rec_name,0,32),
						                    					'address1' => substr($rec_address1,0,32),
						                    					'address2' => substr($rec_address2,0,32),
						                    					'zipCode' => substr($_order->getShippingAddress()->getPostcode(),0,5),
						                    					'city' => substr($rec_city,0,27),
						                    					'instructions' => $instructions,
						                    					'contactLastName' => $nom,
						                    					'contactFirstName' => $prenom,
						                    					'emailAddress' => substr($_order->getCustomerEmail(),0,80),
						                    					'phoneNumber' => substr($phoneNumber,0,10),
						                    					'accessCode' => substr($accessCode,0,7),
						                    					'floorNumber' => substr($floorNumber,0,2),
						                    					'buldingId' => substr($buildingId,0,3)
						                    					),

						                            'serviceCode' => $_shippingMethod[1],
						                            'quantity' => $nb_colis,
						                            'parcelsRequest' => $parcelsRequest,
						                    		'labelFormat'	=> $this->getConfigData('label_format')
						                    		));

				$parcels = Mage::getModel('tnt/shipping_carrier_tnt')->_tnt_exp_crea($params);

                if(is_string($parcels))
                {
                    echo 'hey==';print_r($this->__($parcels));exit;
                    Throw new Exception( $this->__($parcels) );
                }

                //on créé le fichier PDF
                $path = Mage::getBaseDir('media').'/pdf_bt/';
                $filename = $_order->getRealOrderId().".pdf";

                if($parcels['pdfLabels'] && !file_exists($path.$filename))
                {
                    if($handle = fopen($path.$filename, 'x+'))
                    {
	                    fwrite($handle, $parcels['pdfLabels']);
	                    fclose($handle);
                    }
                    else
                    {
                        Throw new Exception( $this->__("Impossible de créer le BT. Vérifiez que le repertoire /media/pdf_bt/ à les droits en écriture.") );
                    }
                }

                foreach($parcels as $parcel)
                {
                    if(is_array($parcel))
                    {
		                $track = Mage::getModel('sales/order_shipment_track')
		                        ->setNumber($parcel['parcelNumber'])
		                        ->setCarrier('TNT')
		                        ->setCarrierCode($_shippingMethod[0])
		                        ->setTitle('TNT')
		                        ->setPopup(1);
		                $shipment->addTrack($track);
                    }
                }
            }

            if ($_shippingMethod[0] == 'pointsrelais')
            {
                // On met en place les paramÃ¨tres de la requÃ¨te
                $adress = $_order->getShippingAddress()->getStreet();

                //echo '<pre>';print_r($_order->getShippingAddress()->getData());exit;
                if (!isset($adress[1]))
                {
                    $adress[1] = '';
                }

                $package_weightTmp = $weight*1000;

			    if($this->getConfigData_mondial('package_weight'))
                {
			        $package_weightTmp = $package_weightTmp+($this->getConfigData_mondial('package_weight'));
			    }

                if($package_weightTmp < 100)
                {
                    $package_weightTmp = 100;
                }

                $cust_shipping_mobile = $_order->getShippingAddress()->getMobilephone();
                $cust_shipping_mobile = trim($cust_shipping_mobile);
                if(!empty($cust_shipping_mobile))
                {
                    $telephone = $cust_shipping_mobile;
                }
                else
                {
                    $telephone = $_order->getShippingAddress()->getTelephone();
                }
                //echo $telephone;die();
                /*echo '<pre>';
                print_r($_order->getShippingAddress()->getData());exit;*/

                $params = array(
                        'Enseigne'       => $this->getConfigData_mondial('enseigne'),
                        'ModeCol'        => 'CCC',
                        'ModeLiv'        => '24R',
                        'NDossier'       => $_order->getRealOrderId(),
                        'Expe_Langage'   => 'FR',
                        'Expe_Ad1'       => trim($this->removeaccents($this->getConfigData_mondial('adresse1_enseigne'))),
                        'Expe_Ad3'       => trim($this->removeaccents($this->getConfigData_mondial('adresse3_enseigne'))),
                        'Expe_Ad4'       => trim($this->removeaccents($this->getConfigData_mondial('adresse4_enseigne'))),
                        'Expe_Ville'     => trim($this->removeaccents($this->getConfigData_mondial('ville_enseigne'))),
                        'Expe_CP'        => $this->getConfigData_mondial('cp_enseigne'),
						'Expe_Pays'      => trim($this->removeaccents($this->getConfigData_mondial('pays_enseigne'))),
                        'Expe_Tel1'      => '0448060270',
                        'Expe_Tel2'      => '',
                        'Expe_Mail'      => $_order->getCustomerEmail(),
                        'Dest_Langage'   => 'FR',
                        'Dest_Ad1'       => trim($this->removeaccents($_order->getShippingAddress()->getFirstname() . ' ' . $_order->getShippingAddress()->getLastname())),
                        'Dest_Ad2'       => trim($this->removeaccents($_order->getShippingAddress()->getCompagny())),
                        'Dest_Ad3'       => trim($this->removeaccents($adress[0])),
                        'Dest_Ad4'       => trim($this->removeaccents($adress[1])),
                        'Dest_Ville'     => trim($this->removeaccents($_order->getShippingAddress()->getCity())),
                        'Dest_CP'        => $_order->getShippingAddress()->getPostcode(),
                        'Dest_Pays'      => trim($this->removeaccents($_order->getShippingAddress()->getCountryId())),
                        'Dest_Tel1'      => $telephone,
                        'Dest_Tel2'      => '',
                        'Dest_Mail'      => $_order->getCustomerEmail(),
                        'Poids'          => round($package_weightTmp),
                        'NbColis'        => '1',
                        'CRT_Valeur'     => '0',
                        'LIV_Rel_Pays'   => $_order->getShippingAddress()->getCountryId(),
                        'LIV_Rel'        => $_order->getRelayId()
                    );

                //$_order->getWeight()*1000,
                //On crÃ©e le code de sÃ©curitÃ©

                $select = "";
                foreach($params as $key => $value)
                {
				    $select .= "\t".'<option value="'.$key.'">' . $value.'</option>'."\r\n";
				}

                Mage::Log('WSI2_CreationExpeditionResult$params : '.($select));

                $code = implode("",$params);
                $code .= $this->getConfigData_mondial('cle');

                //On le rajoute aux paramÃ¨tres
                $params["Security"] = strtoupper(md5($code));

                // On se connecte
                $client = new SoapClient("http://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL");

                // Et on effectue la requÃ¨te
                $expedition = $client->WSI2_CreationExpedition($params)->WSI2_CreationExpeditionResult;

                Mage::Log('WSI2_CreationExpeditionResult : '.($expedition->STAT));
                $track = Mage::getModel('sales/order_shipment_track')
                        ->setNumber($expedition->ExpeditionNum)
                        ->setCarrier('Mondial Relay')
                        ->setCarrierCode($_shippingMethod[0])
                        ->setTitle('Mondial Relay')
                        ->setPopup(1);
                $shipment->addTrack($track);
            }

			//GLS
            if($_shippingMethod[0] == 'gls')
			{
			    //echo "addad12313";
                //exit;
			    $shipfromID = 1;

				$shipfrom = $this->_initShipfrom($shipfromID);
				//	$weight = $_order->getWeight();
				$weight = $_order->getWeight();
				$notiz = '';
		        $extra['frankatur'] = '';
		        $extra['expressart'] = '';
		        $extra['alternativzustellung'] = '';
		        $extra['paketsum'] = '';
		        $extra['paketnumber'] = '';
		        $extra['notiz'] = '';


				$response_gls = $this->_initGlsService('business',$shipment,$weight,$shipfrom,$notiz,$extra);
				//echo 'tes=='.print_r($response_gls);exit;

                if (is_String($response_gls))
                {
		            $response = array(
		                        'error'     => true,
		                        'message'   => $response_gls,
		                        );
		        }
                else
                {
		            $this->_insertTracking($shipment,$response_gls,$notiz);
		        }
			}
            //for gls - end

			// add by nisha - add shipment track etc..which will be useful for create label -- end//
            //echo "345";exit;
            $shipment->getOrder()->setIsInProcess(true);
            //echo "78979";exit;
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($shipment)
                ->addObject($shipment->getOrder())
                ->save();

            //echo "utyutu";exit;
            //echo $_shippingMethod[0];
            //echo "<br />";
            if($_shippingMethod[0] == 'socolissimo')
            {
                //echo "123";exit;
                //$shipment_data = Mage::getModel('sales/order_shipment')->load($shipment->getEntityId());
                $shipment_data = Mage::getModel('sales/order_shipment')->load($shipment->getEntityId());
                if(empty($shipment_data))
                {
                    $shipment_data = Mage::getModel('sales/order_shipment')->load($shipment->getId());
                }
                if(!empty($shipment_data))
                {
                    $itemsCollection = $shipment_data->getItemsCollection();
                    $new_shipping_data = array();
                    $new_shipping_data['packages']['1']['params']['container'] = '';
                    $new_shipping_data['packages']['1']['params']['weight'] = '';
                    $new_shipping_data['packages']['1']['params']['customs_value'] = '';
                    $new_shipping_data['packages']['1']['params']['length'] = '';
                    $new_shipping_data['packages']['1']['params']['width'] = '';
                    $new_shipping_data['packages']['1']['params']['height'] = '';
                    $new_shipping_data['packages']['1']['params']['weight_units'] = 'POUND';
                    $new_shipping_data['packages']['1']['params']['dimension_units'] = 'INCH';
                    $new_shipping_data['packages']['1']['params']['content_type'] = '';
                    $new_shipping_data['packages']['1']['params']['content_type_other'] = '';


                    foreach($itemsCollection as $item)
                    {
                        $entity_id = $item->getEntityId();
                        $entity_qty = $item->getQty();
                        $entity_customvalue = $item->getCustomsValue();
                        $entity_price = $item->getPrice();
                        $entity_name = $item->getName();
                        $entity_weight = $item->getWeight()*$item->getQty();
                        $entity_productid = $item->getProductId();
                        $entity_orderitemid = $item->getOrderItemId();

                        $new_shipping_data['packages']['1']['params']['weight'] +=  $entity_weight;
                        $new_shipping_data['packages']['1']['params']['customs_value'] += $entity_customvalue;

                        $new_shipping_data['packages']['1']['items'][$entity_id]['qty'] = $entity_qty;
                        $new_shipping_data['packages']['1']['items'][$entity_id]['customs_value'] = $entity_customvalue;
                        $new_shipping_data['packages']['1']['items'][$entity_id]['price'] = $entity_price;
                        $new_shipping_data['packages']['1']['items'][$entity_id]['name'] = $entity_name;
                        $new_shipping_data['packages']['1']['items'][$entity_id]['weight'] = $entity_weight;
                        $new_shipping_data['packages']['1']['items'][$entity_id]['product_id'] = $entity_productid;
                        $new_shipping_data['packages']['1']['items'][$entity_id]['order_item_id'] = $entity_orderitemid;
                    }
                }


                $shipment->setPackages($new_shipping_data['packages']);
                /*echo "<pre>";
                print_r("SocolissimoLabel");
                echo "</pre>";*/
                //exit;
                $shipment = $this->SocolissimoLabel($shipment);
                /*echo "<pre>";
                print_r($shipment->getData());
                echo "</pre>";*/

                //$shipment->getOrder()->setIsInProcess(true);

                $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($shipment)
                ->addObject($shipment->getOrder())
                ->save();
                //exit;

            }
            /*echo "completes";
            echo "<br />";*/
            //save shipment id in order_to_prepare item
            $this->StoreShipmentId($order->getid(), $shipment->getincrement_id(), $warehouseId, $operatorId);

            /*echo "StoreShipmentId";
            echo "<br />";*/

            return $shipment;

            /*echo "dispatchEvent";
            echo "<br />";*/

            Mage::dispatchEvent('orderpreparartion_after_create_shipment', array('order' => $order, 'shipment' => $shipment));
        }
        catch (Exception $ex)
        {
            Mage::logException($ex);
            throw new Exception('Error while creating Shipment for Order ' . $order->getincrement_id() . ': ' . $ex->getMessage());
        }

        return null;
    }

    /*
     * Get generate socilissimo label
     *
    */

    public function SocolissimoLabel($final_shipment)
    {

        $response = Mage::getModel('shipping/shipping')->requestToShipment($final_shipment);
        /*echo "<pre>";
        print_r("sagar shah");
        echo "</pre>";*/
        $carrier = $final_shipment->getOrder()->getShippingCarrier();

        if ($response->hasErrors())
        {
            Mage::throwException($response->getErrors());
        }
        if (!$response->hasInfo())
        {
            return false;
        }

        $labelsContent = array();
        $trackingNumbers = array();
        $info = $response->getInfo();

        foreach ($info as $inf)
        {
            if (!empty($inf['tracking_number']) && !empty($inf['label_content']))
            {
                $labelsContent[] = $inf['label_content'];
                $trackingNumbers[] = $inf['tracking_number'];
            }
        }
        /*echo "<pre>";
        echo "_combineLabelsPdf";
        echo "</pre>";*/

        $outputPdf = $this->_combineLabelsPdf($labelsContent);
         /*echo "<pre>";
        echo "setShippingLabel";
        echo "</pre>";*/

        $final_shipment->setShippingLabel($outputPdf->render());
        $carrierCode = $carrier->getCarrierCode();
        $carrierTitle = Mage::getStoreConfig('carriers/'.$carrierCode.'/title', $final_shipment->getStoreId());

       /* print_r($carrierCode);
        echo "<br/>";
        print_r($carrierTitle);
        echo "<br/>";*/


        if ($trackingNumbers)
        {
            foreach ($trackingNumbers as $trackingNumber)
            {
                $track = Mage::getModel('sales/order_shipment_track')
                        ->setNumber($trackingNumber)
                        ->setCarrierCode($carrierCode)
                        ->setTitle($carrierTitle);
                $final_shipment->addTrack($track);
            }
        }


       // $final_shipment->save();

        /*echo "<pre>";
        var_dump("trackingNumbers");
        echo "</pre>";*/

        return $final_shipment;
    }

    /**
     * Combine array of labels as instance PDF
     *
     * @param array $labelsContent
     * @return Zend_Pdf
     */
    protected function _combineLabelsPdf(array $labelsContent)
    {
        $outputPdf = new Zend_Pdf();
        foreach ($labelsContent as $content) {
            if (stripos($content, '%PDF-') !== false) {
                $pdfLabel = Zend_Pdf::parse($content);
                foreach ($pdfLabel->pages as $page) {
                    $outputPdf->pages[] = clone $page;
                }
            } else {
                $page = $this->_createPdfPageFromImageString($content);
                if ($page) {
                    $outputPdf->pages[] = $page;
                }
            }
        }
        return $outputPdf;
    }

    /**
     * Create Zend_Pdf_Page instance with image from $imageString. Supports JPEG, PNG, GIF, WBMP, and GD2 formats.
     *
     * @param string $imageString
     * @return Zend_Pdf_Page|bool
     */
    protected function _createPdfPageFromImageString($imageString)
    {
        $image = imagecreatefromstring($imageString);
        if (!$image) {
            return false;
        }

        $xSize = imagesx($image);
        $ySize = imagesy($image);
        $page = new Zend_Pdf_Page($xSize, $ySize);

        imageinterlace($image, 0);
        $tmpFileName = sys_get_temp_dir() . DS . 'shipping_labels_'
                     . uniqid(mt_rand()) . time() . '.png';
        imagepng($image, $tmpFileName);
        $pdfImage = Zend_Pdf_Image::imageWithPath($tmpFileName);
        $page->drawImage($pdfImage, 0, 0, $xSize, $ySize);
        unlink($tmpFileName);
        return $page;
    }

    /*
     * Get items to ship for order id
     *
     * @param unknown_type $OrderId
    */

    public function GetItemsToShipAsArray($OrderId, $warehouseId = null, $operatorId = null)
    {
        $collection = Mage::getModel('Orderpreparation/ordertoprepareitem')
                ->getCollection()
                ->addFieldToFilter('order_id', $OrderId);

        if ($warehouseId)
            $collection->addFieldToFilter('preparation_warehouse', $warehouseId);

        if ($operatorId)
            $collection->addFieldToFilter('user', $operatorId);

        $retour = array();

        foreach ($collection as $item)
        {
            $retour[$item->getorder_item_id()] = $item->getqty();
        }

        /*echo "<pre>";
        print_r($retour);
        exit;*/
        return $retour;
    }

    /*
     * Store shipment id in ordertoprepare model
     *
     * @param unknown_type $OrderId
    */

    public function StoreShipmentId($OrderId, $ShipmentId, $warehouseId = null, $operatorId = null)
    {
        $collection = mage::getModel('Orderpreparation/ordertoprepare')
                ->getCollection()
                ->addFieldToFilter('order_id', $OrderId);

        if ($warehouseId)
            $collection->addFieldToFilter('preparation_warehouse', $warehouseId);

        if ($operatorId)
            $collection->addFieldToFilter('user', $operatorId);

        $orderToPrepare = $collection->getFirstItem();
        $orderToPrepare->setshipment_id($ShipmentId)->save();
    }

    /*
     * Check if shipment is created for one order
     *
    */

    public function ShipmentCreatedForOrder($OrderId, $warehouseId = null, $operatorId = null) {
        $collection = mage::getModel('Orderpreparation/ordertoprepare')
                ->getCollection()
                ->addFieldToFilter('order_id', $OrderId);
        if ($warehouseId)
            $collection->addFieldToFilter('preparation_warehouse', $warehouseId);
        if ($operatorId)
            $collection->addFieldToFilter('user', $operatorId);

        $orderToPrepare = $collection->getFirstItem();

        if ($orderToPrepare->getshipment_id() != 0)
            return true;
        else
            return false;
    }

	private function _initShipfrom($id)
    {
        $client = false;
        if ($id) {
            $client = Mage::getModel('glsbox/client')->getCollection()->addFieldToFilter('status', '1')->addFieldToFilter('id', $id)->getFirstItem();
        }
        return $client;
    }

	protected function _initGlsService($service,$shipment,$weight,$shipfrom,$notiz,$extra){
        $submit = Mage::getModel('glsbox/unibox_parser');
        $gls = $submit->create($service,$shipment,$weight,$shipfrom,$notiz,$extra);
        return $gls;
    }

	protected function _insertTracking($shipment,$gls_return,$notiz)
    {
        if (Mage::helper('glsbox')->getAutoInsertTracking() == true)
        {
            try
            {
                $paketnummer = $gls_return->getItemsByColumnValue('tag', '8916');
                $paketnummer = $paketnummer[0]->getValue();

				$paketnummer_1 = $gls_return->getItemsByColumnValue('tag', '8913');
				$paketnummer_1 = $paketnummer_1[0]->getValue();

                if($notiz == "")
                {
                    $notiz = 'GLS';
                }

                $arrTracking = array(
                    'carrier_code' => 'gls',
                    'title' => 'GLS',
                    'number' => $paketnummer_1
                );

                $shipment->save();

                $track = Mage::getModel('sales/order_shipment_track')->addData($arrTracking);
                $shipment->addTrack($track)->save();

				$gls_data = Mage::getModel('glsbox/shipment')->getCollection()->getLastItem();

				$gls_model = Mage::getModel('glsbox/shipment')->load($gls_data->getId());
				$gls_model->setShipmentId($shipment->getId());
				$gls_model->save();
				//echo $gls_data->getId();exit;

            }
            catch (Exception $e)
            {
                echo $e->getMessage();exit;
                Mage::throwException($this->__('Es gibt Probleme mit der Unibox-Kommunikation.'));
            }
        }
    }

	function removeaccents($string)
    {
	    $stringToReturn = str_replace(
	        array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','','Î','', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', '','/','\xa8'),
	        array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y',' ','e'), $string);
	        // Remove all remaining other unknown characters

            $stringToReturn = preg_replace('/[^a-zA-Z0-9\-]/', ' ', $stringToReturn);
	        $stringToReturn = preg_replace('/^[\-]+/', '', $stringToReturn);
	        $stringToReturn = preg_replace('/[\-]+$/', '', $stringToReturn);
	        $stringToReturn = preg_replace('/[\-]{2,}/', ' ', $stringToReturn);
	        return $stringToReturn;
    }
}